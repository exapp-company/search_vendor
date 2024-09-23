<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enums\HttpStatus;
use App\Enums\UserRoles;
use App\Filters\UserRoleFilter;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Collections\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\ShopRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{

    public function __construct(
        protected UserRepository $userRepository,
        protected ShopRepository $shopRepository,
    )
    {
    }

    public function index(Request $request)
    {
        $role = $request->input('role');
        $usersQuery = User::query();

        UserRoleFilter::apply($usersQuery, $role);

        return new UserCollection($usersQuery->with(['shops', 'shops.feeds'])->paginate(30));
    }

    public function store(RegisterRequest $request)
    {
        $user = $this->userRepository->register($request->validated());

        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['shops', 'shops.feeds']));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'name' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::in(UserRoles::values())],
        ]);

        return new UserResource($this->userRepository->update($user, $request->all()));
    }


    public function destroy(User $user)
    {
        if ($user->delete()) {
            return $this->success(__('Пользователь успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении объекта.'), HttpStatus::internalServerError);
        }
    }

}
