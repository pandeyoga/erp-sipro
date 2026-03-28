<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Controller
{
    use ApiResponse;
}
