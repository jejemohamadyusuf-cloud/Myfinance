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

    public function toggleStaffStatus(User $masterUser, int $staffId): bool
    {
        $staff = User::findOrFail($staffId);

        if ($staff->isMaster() || $staff->isSuperAdmin()) {
            throw new Exception('Master or Super Admin cant be deactived');
        }

        if ($masterUser->isMaster() && $staff->owner_id !== $masterUser->id) {
            throw new Exception('You cannot deactived this user');
        }

        if ($staff->is_active) {
            $cleanEmail = preg_replace('/^(deactivated_\d+_)+/', '', $staff->email);
            $staff->update([
                'is_active' => false,
                'email' => 'deactivated_' . time() . '_' . $cleanEmail
            ]);
        } else {
            $restoredEmail = preg_replace('/^(deactivated_\d+_)+/', '', $staff->email);
            $emailExists = User::where('email', $restoredEmail)->where('id', '!=', $staff->id)->exists();

            if ($emailExists) {
                throw new Exception("Email '{{$restoredEmail}}' already registred try another one!");
            }
            $staff->update([
                'is_active' => true,
                'email' => $restoredEmail
                ]);
        }

        return true;
    }
}



