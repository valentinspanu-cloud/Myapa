<?php

namespace App\Models;

// Laravel 11 + spatie/laravel-permission v6
// Nu mai folosi acest model direct — folosește Spatie Role
// Păstrat pentru compatibilitate cu codul existent

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Poți adăuga metode custom dacă ai nevoie
}
