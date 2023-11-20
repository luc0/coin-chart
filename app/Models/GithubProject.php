<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GithubProject extends Model
{
    use HasFactory;

    public function githubCommits(): HasMany
    {
        return $this->hasMany(GithubCommit::class);
    }

    public function getLatsCommitAt(): ?string
    {
        return $this->githubCommits()->orderByDesc('committed_at')->first()?->committed_at;
    }
}
