<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function updateProfile(User $user, array $data): bool
    {
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        return $user->save();
    }

    public function createStaff(User $masterUser, array $data): User
    {
        if (!$masterUser->isMaster() && !$masterUser->isSuperAdmin()) {
            throw new Exception("Your user priviledge cannot add downline");
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_STAFF,
            'owner_id' => $masterUser->isMaster() ? $masterUser->id : null
        ]);
    }

    public function deleteStaff(User $masterUser, int $staffId): bool
    {
        $staff = User::findOrFail($staffId);

        if ($staff->isMaster() || $staff->isSuperAdmin()) {
            throw new Exception('Master or Super Admin cant be deleted');
        }

        if ($masterUser->isMaster() && $staff->owner_id !== $masterUser->id) {
            throw new Exception('You cannot delete this user');
        }

        return $staff?->delete();
    }
}



