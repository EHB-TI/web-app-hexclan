<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\BankAccount;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EventResource::collection(Event::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Make sure that event name is lowercase when stored in db. Event names should be unique.

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:events|max: 30',
            'date' => 'required|date',
            'bank_account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $bankAccount = BankAccount::findOrFail($validatedAttributes['bank_account_id']);

        $event = Event::create([
            'name' => $validatedAttributes['name'],
            'date' => $validatedAttributes['date'],
            'bank_account_id' => $bankAccount->id
        ]);

        $event->bankAccount()->associate($bankAccount);

        // Given that the relationship is loaded, the bank account will be returned here with the created event.
        return (new EventResource($event))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return new EventResource(Event::findOrFail($event->id));
    }

    /**
     * TODO
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:events|max: 30',
            'date' => 'required|date',
            'bank_account_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $bankAccount = BankAccount::findOrFail($validatedAttributes['bank_account_id']);

        $event = Event::create([
            'name' => $validatedAttributes['name'],
            'date' => $validatedAttributes['name'],
            'bank_account_id' => $bankAccount->id
        ]);

        return (new EventResource($event))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        Event::destroy($user->id);

        return response()->noContent();
    }
}
