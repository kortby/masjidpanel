<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedIdentifier extends Model
{
    protected $fillable = [
        'type',
        'value',
        'user_id',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
