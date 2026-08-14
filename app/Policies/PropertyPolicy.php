<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function view(User $user, Property $property): bool
    {
        return $user->isSuperAdmin() || $user->properties->contains($property->id);
    }

    public function update(User $user, Property $property): bool
    {
        return $user->isSuperAdmin() || $user->properties->contains($property->id);
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->isSuperAdmin(); // only Super Admin can delete a property
    }
}
