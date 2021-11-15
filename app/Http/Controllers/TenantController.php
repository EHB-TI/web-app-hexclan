<?php

namespace App\Http\Controllers;

use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TenantResource::collection(Tenant::all());
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
            'name' => 'required|unique:tenants|max: 30',
            'tenancy_admin_email' => 'required|email|max:255',
            'domain' => 'required|unique:domains|max: 30' //TODO: should be subdomain of hexclan.test
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $tenant = Tenant::create([
            'name' => $validatedAttributes['name'],
            'tenancy_admin_email' => $validatedAttributes['tenancy_admin_email']
        ]);
        $tenant->domains()->create(['domain' => $validatedAttributes['domain']]);

        return (new TenantResource($tenant))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function show(Tenant $tenant)
    {
        return new TenantResource(Tenant::findOrFail($tenant->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:tenants|max: 30',
            'domain' => 'required|unique:domains|max: 30' //TODO: should be subdomain of hexclan.test
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $tenant->update($validatedAttributes['name']);
        $tenant->update($validatedAttributes['domain']);

        return (new TenantResource($tenant))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tenant $tenant)
    {
        Tenant::destroy($tenant->id);

        return response()->noContent();
    }
}
