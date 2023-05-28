<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct(private User $user)
    {
    }

    public function getUserById($id)
    {
        return $this->user->findOrFail($id);
    }

    public function createUser(array $userData)
    {
        return $this->user->create($userData);
    }

    public function updateUser($id, array $userData)
    {
        $user = $this->getUserById($id);
        $user->fill($userData);
        $user->save();

        return $user;
    }

    public function deleteById($id)
    {
        $user = $this->getUserById($id);
        $user->delete();
    }

    public function getAllUsers()
    {
        return $this->user->all();
    }
}
