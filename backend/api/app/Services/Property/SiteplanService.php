<?php
        
namespace App\Services\Property;

use App\Repositories\Property\ProjectRepository;
use App\Repositories\Property\SiteplanRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SiteplanService
{
    public function __construct(
        protected SiteplanRepository $repository
    ) {}

    public function index($projectId)
    {
        return $this->repository->index($projectId);
    }

    // changeSiteplanImage
    public function changeSiteplanImage($request, $projectId)
    {
        $image = $request['image'];
        
        $result = $this->repository->changeSiteplanImage($image, $projectId);
        return $result;
    }

    public function getUnitPropertyList($projectId)
    {
        return $this->repository->getUnitPropertyList($projectId);
    }

    public function storeUnit($request, $projectId)
    {
        $data = [
            'property_id' => $request['property_id'],
            'top' => $request['top'],
            'left' => $request['left'],
            'width' => $request['width'],
            'height' => $request['height'],
            'rotate' => $request['rotate'],
        ];

        return $this->repository->store($data, $projectId);
    }

    public function getById($projectId, $propertyId)
    {
        return $this->repository->getById($projectId, $propertyId);
    }

    public function updateUnit($request, $projectId, $propertyId)
    {
        $data = [
            'top' => $request['top'],
            'left' => $request['left'],
            'width' => $request['width'],
            'height' => $request['height'],
            'rotate' => $request['rotate'],
        ];

        return $this->repository->update($data, $projectId, $propertyId);
    }

    public function destroyUnit($projectId, $propertyId)
    {
        return $this->repository->destroy($projectId, $propertyId);
    }
    
    
}