<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRoles;
use App\Http\Requests\ShopRequest;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends ApiController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected ShopRepository $shopRepository,
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $user->load([
            'favoriteProducts.shop',
            'shops' => [
                'city',
                'offices' => [
                    'city'
                ]
            ]
        ]);
        return new UserResource($user);
    }

    public function update(UpdateProfileRequest $request)
    {

        $user = $request->user();
        $user->fill($request->validated());
        $user->save();
        //$user = $this->userRepository->update(User::find($user->id), $request->all());
        return new UserResource($user->load('shops'));
    }

    public function changePassword(PasswordUpdateRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->get('old_password'), $user->password)) {
            return $this->error(__('Неверный старый пароль'), HttpStatus::notFound);
        }

        $this->userRepository->changePassword(User::find($user->id), $request->input('password'));
        return new UserResource($user);
    }


    public function createBusiness(ShopRequest $request)
    {
        $user = $request->user();

        if ($user->role !== UserRoles::get('admin')) {
            $this->userRepository->changeRole($user, UserRoles::get('supplier'));
        }

        $this->shopRepository->create($user, $request->input());
        return new UserResource($user->load('shops'));
    }
}
