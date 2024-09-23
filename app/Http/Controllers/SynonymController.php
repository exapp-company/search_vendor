<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSynonymRequest;
use App\Http\Requests\UpdateSynonymRequest;
use App\Models\Synonym;
use App\Services\SynonymService;
use GuzzleHttp\Client;

class SynonymController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(public SynonymService $synonymService)
    {
    }
    public function index()
    {
        return Synonym::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSynonymRequest $request)
    {
        $synonym = Synonym::create($request->validated());
        return $synonym;
    }

    /**
     * Display the specified resource.
     */
    public function show(Synonym $synonym)
    {
        return $synonym;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSynonymRequest $request, Synonym $synonym)
    {
        $synonym->fill($request->validated());
        $synonym->save();
        return $synonym;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Synonym $synonym)
    {
        $synonym->delete();
        return response()->noContent();
    }

    public function refreshIndex()
    {

        $synonyms = Synonym::all();
        $result = $this->synonymService->refreshSynonymSet('products-synonym', $synonyms);
        return response()->json($result);
    }
}
