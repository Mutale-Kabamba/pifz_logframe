<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LogframeCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogframeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'parent_id',
        'category',
        'description',
        'indicator',
        'means_of_verification',
        'assumptions',
        'target_value',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => LogframeCategory::class,
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<self, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<self, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** @return HasMany<TrackingLog, $this> */
    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class);
    }
}
