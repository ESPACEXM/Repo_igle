<?php

namespace App\Models;

use App\Exceptions\InvalidAttendanceException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'rehearsal_id',
        'event_id',
        'user_id',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($attendance) {
            // Always validate attendance associations to ensure data integrity
            $rehearsalId = $attendance->rehearsal_id;
            $eventId = $attendance->event_id;

            // Ensure attendance is associated with either a rehearsal or an event
            if (is_null($rehearsalId) && is_null($eventId)) {
                throw InvalidAttendanceException::missingAssociation();
            }

            // Ensure attendance is not associated with both a rehearsal and an event
            if (!is_null($rehearsalId) && !is_null($eventId)) {
                throw InvalidAttendanceException::duplicateAssociation();
            }
        });
    }

    /**
     * Get the rehearsal that owns this attendance.
     */
    public function rehearsal(): BelongsTo
    {
        return $this->belongsTo(Rehearsal::class);
    }

    /**
     * Get the event that owns this attendance.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user that owns this attendance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for present attendances.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope for absent attendances.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope for justified absences.
     */
    public function scopeJustified($query)
    {
        return $query->where('status', 'justified');
    }
}