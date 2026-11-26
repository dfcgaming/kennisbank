<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'equipment_kind',
        'asset_tag',
        'serial_number',
        'manufacturer',
        'model',
        'location',
        'hostname',
        'ip_address',
        'person_id',
    ];

    protected function casts(): array
    {
        return ['type' => AssetType::class];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeArticle::class);
    }
}
