<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PbxConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'host',
        'port',
        'wss_url',
        'stun_server',
        'turn_server',
        'turn_username',
        'turn_password',
        'is_active',
    ];

    protected $hidden = [
        'turn_password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'port' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(Extension::class);
    }
}
