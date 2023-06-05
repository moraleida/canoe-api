<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public const TABLE = 'companies';

    public $table = 'companies';

    protected $fillable = [
        'name',
    ];

    public function funds()
    {
        return $this->belongsToMany(Fund::class);
    }
}
