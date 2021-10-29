<?php

namespace App\Http\Controllers;

use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;

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
        $validatedAttributes = $request->validate([
            'name' => 'required|unique:tenants|max: 30',
            'domain' => 'required|unique:domains|max: 30' //TODO: should be subdomain of hexclan.test
        ]);

        $tenant = Tenant::create(['name' => $validatedAttributes['name']]);
        $tenant->domains()->create(['domain' => $validatedAttributes['domain']]);

        return new TenantResource($tenant); //TODO: nested resource
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
        $validatedAttributes = $request->validate([
            'name' => 'required|unique:tenants|max: 30',
            'domain' => 'required|unique:domains|max: 30' //TODO: should be subdomain of hexclan.test
        ]);

        $tenant->update($validatedAttributes['name']);
        $tenant->update($validatedAttributes['domain']);

        return new TenantResource($tenant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tenant $tenant)
    {
        return Tenant::destroy($tenant->id);
    }
}
