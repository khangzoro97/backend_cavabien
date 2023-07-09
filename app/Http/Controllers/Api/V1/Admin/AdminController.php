<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Services\Admin\AdminServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminController extends ApiController
{
    private $adminServiceInterface;

    public function __construct(AdminServiceInterface $adminServiceInterface)
    {
        $this->adminServiceInterface = $adminServiceInterface;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        list($datas, $status) = $this->adminServiceInterface->register($request->all());

        return $this->response($datas, $status);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        list($datas, $status) = $this->adminServiceInterface->login($request->all());

        return $this->response($datas, $status);
    }

    public function logout(Request $request)
    {
        list($datas, $status) = $this->adminServiceInterface->logout();

        return $this->response($datas, $status);
    }
}
