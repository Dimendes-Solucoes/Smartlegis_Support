<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function list()
    {
        return User::where('is_root', false)->get();
    }

    public function find(int $id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_root) {
            $user->delete();
        }
    }
}
