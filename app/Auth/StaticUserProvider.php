<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class StaticUserProvider implements UserProvider
{
    protected array $users = [
        1 => [
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@hmhhotelgroup.com',
            'password' => 'password123',
        ]
    ];

    public function retrieveById($identifier)
    {
        if (isset($this->users[$identifier])) {
            return new StaticUser($this->users[$identifier]);
        }
        return null;
    }

    public function retrieveByToken($identifier, $token) { return null; }
    
    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveByCredentials(array $credentials)
    {
        foreach ($this->users as $user) {
            if ($user['email'] === ($credentials['email'] ?? null)) {
                return new StaticUser($user);
            }
        }
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $user->getAuthPassword() === ($credentials['password'] ?? null);
    }
    
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false) {}
}
