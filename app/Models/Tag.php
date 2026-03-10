<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'type',
        'color',
        'icon',
        'description',
    ];

    /**
     * Get the songs that belong to this tag.
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class)
            ->withTimestamps();
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get available tag types.
     */
    public static function getTypes(): array
    {
        return [
            'mood' => 'Ánimo/Estado',
            'theme' => 'Tema/Contenido',
            'moment' => 'Momento del Culto',
            'tempo' => 'Tempo/Velocidad',
        ];
    }

    /**
     * Get available colors.
     */
    public static function getColors(): array
    {
        return [
            'blue' => 'Azul',
            'green' => 'Verde',
            'red' => 'Rojo',
            'yellow' => 'Amarillo',
            'purple' => 'Morado',
            'pink' => 'Rosa',
            'indigo' => 'Índigo',
            'orange' => 'Naranja',
            'teal' => 'Verde azulado',
            'gray' => 'Gris',
        ];
    }
}
