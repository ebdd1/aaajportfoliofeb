<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'tagline',
        'bio',
        'photo_path',
        'email',
        'university',
        'major',
        'semester',
        'meta_title',
        'meta_description',
    ];

    public static function getSingleton(): self
    {
        $data = Cache::remember('profile_singleton', 3600, function () {
            $profile = self::first();
            return $profile ? $profile->toArray() : [
                'name' => 'Febryanus Tambing',
                'tagline' => 'Cybersecurity Enthusiast & API Integration Specialist',
                'bio' => '',
                'email' => 'febryanustambling54@gmail.com',
            ];
        });

        return (new self)->forceFill($data);
    }

    public static function clearCache(): void
    {
        Cache::forget('profile_singleton');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : null;
    }
}
