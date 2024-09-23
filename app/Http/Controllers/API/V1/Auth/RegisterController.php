<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Notifications\UserRegisteredNotification;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class RegisterController extends ApiController
{

    public function __construct(
        protected UserRepository $userRepository
    ) {
    }

    public function store(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = ($this->userRepository->register($data));

        $user->notify(new UserRegisteredNotification($user, $request->input('password')));
        return [
            'user' => new UserResource($user),
            'access_token' => $this->userRepository->auth($data),
        ];
    }
}
