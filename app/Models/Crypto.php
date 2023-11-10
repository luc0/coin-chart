<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Crypto extends Model
{
    use HasFactory;

    public function githubProject(): HasOne
    {
        return $this->hasOne(GithubProject::class);
    }
}
