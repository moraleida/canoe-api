<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FundResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'fund_manager_id' => $this->fund_manager_id,
            'year'            => $this->year,
            'fund_manager'    => $this->fundManager->name,
            'companies'       => $this->companies->map(function ($company) {
                return [
                    'id'   => $company->id,
                    'name' => $company->name,
                ];
            }),
            'aliases'       => $this->fundAliases->map(function ($alias) {
                return [
                    'id'   => $alias->id,
                    'name' => $alias->name,
                ];
            }),
        ];
    }
}
