<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'extension_id',
        'user_id',
        'direction',
        'caller_number',
        'callee_number',
        'status',
        'started_at',
        'answered_at',
        'ended_at',
        'duration',
        'talk_time',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'answered_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration' => 'integer',
            'talk_time' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function extension(): BelongsTo
    {
        return $this->belongsTo(Extension::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CallNote::class);
    }

    public function callDispositions(): HasMany
    {
        return $this->hasMany(CallDisposition::class);
    }

    public function dispositions(): BelongsToMany
    {
        return $this->belongsToMany(Disposition::class, 'call_dispositions')
            ->withPivot('user_id')
            ->withTimestamps();
    }
}
