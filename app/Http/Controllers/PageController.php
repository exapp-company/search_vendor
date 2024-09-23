<?php

namespace App\Http\Controllers;

use App\DTO\ProductSearch;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\Collections\SearchCollection;
use App\Http\Resources\PageCollection;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Services\Search\SearchService;
use Illuminate\Http\Request;
use Meilisearch\Contracts\SearchQuery;

class PageController extends Controller
{

    public function __construct(public SearchService $searchService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->paginate(30);
        return new PageCollection($pages->load('city'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
        $page = Page::create($request->validated());
        $page->load('city');
        return new PageResource($page);
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        return new PageResource($page->load('city'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $page->fill($request->validated());
        $page->save();
        $page->load('city');
        return new PageResource($page);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return response()->noContent();
    }
    public function getProducts(Request $request, Page $page)
    {
        $search_query = new ProductSearch($request->all());
        if (!$page->is_published) {
            return response("Страница не найдена", 404);
        }
        $search_query->query = $page->query;
        $search_query->city_id = $page->city_id ?? $request->input('city_id', -1);
        $products = $this->searchService->searchProducts($search_query);
        $products = $products->paginate($request->input('count', 30));
        $products->load('shop');
        $products->load('stocks.office.city');
        $response = new SearchCollection($products);
        $response->additional(['page' => [
            'title' => $page->title,
            'query' => $page->query,
            'pagetitle' => $page->pagetitle,
            'description' => $page->description,
            'introtext' => $page->introtext
        ]]);
        return $response;
    }
}
