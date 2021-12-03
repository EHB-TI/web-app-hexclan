<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $sevent)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:name',
            'data.name' => ['required', Rule::unique('categories', 'name'), 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $category = Category::create([
            'name' => $validatedAttributes['name'],
            'event_id' => $event->id
        ]);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:name,event_id',
            'data.name' => ['required', Rule::unique('categories', 'name')->ignore($bankAccount->id), 'max:30'],
            'data.event_id' => ['required', Rule::exists('events', 'id')]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $originalAttributes = collect($category->getAttributes())->only(array_keys($validatedAttributes));
        $changedAttributes = collect($validatedAttributes);
        $diff = $changedAttributes->diff($originalAttributes);

        $category->fill($diff);
        $category->save();

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }

    public function items(Category $category)
    {
        return ItemResource::collection($category->items);
    }
}
