<?php
        
namespace App\Repositories\finance;

use App\Models\CashFlowOut;
use App\Models\CashSubmission;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
class SubmissionRepository
{
    public function categories()
    {
        $result = DB::table('cash_out_categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function subCategories($categoryId)
    {
        $result = DB::table('cash_out_sub_categories')
            ->where('category_id', $categoryId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }


    public function getCategory($id)
    {
        $result = DB::table('cash_out_categories')
            ->where('id', $id)
            ->select('id', 'name')
            ->first();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getSubCategory($id, $categoryId)
    {
        $result = DB::table('cash_out_sub_categories')
            ->where('id', $id)
            ->where('category_id', $categoryId)
            ->select('id', 'name')
            ->first();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            if ($data['file_proof']) {
                $data['file_proof'] = uploadFile('finance/submission/file_proof', $data['file_proof']);
                if ($data['file_proof'] === false) {
                    DB::rollBack();
                    return [
                        'error' => 'Failed to upload file',
                        'result' => null,
                        'code' => 500
                    ];
                }
            }
            
            $result = CashSubmission::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getAll($data = [])
    {
        $perpage = $data['per_page'] ?? 10;
        $page = $data['page'] ?? 1;
        $search = $data['search'] ?? null;
        $result = CashSubmission::join('cash_out_categories as c', 'cash_flow_submissions.category_id', '=', 'c.id')
            ->join('cash_out_sub_categories as s', 'cash_flow_submissions.sub_category_id', '=', 's.id')
            ->select(
                'cash_flow_submissions.id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_submissions.category_id',
                'cash_flow_submissions.sub_category_id',
                'cash_flow_submissions.amount',
                'cash_flow_submissions.description',
                'cash_flow_submissions.notes',
                'cash_flow_submissions.created_at',
                'cash_flow_submissions.status',
                'cash_flow_submissions.created_at',
                'cash_flow_submissions.submitted_by',
                'cash_flow_submissions.file_proof',
                'cash_flow_submissions.approved_by',
                'cash_flow_submissions.approved_at'
                )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('cash_flow_submissions.description', 'ilike', '%' . $search . '%')
                        ->orWhere('cash_flow_submissions.notes', 'ilike', '%' . $search . '%')
                        ->orWhere('c.name', 'ilike', '%' . $search . '%')
                        ->orWhere('s.name', 'ilike', '%' . $search . '%');
                });
            })
            ->orderBy('cash_flow_submissions.created_at', 'desc')
            ->paginate($perpage, ['*'], 'page', $page);

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function getCategoryType($id)
    {
        $result = CashSubmission::join('cash_out_categories as c', 'cash_flow_submissions.category_id', '=', 'c.id')
            ->join('cash_out_sub_categories as s', 'cash_flow_submissions.sub_category_id', '=', 's.id')
            ->select(
                'cash_flow_submissions.id',
                'c.id as category_id',
                's.id as sub_category_id',
                'c.name as category',
                's.name as sub_category',
                'cash_flow_submissions.file_proof',
                'cash_flow_submissions.status as status',
                'cash_flow_submissions.feedback',
                'cash_flow_submissions.submitted_by',
                'cash_flow_submissions.approved_by',
                'cash_flow_submissions.approved_at',
                'cash_flow_submissions.amount',
                'cash_flow_submissions.description',
                'cash_flow_submissions.notes',
                )
            ->where('cash_flow_submissions.id', $id)
            ->first();

        $result->file_proof = $result->file_proof ? url($result->file_proof) : null;

        return [
            'error' => $result ? null : 'Data not found',
            'status' => $result ? 200 : 404,
            'result' => $result
        ];
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $submission = CashSubmission::where('id', $id)->first();
            // cek status submission
            if ($submission->status == 'approved') {
                DB::rollBack();
                return [
                    'error' => 'Submission already approved',
                    'status' => 400,
                    'result' => null
                ];
            }
            DB::commit();
            return [
                'error' => null,
                'status' => 200,
                'result' => null
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage(),
                'status' => 500,
                'result' => null
            ];
        }
    }

    // approve
    public function approve($id) {
        $result = CashSubmission::where('id', $id)->first();
        if (!$result) {
            return [
                'error' => 'Submission not found',
                'status' => 404,
                'result' => null
            ];
        }
        if ($result->status == 'approved' || $result->status == 'rejected') {
            return [
                'error' => 'Submission already ' . $result->status,
                'status' => 400,
                'result' => null
            ];
        }

        $result->status = 'approved';
        $result->approved_at = now();
        $result->approved_by = auth()->user()->id;
        $result->save();

        CashFlowOut::create([
            'category_id' => $result->category_id,
            'sub_category_id' => $result->sub_category_id,
            'total_amount' => $result->amount,
            'paid_amount' => 0,
            'description' => $result->description,
            'notes' => $result->notes
        ]);

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    // reject
    public function reject($id) {
        $result = CashSubmission::where('id', $id)->first();
        if (!$result) {
            return [
                'error' => 'Submission not found',
                'status' => 404,
                'result' => null
            ];
        }

        if ($result->status == 'approved' || $result->status == 'rejected') {
            return [
                'error' => 'Submission already ' . $result->status,
                'status' => 400,
                'result' => null
            ];
        }

        $result->status = 'rejected';
        $result->save();

        return [
            'error' => null,
            'status' => 200,
            'result' => $result
        ];
    }

    public function update($id, $data) {
        $submission = CashSubmission::where('id', $id)->first();
        if (!$submission) {
            return [
                'error' => 'Submission not found',
                'status' => 404,
                'result' => null
            ];
        }

        $submission->amount = $data['amount'];
        $submission->description = $data['description'] ?? null;
        $submission->notes = $data['notes'] ?? null;
        $submission->save();

        return [
            'error' => null,
            'status' => 200,
            'result' => $submission
        ];
    }
}