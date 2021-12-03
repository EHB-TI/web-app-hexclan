<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BankAccountResource::collection(BankAccount::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:beneficiary_name,bic,iban',
            'data.beneficiary_name' => 'required|max:255',
            'data.bic' => 'required|alpha_num|max:8', //TODO: could be improved with regex.
            'data.iban' => 'required|alphanum|unique:bank_accounts|max:16' //TODO: could be improved with regex.
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $bankAccount = BankAccount::create([
            'beneficiary_name' => $validatedAttributes['beneficiary_name'],
            'bic' => $validatedAttributes['bic'],
            'iban' => $validatedAttributes['iban'],
        ]);

        return (new BankAccountResource($bankAccount))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        return new BankAccountResource($bankAccount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:beneficiary_name,bic,iban',
            'data.beneficiary_name' => 'required|max:255',
            'data.bic' => 'required|alpha_num|max:8', //TODO: could be improved with regex.
            'data.iban' => ['required', 'alphanum', Rule::unique('bank_accounts', 'iban')->ignore($bankAccount->id), 'max:16'] //TODO: could be improved with regex.
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $originalAttributes = collect($bankAccount->getAttributes())->only(array_keys($validatedAttributes));
        $changedAttributes = collect($validatedAttributes);
        $diff = $changedAttributes->diff($originalAttributes); // Return the values in the changedAttributes that are not present in the originalAttributes.

        $bankAccount->fill($diff);
        $bankAccount->save();

        return (new BankAccountResource($bankAccount))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return response()->noContent();
    }
}

// Bulk entity creation
/* public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'data.*' => 'required|array:beneficiary_name,bic,iban',
        'data.*.beneficiary_name' => 'required|max:255',
        'data.*.bic' => 'required|alpha_num|max:8', //TODO: could be improved with regex.
        'data.*.iban' => 'required|alphanum|unique:bank_accounts|max:16' //TODO: could be improved with regex.
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $rawValidatedAttributes = $validator->validated();

    $collection = collect();
    foreach ($rawValidatedAttributes['data'] as $validatedAttributes) {
        $collection->push(BankAccount::create([
            'beneficiary_name' => $validatedAttributes['beneficiary_name'],
            'bic' => $validatedAttributes['bic'],
            'iban' => $validatedAttributes['iban'],
        ]));
    }

    return (BankAccountResource::collection($collection))
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);
} */
