<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Scope a query to order categories by demand (post count) in a given location.
     */
    public function scopeOrderedByLocationDemand(Builder $query, ?string $city, ?string $zipCode = null): Builder
    {
        return $query->withCount(['posts' => function ($query) use ($city, $zipCode) {
            $query->where(function ($q) use ($city, $zipCode) {
                if ($city) {
                    $q->where('city', $city);
                }
                if ($zipCode) {
                    $q->orWhere('zip_code', $zipCode);
                }
            })->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
        }])->orderByDesc('posts_count')->orderBy('id');
    }
}
