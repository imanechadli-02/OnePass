<?php

namespace App\Policies;

use App\Models\Password;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PasswordPolicy
{
    use HandlesAuthorization;

    
    public function view(User $user, Password $password): bool
    {
        return $user->id === $password->user_id;
    }


    public function update(User $user, Password $password): bool
    {
        return $user->id === $password->user_id;
    }


    public function delete(User $user, Password $password): bool
    {
        return $user->id === $password->user_id;
    }
}
