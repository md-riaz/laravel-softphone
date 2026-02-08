<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Disposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function calls(): BelongsToMany
    {
        return $this->belongsToMany(Call::class, 'call_dispositions')
            ->withPivot('user_id')
            ->withTimestamps();
    }
}
