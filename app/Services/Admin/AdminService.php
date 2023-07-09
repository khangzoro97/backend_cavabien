<?php

namespace App\Services\Admin;

use App\Http\Resources\UserResource;
use App\Repositories\AdminRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminService extends BaseService implements AdminServiceInterface
{
    private $adminRepositoryInterface;

    public function __construct(AdminRepositoryInterface $adminRepositoryInterface)
    {
        $this->adminRepositoryInterface = $adminRepositoryInterface;
    }

    private function validateRegister($params)
    {
        $validator = Validator::make($params, [
            'name' => 'bail|required|string|max:255',
            'email' => 'bail|required|email|max:255|unique:administrators',
            'password' => 'bail|required|string|min:8|max:255',
        ]);

        return $this->validate($validator);
    }

    public function register($params)
    {
        list($status, $data) = $this->validateRegister($params);
        if (!$status) {
            return [$this->responseFail($data), Response::HTTP_UNAUTHORIZED];
        }

        $validated = $data->validated();
        $validated['password'] = Hash::make($validated['password']);

        DB::beginTransaction();
        try {
            $admin = $this->adminRepositoryInterface->create($validated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [$this->responseError(['message' => 'error server']), Response::HTTP_INTERNAL_SERVER_ERROR];
        }

        return [$this->responseSuccess($admin), Response::HTTP_OK];
    }

    public function validateLogin($params)
    {
        $validator = Validator::make($params, [
            'email' => 'bail|required|email|max:255|exists:administrators',
            'password' => 'bail|required|string|min:8|max:255',
        ]);

        return $this->validate($validator);
    }

    public function login($params)
    {
        list($status, $data) = $this->validateLogin($params);
        if (!$status) {
            return [$this->responseFail($data), Response::HTTP_UNAUTHORIZED];
        }

        $validated = $data->validated();
        if (Auth::guard('admin')->attempt($validated)) {
            $user = Auth::guard('admin')->user();

            DB::beginTransaction();
            try {
                $user->tokens()->delete();
                $accessToken = $user->createToken($validated['email'] . '' . now())->plainTextToken;

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());

                return [$this->responseError(['message' => 'error server']), Response::HTTP_INTERNAL_SERVER_ERROR];
            }

            return [
                $this->responseSuccess([
                    'user' => new UserResource($user),
                    'access_token' => $accessToken,
                ]),
                Response::HTTP_OK
            ];
        }

        $data = ['error' => 'Wrong account or password'];
        return [$this->responseFail($data), Response::HTTP_UNAUTHORIZED];
    }

    public function logout()
    {
        DB::beginTransaction();
        try {
            request()->user()->tokens()->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [$this->responseError(['message' => 'error server']), Response::HTTP_INTERNAL_SERVER_ERROR];
        }

        return [$this->responseSuccess(), Response::HTTP_OK];
    }
}
