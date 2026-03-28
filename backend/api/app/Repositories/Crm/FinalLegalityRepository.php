<?php

namespace App\Repositories\Crm;

use App\Models\FinalLegality;
use App\Models\Lead;
use App\Models\LeadPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinalLegalityRepository
{

    // groupedByStatus
    public function groupedByStatus()
    {
        $data = FinalLegality::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()->toArray();
        return [
            'error' => false,
            'code' => 200,
            'result' => $data
        ];
    }

    public function index($request)
    {
        $page = $request['page'] ?? 1;
        $perPage = $request['per_page'] ?? 10;
        $search = $request['search'] ?? null;
        $status = $request['status'] ?? null;
        $sortKey = $request['sortKey'] ?? 'duration';
        $sortDir = $request['sortDir'] ?? 'asc';

        $data = FinalLegality::join('leads', 'final_legalities.lead_id', '=', 'leads.id')
            ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->join('reservations', 'leads.id', '=', 'reservations.lead_id')
            ->when($search, function ($query) use ($search) {
                $query->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.email', 'ilike', '%' . $search . '%')
                    ->orWhere('contacts.phone', 'ilike', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('final_legalities.status', $status);
            })
            ->when($sortKey == "name", function ($query) use ($sortKey, $sortDir) {
                $query->orderByRaw('LOWER(contacts.name) ' . $sortDir);
            })
            ->when($sortKey == "duration", function ($query) use ($sortKey, $sortDir) {
                $query->orderBy('final_legalities.created_at', $sortDir == 'asc' ? 'desc' : 'asc');
            })
            ->select(
                'final_legalities.id',
                'final_legalities.lead_id',
                'contacts.name as name',
                'contacts.phone as phone',
                'final_legalities.status',
                'final_legalities.notes',
                'reservations.property_unit_id',
                'final_legalities.created_at',
            )
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }
    public function create($request)
    {
        // cek apakah lead sudah ada final legality
        $old = FinalLegality::where('lead_id', $request['lead_id'])->first();
        if ($old) {
            return [
                'error' => 'lead sudah membuat legalitas ahir',
                'result' => null,
                'code' => 400
            ];
        }

        // cek apakah payment sudah selesai
        $lead = Lead::with('payment')->join('lead_payments', 'leads.id', '=', 'lead_payments.lead_id')
            ->where('leads.id', $request['lead_id'])
            ->whereIn('lead_payments.status', ['akad_kredit', 'cash'])
            ->first();
        
        if (!$lead) {
            return [
                'error' => 'lead belum kpr / cash',
                'result' => null,
                'code' => 400
            ];
        }

        $bastDocument = $request['bast_document'] ?? null;
        $bastHanoverPhoto = $request['bast_hanover_photo'] ?? null;
        $bastDate = $request['bast_date'] ?? null;
        $retentionDocument = $request['retention_document'] ?? null;
        $retentionHanoverPhoto = $request['retention_hanover_photo'] ?? null;
        $retentionStartDate = $request['retention_start_date'] ?? null;
        $notes = $request['notes'] ?? null;

        $retensiMonth = config('setting.retention_periode_month');
        try {
            DB::beginTransaction();

            $bastFilename = null;
            if ($bastDocument) {
                $bastFilename = uploadFile('crm/lead_legalitas_akhir/bast_file', $bastDocument);
                if ($bastFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $bastHanoverPhotoFilename = null;
            if ($bastHanoverPhoto) {
                $bastHanoverPhotoFilename = uploadFile('crm/lead_legalitas_akhir/bast_hanover_photo', $bastHanoverPhoto);
                if ($bastHanoverPhotoFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $retentionDocumentFilename = null;
            if ($retentionDocument) {
                $retentionDocumentFilename = uploadFile('crm/lead_legalitas_akhir/retention_document', $retentionDocument);
                if ($retentionDocumentFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => "Failed to upload file",
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $retentionHanoverPhotoFilename = null;
            if ($retentionHanoverPhoto) {
                $retentionHanoverPhotoFilename = uploadFile('crm/lead_legalitas_akhir/retention_hanover_photo', $retentionHanoverPhoto);
                if ($retentionHanoverPhotoFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => "Failed to upload file",
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $status = 'pending';
            if ($bastFilename && $bastHanoverPhotoFilename && $bastDate) {
                $status = 'bast';
                if ($retentionDocumentFilename && $retentionHanoverPhotoFilename && $retentionStartDate) {
                    $status = 'retention';
                }
            }

            
            $finalLegality = FinalLegality::create([
                'lead_id' => $request['lead_id'],
                'status' => $status,
                'bast_document' => $bastFilename,
                'bast_hanover_photo' => $bastHanoverPhotoFilename,
                'bast_date' => $bastDate,
                'retention_document' => $retentionDocumentFilename,
                'retention_hanover_photo' => $retentionHanoverPhotoFilename,
                'retention_start_date' => $retentionStartDate,
                'retention_end_date' => $retentionStartDate ? Carbon::parse($retentionStartDate)->addMonths($retensiMonth) : null,
                'notes' => $request['notes'] ?? null,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => $finalLegality,
            'code' => 201
        ];
    }

    // getById
    public function getById($id)
    {
        $finalLegality = FinalLegality::where('final_legalities.id', $id)
            ->join('leads', 'final_legalities.lead_id', '=', 'leads.id')
            ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->select(
                'final_legalities.id',
                'lead_id',
                'contacts.name as name',
                'contacts.phone as phone',
                'contacts.email as email',
                'final_legalities.status',
                'bast_document',
                'bast_hanover_photo',
                'bast_date',
                'retention_document',
                'retention_hanover_photo',
                'retention_start_date',
                'retention_end_date',
                'final_legalities.notes',
            )
            ->first();
        if (!$finalLegality) {
            return [
                'error' => 'final legality not found',
                'result' => null,
                'code' => 404
            ];
        }

        return [
            'error' => null,
            'result' => $finalLegality,
            'code' => 200
        ];
    }

    // update
    public function update($request, $id)
    {
        // cek apakah lead sudah ada final legality
        $old = FinalLegality::where('id', $id)->first();
        if (!$old) {
            return [
                'error' => 'final legality not found',
                'result' => null,
                'code' => 404
            ];
        }

        $bastDocument = $request['bast_document'] ?? null;
        $bastHanoverPhoto = $request['bast_hanover_photo'] ?? null;
        $bastDate = $request['bast_date'] ?? $old->bast_date;
        $retentionDocument = $request['retention_document'] ?? null;
        $retentionHanoverPhoto = $request['retention_hanover_photo'] ?? null;
        $retentionStartDate = $request['retention_start_date'] ?? $old->retention_start_date;
        $notes = $request['notes'] ?? null;

        $retensiMonth = config('setting.retention_periode_month');
        try {
            DB::beginTransaction();

            $bastFilename = $old->bast_document;
            if ($bastDocument) {
                $bastFilename = uploadFile('crm/lead_legalitas_akhir/bast_file', $bastDocument);
                if ($bastFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $bastHanoverPhotoFilename = $old->bast_hanover_photo;
            if ($bastHanoverPhoto) {
                $bastHanoverPhotoFilename = uploadFile('crm/lead_legalitas_akhir/bast_hanover_photo', $bastHanoverPhoto);
                if ($bastHanoverPhotoFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $retentionDocumentFilename = $old->retention_document;
            if ($retentionDocument) {
                $retentionDocumentFilename = uploadFile('crm/lead_legalitas_akhir/retention_document', $retentionDocument);
                if ($retentionDocumentFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => "Failed to upload file",
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $retentionHanoverPhotoFilename = $old->retention_hanover_photo;
            if ($retentionHanoverPhoto) {
                $retentionHanoverPhotoFilename = uploadFile('crm/lead_legalitas_akhir/retention_hanover_photo', $retentionHanoverPhoto);
                if ($retentionHanoverPhotoFilename === false) {
                    DB::rollBack();
                    return [
                        'error' => "Failed to upload file",
                        'result' => null,
                        'code' => 500
                    ];
                }
            }

            $status = 'pending';
            if ($bastFilename && $bastHanoverPhotoFilename && $bastDate) {
                $status = 'bast';
                if ($retentionDocumentFilename && $retentionHanoverPhotoFilename && $retentionStartDate) {
                    $status = 'retention';
                }
            }

            
            $finalLegality = FinalLegality::where('id', $id)->update([
                'status' => $status,
                'bast_document' => $bastFilename,
                'bast_hanover_photo' => $bastHanoverPhotoFilename,
                'bast_date' => $bastDate,
                'retention_document' => $retentionDocumentFilename,
                'retention_hanover_photo' => $retentionHanoverPhotoFilename,
                'retention_start_date' => $retentionStartDate,
                'retention_end_date' => $retentionStartDate ? Carbon::parse($retentionStartDate)->addMonths($retensiMonth) : null,
                'notes' => $notes
            ]);

            if ($bastDocument) {
                if ($old->bast_document) {
                    $path = explode('api/file/', $old->bast_document);
                    deleteFile($path[1]);
                }
            }

            if ($bastHanoverPhoto) {
                if ($old->bast_hanover_photo) {
                    $path = explode('api/file/', $old->bast_hanover_photo);
                    deleteFile($path[1]);
                }
            }

            if ($retentionDocument) {
                if ($old->retention_document) {
                    $path = explode('api/file/', $old->retention_document);
                    deleteFile($path[1]);
                }
            }

            if ($retentionHanoverPhoto) {
                if ($old->retention_hanover_photo) {
                    $path = explode('api/file/', $old->retention_hanover_photo);
                    deleteFile($path[1]);
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => $finalLegality,
            'code' => 201
        ];
    }

    // updateFinishedRetention
    public function updateFinishedRetention()
    {
        try {
            DB::beginTransaction();
            FinalLegality::where('status', 'retention')
                ->whereDate('retention_end_date', '<=', Carbon::now())
                ->update([
                    'status' => 'complete'
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    // getLeadCompletedPayment
    public function getLeadCompletedPayment()
    {
        $finalLegality = LeadPayment::join('leads', 'leads.id', '=', 'lead_payments.lead_id')
            ->join('contacts', 'leads.contact_id', '=', 'contacts.id')
            ->whereIn('lead_payments.status', ['akad_kredit', 'cash'])
            ->select(
                'leads.id',
                'lead_payments.id as payment_id',
                'contacts.name as name',
            )
            ->get();

        return [
            'error' => null,
            'result' => $finalLegality,
            'code' => 200
        ];
    }
}