<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class AuthController extends ApiController
{


    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function login(LoginRequest $request)
    {
        $token = $this->userRepository->auth($request->validated(), $request->has('remember_me'));
        if (!$token) {
            return $this->error(__('Логин или пароль указан неверно'), 422);
        }
        $user = User::where('email', $request->email)->first();
        return [
            'user' => new UserResource($user->load([
                'favoriteProducts',
                'shops' => [
                    'city',
                    'offices' => [
                        'city'
                    ]
                ]
            ])),
            'access_token' => $token,
        ];
    }

    public function me()
    {
        return new UserResource(Auth::user());
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(__('Выход выполнен успешно'));
    }

    public function refresh(RefreshTokenRequest $request)
    {
        abort(422, "DEPRECATED");
    }
}
