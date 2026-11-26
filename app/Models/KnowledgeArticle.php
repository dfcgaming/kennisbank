<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KnowledgeArticle extends Model
{
    protected $fillable = ['title', 'body'];

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class);
    }
}
