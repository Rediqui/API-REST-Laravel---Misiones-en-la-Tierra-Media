<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    /** @use HasFactory<\Database\Factories\HeroFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'id_hero';
    protected $fillable = [
        'name_hero',
        'race_hero',
        'role_hero',
        'created_at',
        'updated_at',
    ];

    /**
     * Las misiones asignadas a este héroe.
     * Relación muchos a muchos.
     */
    public function missions()
    {
        return $this->belongsToMany(
            Mission::class,
            'hero_mission',
            'id_hero',
            'id_mission',
            'id_hero',
            'id_mission'
        )->withPivot('status', 'group_name', 'started_at', 'completed_at', 'failed_at', 'notes')
         ->withTimestamps();
    }

    //Antes de registrar 
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hero) {
            $hero->created_at = now();
        });
    }

    //Antes de actualizar
    protected static function booted()
    {
        static::updating(function ($hero) {
            $hero->updated_at = now();
        });
    }
}