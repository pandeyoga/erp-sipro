<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\Property\StoreProjectRequest;
use App\Http\Requests\Property\UpdateProjectRequest;
use App\Services\Property\ProjectService;
use App\Services\Property\SiteplanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteplanController extends Controller
{
    public function __construct(protected SiteplanService $service) {}

    public function index($projectId)
    {
        $index = $this->service->index($projectId);
        if ($index['error']) {
            return $this->errorResponse($index['error'], null, $index['code']);
        }
        $data = $index['result'];

        return $this->successResponse($data, 'Siteplan retrieved successfully', 200);
    }

    public function changeSiteplanImage(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10800',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $change = $this->service->changeSiteplanImage($request, $projectId);
        if ($change['error']) {
            return $this->errorResponse($change['error'], null, $change['code']);
        }
        return $this->successResponse($change['result'], 'Siteplan image changed successfully', 200);
    }

    public function getUnitPropertyList($projectId)
    {
        $list = $this->service->getUnitPropertyList($projectId);
        if ($list['error']) {
            return $this->errorResponse($list['error'], null, $list['code']);
        }
        return $this->successResponse($list['result'], 'Unit property list retrieved successfully', 200);
    }

    public function storeUnit(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|uuid|exists:unit_properties,id',
            'top' => 'required|numeric',
            'left' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'rotate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $store = $this->service->storeUnit($request, $projectId);
        if ($store['error']) {
            return $this->errorResponse($store['error'], null, $store['code']);
        }
        return $this->successResponse(['unit_id' => $store['result']], 'Unit created successfully', 201);
    }

    public function showUnit($projectId, $propertyId)
    {
        $show = $this->service->getById($projectId, $propertyId);
        if ($show['error']) {
            return $this->errorResponse($show['error'], null, $show['code']);
        }
        return $this->successResponse($show['result'], 'Unit retrieved successfully', 200);
    }

    public function updateUnit(Request $request, $projectId, $propertyId)
    {
        $validator = Validator::make($request->all(), [
            'top' => 'required|numeric',
            'left' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'rotate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            );
        }

        $update = $this->service->updateUnit($request, $projectId, $propertyId);
        if ($update['error']) {
            return $this->errorResponse($update['error'], null, $update['code']);
        }
        return $this->successResponse(null, 'Unit updated successfully', 200);
    }

    public function destroyUnit($projectId, $propertyId)
    {
        $destroy = $this->service->destroyUnit($projectId, $propertyId);
        if ($destroy['error']) {
            return $this->errorResponse($destroy['error'], null, $destroy['code']);
        }
        return $this->successResponse(null, 'Unit deleted successfully', 200);
    }

}
