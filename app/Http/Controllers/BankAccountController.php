<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

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
            'beneficiary_name' => 'required|max: 255',
            'bic' => 'required|alpha_num|max: 8', // Could be improved with regex.
            'iban' => 'required|alphanum|max: 16' // Could be improved with regex.
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

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
     * TODO
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validator = Validator::make($request->all(), [
            'beneficiary_name' => 'required|max: 255',
            'bic' => 'required|alpha_num|max: 8', // Could be improved with regex.
            'iban' => 'required|alphanum|max: 16' // Could be improved with regex.
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $bankAccount = BankAccount::update([
            'beneficiary_name' => $validatedAttributes['beneficiary_name'],
            'bic' => $validatedAttributes['bic'],
            'iban' => $validatedAttributes['iban'],
        ]);

        return (new BankAccountResource($bankAccount))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        BankAccount::destroy($bankAccount->id);

        return response()->noContent();
    }
}
