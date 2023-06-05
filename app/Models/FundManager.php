<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundManager extends Model {

    use HasFactory;

    public const TABLE = 'fund_managers';

    public $table = 'fund_managers';

    protected $fillable = [
        'name',
    ];

    public function funds() {
        return $this->hasMany(Fund::class);
    }
}
