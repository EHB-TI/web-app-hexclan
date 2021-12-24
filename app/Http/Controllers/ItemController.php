<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Http\Resources\TransactionResource;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ItemResource::collection(Item::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:name,price,vat_rate',
            'data.name' => ['required', Rule::unique('items', 'name'), 'max:30'],
            'data.price' => 'required|numeric|min:0|max:99.99', // Client should use decimal separator '.'. 
            'data.vat_rate' => 'required|integer|min:0|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $price = str_replace('.', '', $validatedAttributes['price']);

        $item = Item::create([
            'name' => $validatedAttributes['name'],
            'price' => $price,
            'vat_rate' => $validatedAttributes['vat_rate'],
            'category_id' => $category->id
        ]);

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return new ItemResource($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:name,price,vat_rate',
            'data.name' => ['required', Rule::unique('items', 'name')->ignore($item->id), 'max:30'],
            'data.price' => 'required|numeric|min:0|max:99.99',
            'data.vat_rate' => 'required|integer|min:0|max:50',
            'data.category_id' => ['required', Rule::exists('categories', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $originalAttributes = collect($item->getAttributes())->only(array_keys($validatedAttributes));
        $changedAttributes = collect($validatedAttributes);
        $diff = $changedAttributes->diff($originalAttributes);

        $item->fill($diff->toArray());
        $item->save();

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return response()->noContent();
    }

    public function transactions(Item $item)
    {
        return TransactionResource::collection($item->transactions);
    }
}
