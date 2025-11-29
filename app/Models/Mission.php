<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    /** @use HasFactory<\Database\Factories\MissionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'id_mission';
    protected $fillable = [
        'title_mission',
        'description_mission',
        'difficulty_mission',
        'status_mission',
        'created_at',
        'updated_at',
    ];

    /**
     * Los héroes asignados a esta misión.
     * Relación muchos a muchos.
     */
    public function heroes()
    {
        return $this->belongsToMany(
            Hero::class,
            'hero_mission',
            'id_mission',
            'id_hero',
            'id_mission',
            'id_hero'
        )->withPivot('status', 'group_name', 'started_at', 'completed_at', 'failed_at', 'notes')
         ->withTimestamps();
    }

    //Antes de registrar 
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mission) {
            $mission->created_at = now();
        });
    }

    //Antes de actualizar
    protected static function booted()
    {
        static::updating(function ($mission) {
            $mission->updated_at = now();
        });
    }
}