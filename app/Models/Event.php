<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'date',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the users assigned to this event.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('instrument_id', 'status', 'notification_sent')
            ->withTimestamps();
    }

    /**
     * Get confirmed users for this event.
     */
    public function confirmedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('status', 'confirmed')
            ->withPivot('instrument_id', 'status', 'notification_sent')
            ->withTimestamps();
    }

    /**
     * Get pending users for this event.
     */
    public function pendingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('status', 'pending')
            ->withPivot('instrument_id', 'status', 'notification_sent')
            ->withTimestamps();
    }

    /**
     * Get the songs for this event.
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class)
            ->withPivot('order', 'notes')
            ->withTimestamps()
            ->orderBy('event_song.order');
    }

    /**
     * Get the rehearsals for this event.
     */
    public function rehearsals(): HasMany
    {
        return $this->hasMany(Rehearsal::class);
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now())->orderBy('date');
    }

    /**
     * Scope for past events.
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now())->orderBy('date', 'desc');
    }
}