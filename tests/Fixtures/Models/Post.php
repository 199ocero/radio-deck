<?php

namespace JaOcero\RadioDeck\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JaOcero\RadioDeck\Tests\Fixtures\Enums\Platform;

class Post extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'platform' => Platform::class,
            'targets' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withPivot('note');
    }
}
