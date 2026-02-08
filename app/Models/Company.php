<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function pbxConnections(): HasMany
    {
        return $this->hasMany(PbxConnection::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    public function callAnalytics(): HasMany
    {
        return $this->hasMany(CallAnalytic::class);
    }
}
