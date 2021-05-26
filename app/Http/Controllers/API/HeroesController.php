<?php

namespace App\Http\Controllers\API;

use App\Heroes;
use App\Http\Controllers\Controller;
use App\Http\Requests\HeroesRequest;
use App\Http\Requests\StoreHeroesRequest;
use App\Http\Resources\HeroesResource;
use Illuminate\Http\Request;

class HeroesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = Heroes::query();
        $sortBy = $request->input('sortBy', 'created_at');
        $orderBy = $request->input('orderBy', 'desc');
        $perPage = $request->input('perPage', 10);

        $perpage = $this->getPaginationSize($perPage);
        $records = $this->searchRow($request, $records);
        $records = $this->sortRow($sortBy, $orderBy, $records);

        return HeroesResource::collection($records->paginate($perpage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HeroesRequest $request)
    {
        $result = Heroes::create($request->validated());

        return new HeroesResource($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Heroes  $heroes
     * @return \Illuminate\Http\Response
     */
    public function show(Heroes $hero)
    {
        return new HeroesResource($hero);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Heroes  $heroes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Heroes $hero)
    {
        $hero->fill($request->all())->save();

        return new HeroesResource($hero);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Heroes  $heroes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Heroes $hero)
    {
        $hero->delete();

        return response()->json(['message' => 'Hero has been deleted']);
    }

    /**
     * Private function to defined size of pagination
     *
     * @param [Integer] $perPage
     * @return Integer
     */
    protected function getPaginationSize($perPage)
    {
        $perPageAllowed = [20, 50, 100, 500];

        if(in_array($perPage, $perPageAllowed)) {
            return $perPage;
        }

        return 10;
    }

    /**
     * Private function to search row
     *
     * @param [String] $request
     * @param [Collection] $records
     * @return Collection
     */
    protected function searchRow($request, $records)
    {
        if ($request->has('name')) {
            $records = $records->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('ability')) {
            $records = $records->where('agility', 'LIKE', '%' . $request->ability . '%');
        }

        return $records;
    }

    /**
     * Private function to sorting
     *
     * @param [String] $sortBy
     * @param [String] $orderBy
     * @param [Collection] $records
     * @return Collection
     */
    protected function sortRow($sortBy, $orderBy, $records)
    {
        if ($sortBy == 'name') {
            $records->orderBy('status', 'asc');
        }

        $records = $records->orderBy($sortBy, $orderBy);

        return $records;
    }
}
