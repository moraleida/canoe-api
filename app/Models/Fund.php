<?php

namespace App\Models;

use App\Http\Resources\FundResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class Fund extends Model
{

    use HasFactory;

    public const TABLE = 'funds';

    public $table = 'funds';

    protected $fillable = [
        'name',
        'year',
        'fund_manager_id',
    ];

    public function fundAliases()
    {
        return $this->hasMany(FundAlias::class);
    }

    public function fundManager()
    {
        return $this->belongsTo(FundManager::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function updateRelationships(array $companies)
    {
        $companies = $companies['companies'];
        if ( ! empty($companies['add'])) {
            foreach ($companies['add'] as $id) {
                if ( ! $this->companies()->find($id)) {
                    $this->companies()->attach($id);
                }
            }
        }

        if ( ! empty($companies['remove'])) {
            foreach ($companies['remove'] as $id) {
                if ($this->companies()->find($id)) {
                    $this->companies()->detach($id);
                }
            }
        }
    }

    public static function findDuplicates(Fund $fund)
    {
        $fundsFromManager = $fund->fundManager()->first()->funds()->get()->except($fund->id);
        $aliasesByManager = FundAlias::whereIn('fund_id', $fundsFromManager->pluck('id')->toArray())->get();
        $objects    = array_merge($fundsFromManager->toArray(), $aliasesByManager->toArray());
        $duplicates = array_filter($objects, function ($obj) use ($fund) {
            return $obj['name'] === $fund->name;
        });

        return $duplicates;
    }

}
