<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use App\Mail\SendResetLink;
use App\Models\PasswordReset;
use App\Repositories\UserRepositoryInterface;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserService extends BaseService implements UserServiceInterface
{
    private $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    private function validateRegister($params)
    {
        $validator = Validator::make($params, [
            'name' => 'bail|required|string|max:255',
            'email' => 'bail|required|email|max:255|unique:users',
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
            $user = $this->userRepositoryInterface->create($validated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [$this->responseError(['message' => 'error server']), Response::HTTP_INTERNAL_SERVER_ERROR];
        }

        return [$this->responseSuccess(new UserResource($user)), Response::HTTP_OK];
    }

    public function validateLogin($params)
    {
        $validator = Validator::make($params, [
            'email' => 'bail|required|email|max:255|exists:users',
            'password' => 'bail|required|string|min:8|max:255',
            'remember_token' => 'bail|required|boolean',
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
        $rememberToken = $validated['remember_token'] ? true : false;
        unset($validated['remember_token']);

        if (Auth::guard('web')->attempt($validated, $rememberToken)) {
            $user = Auth::guard('web')->user();

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

    public function sendMailResetPassword($email)
    {
        $token = $this->generateTokenPasswordReset();

        PasswordReset::updateOrCreate([
            'email' => $email,
        ], [
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $resetLink = URL::temporarySignedRoute(
            'password.reset',
            now()->addDays(config('constant.url_expiration_date')),
            ['token' => $token]
        );
        Mail::to($email)->queue(new SendResetLink($resetLink));

        return true;
    }

    public function generateTokenPasswordReset()
    {
        $key = config('app.key');

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return hash_hmac('sha256', Str::random(40), $key);
    }

    public function verifyTokenResetPassword($token)
    {
        $passwordReset = PasswordReset::where([
            'token' => $token,
        ])->first();

        if (!$passwordReset) {
            throw new ModelNotFoundException();
        }

        return true;
    }

    public function updatePassword($request)
    {
        $passwordReset = PasswordReset::where([
            'token' => $request->token,
        ])->first();

        if (!$passwordReset) {
            throw new ModelNotFoundException(trans('messages.not_found_user'));
        }

        $user = $this->userRepositoryInterface->filterFirst(['email' => $passwordReset->email]);
        if (!$user) {
            throw new ModelNotFoundException(trans('messages.not_found_user'));
        }

        $this->userRepositoryInterface->update($user->id, [
            'password' => Hash::make($request->password),
            'remember_token' => null,
        ]);

        return true;
    }
}
