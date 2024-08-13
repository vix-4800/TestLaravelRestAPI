<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class UserActivity extends Model
{
    use HasFactory;

    protected $with = ['user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'url',
        'method',
        'response_code',
        'ip_address',
    ];

    /**
     * Get the user for the activity.
     *
     * @return BelongsTo The user for the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include records created after a given date.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  Carbon  $from  The start date to filter by.
     * @return Builder The modified query builder instance.
     */
    public function scopeFromDate(Builder $query, Carbon $from): Builder
    {
        return $query->whereDate('created_at', '>=', $from);
    }

    /**
     * Scope the query to include records up to a given date.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  Carbon  $to  The end date for the scope.
     * @return Builder The modified query builder instance.
     */
    public function scopeToDate(Builder $query, Carbon $to): Builder
    {
        return $query->whereDate('created_at', '<=', $to);
    }

    /**
     * Scope the query to include records created within the last N days.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  int  $days  The number of days to filter by.
     * @return Builder The modified query builder instance.
     */
    public function scopeForLastDays(Builder $query, int $days): Builder
    {
        return $query->whereDate('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope the query to include records created within the last month.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeForLastMonth(Builder $query): Builder
    {
        return $query->forLastDays(30);
    }
}
