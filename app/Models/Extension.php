<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'pbx_connection_id',
        'user_id',
        'extension_number',
        'password',
        'display_name',
        'is_active',
        'is_registered',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_registered' => 'boolean',
        ];
    }

    public function pbxConnection(): BelongsTo
    {
        return $this->belongsTo(PbxConnection::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }
}
