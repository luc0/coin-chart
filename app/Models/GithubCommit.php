<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubCommit extends Model
{
    use HasFactory;

    protected $fillable = [
        'committed_at'
    ];
}
