<?php

namespace App\Http\Controllers;

use App\Events\FundCreatedOrUpdated;
use App\Http\Resources\FundResource;
use App\Models\Company;
use App\Models\Fund;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FundsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        try {
            $filters = $request->collect();

            $per_page = $filters->get('per_page', 100);
            $order    = $filters->get('order', 'asc');
            $order_by = $filters->get('order_by', Fund::TABLE . '.id');
            $page     = $filters->get('page', 1);
            $query    = Fund::query()
                            ->groupBy(Fund::TABLE . '.id')
                            ->forPage($page, $per_page);

            if (strpos($order_by, ',') > 0) {
                $order_cols = explode(',', $order_by);

                foreach ($order_cols as $col) {
                    $query->orderBy($col, $order);
                }
            } else {
                $query->orderBy($order_by, $order);
            }

            foreach ($filters as $key => $value) {

                switch ($key) {
                    case 'name':
                    case 'fund_manager_id':
                        $query->where($key, $value);
                        break;
                    case 'year':
                        $operator = is_numeric($value) ? '=' : substr($value, 0, 1);
                        if ($operator !== '=') {
                            $value = substr($value, 1);
                        }
                        $query->where($key, $operator, $value);
                        break;
                    default:
                        break;
                }

            }

            return response()->json(FundResource::collection($query->get()), 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->input(), [
                'name'            => 'string',
                'fund_manager_id' => 'integer',
                'year'            => 'integer',
                'companies.*'     => Rule::forEach(static function () {
                    return [
                        Rule::exists(Company::class, 'id'),
                    ];
                }),
            ], [], ['excludeUnvalidatedArrayKeys' => true]);

            $fund = Fund::create($validator->safe()->except(['companies']));
            $fund->updateRelationships($validator->safe(['companies.add', 'companies.remove']));

            FundCreatedOrUpdated::dispatch($fund);

            return \response()->json(new FundResource($fund), 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fund $fund): JsonResponse
    {
        try {
            return \response()->json(new FundResource($fund), 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fund $fund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fund $fund): JsonResponse
    {
        try {
            $validator = Validator::make($request->input(), [
                'name'            => 'string',
                'fund_manager_id' => 'integer',
                'year'            => 'integer',
                'companies.*'     => Rule::forEach(static function () {
                    return [
                        Rule::exists(Company::class, 'id'),
                    ];
                }),
            ], [], ['excludeUnvalidatedArrayKeys' => true]);

            $fund->update($validator->safe()->except(['companies']));
            $fund->updateRelationships($validator->safe(['companies.add', 'companies.remove']));

            FundCreatedOrUpdated::dispatch($fund);

            return \response()->json(new FundResource($fund), 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fund $fund)
    {
        //
    }
}
