<?php

namespace App\Auth;

use Illuminate\Auth\GenericUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class StaticUser extends GenericUser implements FilamentUser, HasName
{
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->attributes['name'] ?? 'Admin';
    }

    public function getAttributeValue($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getKey()
    {
        return $this->getAuthIdentifier();
    }

    public function getKeyName()
    {
        return $this->getAuthIdentifierName();
    }

    public function is($model)
    {
        return $model && $this->getKey() === $model->getKey();
    }

    public function getRememberToken()
    {
        return $this->attributes[$this->getRememberTokenName()] ?? null;
    }

    public function setRememberToken($value)
    {
        $this->attributes[$this->getRememberTokenName()] = $value;
    }
}
