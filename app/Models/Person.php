<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = ['name', 'email', 'department'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeArticle::class);
    }
}
