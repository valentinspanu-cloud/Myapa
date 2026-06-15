<?php

namespace App\Services;

use Illuminate\Support\Str;

class Slug
{
    public static function createSlug($slug, $id = null)
    {
        if (empty($slug)) {
            return $id ? (string) $id : Str::random(8);
        }
        return Str::slug($slug);
    }
}
