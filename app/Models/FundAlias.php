<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundAlias extends Model
{
    use HasFactory;

    public const TABLE = 'fund_aliases';

    public $table = 'fund_aliases';

    protected $fillable = [
        'name',
    ];

    public function fund(): BelongsTo {
        return $this->belongsTo(Fund::class);
    }
}
