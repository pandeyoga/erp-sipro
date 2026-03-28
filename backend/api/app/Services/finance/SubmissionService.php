<?php
        
namespace App\Services\finance;

use App\Repositories\finance\SubmissionRepository;

class SubmissionService
{
    public function __construct(
        protected SubmissionRepository $repository
    ) {}

    public function categories()
    {
        $categories = $this->repository->categories();
        return $categories;
    }

    public function subCategories($categoryId)
    {
        $subCategories = $this->repository->subCategories($categoryId);
        return $subCategories;
    }

    public function create($data)
    {
        $category = $this->repository->getCategory($data['category_id']);
        if ($category['error']) {
            return $category;
        }

        $subCategory = $this->repository->getSubCategory($data['sub_category_id'], $data['category_id']);
        if ($subCategory['error']) {
            return $subCategory;
        }

        $data = [
            'type' => $data['type'],
            'category_id' => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'notes' => $data['notes'] ?? null,
            'submitted_by' => auth()->user()->id,
            'file_proof' => $data['file_proof'] ?? null
        ];
        
        $create = $this->repository->create($data);
        
        return $create;
    }

    public function getAll($data)
    {
        $cashIns = $this->repository->getAll($data);

        if ($cashIns['error']) {
            return $cashIns;
        }

        $item = $cashIns['result']->map(function ($item) {
            return [
                'id' => $item->id,
                'status' => $item->status,
                'category_id' => $item->category_id,
                'category' => $item->category,
                'sub_category' => $item->sub_category,
                'amount' => $item->amount,
                'description' => $item->description,
                'file_proof' => $item->file_proof ? url($item->file_proof) : null,
                'created_at' => date('Y-m-d', strtotime($item->created_at)),
                'created_by' => $item->submitted_by,
                'approved_by' => $item->approved_by,
                'approved_at' => date('Y-m-d', strtotime($item->approved_at)),
                'notes' => $item->notes,
            ];
        });

        // append item ke paginasi
        $cashIns['result']->setCollection($item);

        return $cashIns;
    }

    public function getById($id)
    {
        // cek apakah ada data dengan id tersebut
        $result = $this->repository->getCategoryType($id);
        if ($result['error']) {
            return $result;
        }

        return [
            'error' => null,
            'status' => 200,
            'result' => $result['result']
        ];
        
    }

    public function delete($id)
    {
        $delete = $this->repository->delete($id);
        return $delete;
    }

    public function approve($id)
    {
        $approve = $this->repository->approve($id);
        return $approve;
    }

    public function reject($id)
    {
        $reject = $this->repository->reject($id);
        return $reject;
    }

    public function update($id, $data)
    {
        $update = $this->repository->update($id, $data);
        return $update;
    }
    
}