<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'artist',
        'key',
        'tempo',
        'duration',
        'lyrics_url',
        'chords_url',
        'youtube_url',
        'lyrics',
        'chords',
        'spotify_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tempo' => 'integer',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the events that include this song.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('order', 'notes')
            ->withTimestamps()
            ->orderBy('event_song.order');
    }

    /**
     * Get the tags associated with this song.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withTimestamps();
    }

    /**
     * Check if song has a specific tag.
     */
    public function hasTag(string $tagName): bool
    {
        return $this->tags()->where('name', $tagName)->exists();
    }

    /**
     * Scope a query to filter by tag.
     */
    public function scopeWithTag($query, string $tagName)
    {
        return $query->whereHas('tags', function ($q) use ($tagName) {
            $q->where('name', $tagName);
        });
    }

    /**
     * Scope a query to filter by tag type.
     */
    public function scopeWithTagType($query, string $type)
    {
        return $query->whereHas('tags', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }
}