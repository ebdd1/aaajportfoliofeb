<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;
    protected $fillable = [
        'projects_count',
        'semesters_count',
        'experiences_count',
    ];

    protected $casts = [
        'projects_count' => 'integer',
        'semesters_count' => 'integer',
        'experiences_count' => 'integer',
    ];

    public static function getSingleton(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'projects_count' => 3,
            'semesters_count' => 4,
            'experiences_count' => 3,
        ]);
    }
}
