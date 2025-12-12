<?php

namespace App\Services;

use App\Models\Tenancy\User;

class UserService
{
    public function list()
    {
        return User::all();
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return true;
    }
}
