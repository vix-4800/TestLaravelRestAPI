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
     * Sets the method attribute for the UserActivity model.
     *
     * @param  string  $value  The method to be set.
     */
    public function setMethodAttribute(string $value): void
    {
        $this->attributes['method'] = strtoupper($value);
    }

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

    /**
     * Scope a query to only include successful records.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereBetween('response_code', [200, 299]);
    }

    /**
     * Scope a query to only include records with a response code outside the range of 200 to 299.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->whereNotBetween('response_code', [200, 299]);
    }
}
