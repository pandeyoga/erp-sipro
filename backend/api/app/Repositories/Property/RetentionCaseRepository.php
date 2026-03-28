<?php
        
namespace App\Repositories\Property;

use App\Models\Construction;
use App\Models\FinalLegality;
use App\Models\PropertyQC;
use App\Models\Reservation;
use App\Models\RetentionCase;
use App\Models\SubContractor;
use App\Models\Unit;
use App\Models\UnitProperty;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class RetentionCaseRepository
{
    public function index($request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;
        $subContractorId = $request->sub_contractor_id ?? null;
        $sortKey = $request->sortKey ?? 'name';
        $sortDir = $request->sortDir ?? 'asc';
        $status = $request->status ?? null;

        $search = $request->search ?? null;
        $units = RetentionCase::join('unit_properties', 'unit_properties.id', '=', 'retention_cases.property_id')
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('sub_contractors', 'sub_contractors.id', '=', 'retention_cases.sub_contractor_id')
            ->join('reservations', 'reservations.property_unit_id', '=', 'unit_properties.id')
            ->join('leads', 'leads.id', '=', 'reservations.lead_id')
            ->join('contacts', 'contacts.id', '=', 'leads.contact_id')
            ->whereIn('reservations.status', ['pending', 'confirmed'])
            ->when($subContractorId, function ($query) use ($subContractorId) {
                return $query->where('constructions.sub_contractor_id', $subContractorId);
            })
            ->when($sortKey == 'name', function ($query) use ($sortDir) {
                $query->orderBy('contacts.name', $sortDir);
            })
            ->when($sortKey == 'duration', function ($query) use ($sortDir) {
                $query->orderBy(DB::raw("DATE_PART('day', NOW()::timestamp - retention_cases.opened_at::timestamp)"), $sortDir);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('retention_cases.status', $status);
            })
            ->select(
                'retention_cases.id',
                'retention_cases.description',
                'retention_cases.notes',
                'contacts.name as lead_name',
                'retention_cases.status',
                'retention_cases.opened_at',
                'retention_cases.estimated_resolved_at',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'sub_contractors.name as sub_contractor_name',
                'sub_contractors.id as sub_contractor_id',
                'unit_properties.unit_number as unit_number',
                DB::raw("DATE_PART('day', NOW()::timestamp - retention_cases.opened_at::timestamp) AS duration")
            )
            ->when($search, function ($query) use ($search) {
                return $query->where('contacts.name', 'ilike', '%' . $search . '%')
                    ->orWhere('projects.name', 'ilike', '%' . $search . '%')
                    ->orWhere('clusters.name', 'ilike', '%' . $search . '%')
                    ->orWhere('units.type', 'ilike', '%' . $search . '%')
                    ->orWhere('sub_contractors.name', 'ilike', '%' . $search . '%')
                    ->orWhere('unit_properties.unit_number', 'ilike', '%' . $search . '%');
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'error' => null,
            'result' => $units,
            'code' => 200
        ];
    }
    
    public function summary()
    {
        $data = RetentionCase::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
        return [
            'error' => false,
            'code' => 200,
            'result' => $data
        ];
    }

    public function getReservedLead()
    {
        $qcUnit = PropertyQC::pluck('property_id')->toArray();

        $reservasi = Reservation::query()
            ->whereIn('reservations.status', ['pending', 'confirmed'])
            ->join('leads', 'leads.id', '=', 'reservations.lead_id')
            ->join('contacts', 'contacts.id', '=', 'leads.contact_id')
            ->join('constructions', 'constructions.unit_property_id', '=', 'reservations.property_unit_id')
            ->join('unit_properties', 'unit_properties.id', '=', 'reservations.property_unit_id')
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->where('constructions.status', 'done')
            ->select([
                'leads.id as lead_id',
                'unit_properties.id as unit_property_id',
                'contacts.name as contact_name',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'unit_properties.unit_number as unit_number',
                'unit_properties.price as unit_price',
            ])
            ->get()
            ->map(function ($item) use ($qcUnit) {
                return [
                    'lead_id'        => $item->lead_id,
                    'unit_property_id' => $item->unit_property_id,
                    'contact_name'   => $item->contact_name,
                    // 'contact_name'   => $item->contact_name . "- unit ". $item->unit_number . (!in_array($item->unit_property_id, $qcUnit) ? ' (Belum QC)' : ''),
                    'project_name'   => $item->project_name,
                    'cluster_name'   => $item->cluster_name,
                    'unit_type'      => $item->unit_type,
                    'unit_number'    => ($item->unit_number ?? '-') . (!in_array($item->unit_property_id, $qcUnit) ? ' (Belum QC)' : ''),
                    'unit_price'     => $item->unit_price ?? 0,
                ];
            });

        return [
            'error' => null,
            'result' => $reservasi,
            'code' => 200
        ];
    }

    public function getAvailableSubCon()
    {
        $data = SubContractor::select('id', 'name')->get();

        return [
            'error' => null,
            'result' => $data,
            'code' => 200
        ];
    }

    public function store($data)
    {
        $reservasi = Reservation::where('lead_id', $data['lead_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if (!$reservasi) {
            return [
                'error' => 'Reservation not found',
                'result' => null,
                'code' => 400
            ];
        }

        $propertyUnit = UnitProperty::where('id', $reservasi->property_unit_id)->first();
        if (!$propertyUnit) {
            return [
                'error' => 'Property unit not found',
                'result' => null,
                'code' => 404
            ];
        }

        // cek apakah unit sudah ada construction
        $construction = Construction::where('unit_property_id', $propertyUnit->id)->first();
        if (!$construction || $construction->status !== 'done') {
            return [
                'error' => 'Construction not yet done',
                'result' => null,
                'code' => 400
            ];
        }

        $subContractor = SubContractor::where('id', $data['sub_contractor_id'])->first();
        if (!$subContractor) {
            return [
                'error' => 'Sub contractor not found',
                'result' => null,
                'code' => 404
            ];
        }

        // cek apakah retensi nya masih ada
        $retensi = FinalLegality::where('lead_id', $data['lead_id'])->first();
        if (!$retensi) {
            return [
                'error' => 'Legalitas Akhir Belum dibuat',
                'result' => null,
                'code' => 404
            ];
        }

        if ($retensi->retention_end_date < Carbon::now()) {
            return [
                'error' => 'Retensi Sudah Terlewat',
                'result' => null,
                'code' => 400
            ];
        }

        try {
            DB::beginTransaction();

            $casePicturePaths = [];
            if ($data['case_pictures']) {
                foreach ($data['case_pictures'] as $casePicture) {
                    $filename = uploadFile('property/retention-case/bukti-case', $casePicture);
                    if ($filename === false) {
                        DB::rollBack();
                        return [
                            'error' => 'Failed to upload file',
                            'result' => null,
                            'code' => 500
                        ];
                    }
                    $casePicturePaths[] = $filename;
                }
            }

            $data = [
                'property_id' => $propertyUnit->id,
                'opened_at' => $data['case_date'],
                'estimated_resolved_at' => Carbon::parse($data['case_date'])->addDays((int) $data['estimated_resolved_day']),
                'description' => $data['description'],
                'case_pictures' => $casePicturePaths,
                'sub_contractor_id' => $data['sub_contractor_id'],
                'notes' => $data['notes'],
            ];

            $create = RetentionCase::create($data);

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

    public function getById($id)
    {
        $retensi = RetentionCase::join('unit_properties', 'unit_properties.id', '=', 'retention_cases.property_id')
            ->where('retention_cases.id', $id)
            ->join('units', 'units.id', '=', 'unit_properties.unit_type_id')
            ->join('projects', 'projects.id', '=', 'unit_properties.project_id')
            ->join('clusters', 'clusters.id', '=', 'unit_properties.cluster_id')
            ->join('sub_contractors', 'sub_contractors.id', '=', 'retention_cases.sub_contractor_id')
            ->join('reservations', 'reservations.property_unit_id', '=', 'unit_properties.id')
            ->join('leads', 'leads.id', '=', 'reservations.lead_id')
            ->join('contacts', 'contacts.id', '=', 'leads.contact_id')
            ->whereIn('reservations.status', ['pending', 'confirmed'])
            ->select(
                'retention_cases.id',
                'retention_cases.description',
                'contacts.name as lead_name',
                'retention_cases.status',
                'retention_cases.opened_at',
                'retention_cases.estimated_resolved_at',
                'retention_cases.notes',
                'projects.name as project_name',
                'clusters.name as cluster_name',
                'units.type as unit_type',
                'sub_contractors.name as sub_contractor_name',
                'sub_contractors.id as sub_contractor_id',
                'unit_properties.unit_number as unit_number',
                'retention_cases.case_pictures',
                'retention_cases.case_documentations',
                DB::raw("DATE_PART('day', NOW()::timestamp - retention_cases.opened_at::timestamp) AS duration")
            )->first();

            $retensi->case_pictures = collect($retensi->case_pictures)->map(function ($path) {
                return url($path);
            });

            $retensi->case_documentations = collect($retensi->case_documentations)->map(function ($path) {
                return url($path);
            });
        
        if (!$retensi) {
            return [
                'error' => 'Unit not found',
                'result' => null,
                'code' => 404
            ];
        }
        return [
            'error' => null,
            'result' => $retensi,
            'code' => 200
        ];
    }

    public function update($data, $id)
    {
        $retensi = RetentionCase::where('id', $id)->first();
        if (!$retensi) {
            return [
                'error' => 'Retention case not found',
                'result' => null,
                'code' => 404
            ];
        }
        $oldCaseDocumentation = $retensi->case_documentations;


        try {
            DB::beginTransaction();

            if ($retensi->case_documentations) {
                foreach ($retensi->case_documentations as $casePicture) {
                    $path = explode('api/file/', $casePicture);
                    deleteFile($path[1]);
                }
            }

            $caseDocumentationIds = [];
            if ($data['case_documentations']) {
                foreach ($data['case_documentations'] as $casePicture) {
                    $filename = uploadFile('property/retention-case/documentation-case', $casePicture);
                    if ($filename === false) {
                        DB::rollBack();
                        return [
                            'error' => 'Failed to upload file',
                            'result' => null,
                            'code' => 500
                        ];
                    }
                    $caseDocumentationIds[] = $filename;
                }
            }

            $data = [
                'status' => $data['status'],
                'case_documentations' => $caseDocumentationIds,
                'notes' => $data['notes']
            ];

            $retensi->update($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'result' => null,
                'code' => 500
            ];
        }

        if ($oldCaseDocumentation) {
            foreach ($oldCaseDocumentation as $casePicture) {
                $path = explode('api/file/', $casePicture);
                deleteFile($path[1]);
            }
        }

        return [
            'error' => null,
            'result' => null,
            'code' => 200
        ];
    }

    public function destroy($id)
    {
        $retensi = RetentionCase::where('id', $id)->first();
        if (!$retensi) {
            return [
                'error' => 'Retention case not found',
                'result' => null,
                'code' => 404
            ];
        }

        try {
            DB::beginTransaction();

            if ($retensi->case_documentations) {
                foreach ($retensi->case_documentations as $casePicture) {
                    $path = explode('api/file/', $casePicture);
                    deleteFile($path[1]);
                }
            }

            if ($retensi->case_pictures) {
                foreach ($retensi->case_pictures as $casePicture) {
                    $path = explode('api/file/', $casePicture);
                    deleteFile($path[1]);
                }
            }

            $retensi->delete();

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
}