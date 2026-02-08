<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallAnalytic extends Model
{
    use HasFactory;

    protected $table = 'call_analytics';

    protected $fillable = [
        'company_id',
        'date',
        'total_calls',
        'inbound_calls',
        'outbound_calls',
        'answered_calls',
        'missed_calls',
        'total_duration',
        'total_talk_time',
        'avg_duration',
        'avg_talk_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_calls' => 'integer',
            'inbound_calls' => 'integer',
            'outbound_calls' => 'integer',
            'answered_calls' => 'integer',
            'missed_calls' => 'integer',
            'total_duration' => 'integer',
            'total_talk_time' => 'integer',
            'avg_duration' => 'decimal:2',
            'avg_talk_time' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
