<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository {

    /**
     * Search or create user by email and return
     *
     * @param string $name
     * @param string $email
     *
     * @return User
     */
    public function getOrCreate(string $name, string $email): User {

        $user = $this->getUserByEmail($email);

        if (!$user) {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->save();
        }

        return $user;
    }

    /**
     * Find and return user by email
     *
     * @param string $email
     *
     * @return Model|null
     */
    public function getUserByEmail(string $email): Model|null {
        return User::query()->where('email', $email)->first();
    }
}
