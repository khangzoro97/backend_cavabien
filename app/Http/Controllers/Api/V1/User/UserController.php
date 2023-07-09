<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\User\UserServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends ApiController
{
    private $userServiceInterface;

    public function __construct(UserServiceInterface $userServiceInterface)
    {
        $this->userServiceInterface = $userServiceInterface;
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
        list($datas, $status) = $this->userServiceInterface->register($request->all());

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
        list($datas, $status) = $this->userServiceInterface->login($request->all());

        return $this->response($datas, $status);
    }

    public function logout()
    {
        list($datas, $status) = $this->userServiceInterface->logout();

        return $this->response($datas, $status);
    }

    /**
     * Send mail reset password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMailResetPassword(Request $request)
    {
        $this->userServiceInterface->sendMailResetPassword($request->email);

        return $this->responseSuccess();
    }

    /**
     * Verify token reset password
     *
     * @return RedirectResponse
     */
    public function verifyTokenResetPassword(Request $request)
    {
        try {
            $this->userServiceInterface->verifyTokenResetPassword($request->token);

            return redirect()->to(config('app.url_fe') . config('constants.screen_fe.change-password') . '?token=' . $request->token);
        } catch (Throwable $ex) {
            Log::error($ex->getMessage());
            return redirect('/');
        }
    }

    /**
     * Reset password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->userServiceInterface->updatePassword($request);

        return $this->responseSuccess();
    }
}
