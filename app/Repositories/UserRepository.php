<?php

namespace App\Repositories;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class UserRepository
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] =  $data['role'] ?? UserRoles::default();
        return User::create($data);
    }


    public function auth($credentials, $remember = false)
    {
        $credentials = ['email' => $credentials['email'], 'password' => $credentials['password']];
        $user = User::where('email', $credentials['email'])->first();
        if (is_null($user)) {
            return null;
        }
        $is_password_checked = Hash::check($credentials['password'], $user->password);
        if (!$is_password_checked) {
            return null;
        }
        $device = request()->header('User-Agent');
        return $user->createToken($device)->plainTextToken;
    }


    public function refreshToken(User $user): string
    {
        $refreshToken = Str::uuid();
        $user->update(['refresh_token' => $refreshToken]);
        return $refreshToken;
    }


    public function changeRole(User $user, string $newRole): User
    {
        $user->update(['role' => $newRole]);
        return $user;
    }

    public function changePassword(User $user, string $newPassword): User
    {
        $user->update(['password' => Hash::make($newPassword)]);
        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();
        return $user;
    }

    public function expireToken(): float|int
    {
        return Auth::factory()->getTTL() * 60;
    }
}
