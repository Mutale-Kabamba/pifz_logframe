<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'assigned_officer_id',
        'spreadsheet_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    /** @return HasMany<LogframeItem, $this> */
    public function logframeItems(): HasMany
    {
        return $this->hasMany(LogframeItem::class);
    }

    /** @return HasMany<LogframeItem, $this> */
    public function impacts(): HasMany
    {
        return $this->logframeItems()->where('category', 'impact');
    }

    /** @return HasMany<LogframeItem, $this> */
    public function outcomes(): HasMany
    {
        return $this->logframeItems()->where('category', 'outcome');
    }

    /** @return HasMany<LogframeItem, $this> */
    public function outputs(): HasMany
    {
        return $this->logframeItems()->where('category', 'output');
    }

    /** @return HasMany<LogframeItem, $this> */
    public function activities(): HasMany
    {
        return $this->logframeItems()->where('category', 'activity');
    }
}
