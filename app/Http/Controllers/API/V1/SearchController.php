<?php

namespace App\Http\Controllers\API\V1;

use App\DTO\ProductSearch;
use App\Enums\HttpStatus;
use App\Enums\LogAction;
use App\Events\QueryCompleted;
use App\Http\Controllers\ApiController;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\Collections\SearchCollection;
use App\Models\Product;
use App\Models\Suggest;
use App\Services\Search\SearchService;
use App\Traits\Loggable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends ApiController
{

    use Loggable;


    public function __construct(
        protected SearchService $searchService
    ) {}


    public function search(SearchRequest $request)
    {

        $searchParams = new ProductSearch($request->all());
        $results = $this->searchService->searchProducts($searchParams);


        $results = $results->paginate((int)$request->input('count', 30));
        $results->load('shop');
        $results->load('stocks.office.city');
        QueryCompleted::dispatch($searchParams->query, $results->total());

        return new SearchCollection($results);
        // try {

        // } catch (Exception $e) {
        //     return $this->error(__('Произошла ошибка во время поиска'), HttpStatus::internalServerError);
        // }
    }


    public function quickSearch(Request $request)
    {
        $searchParams = new ProductSearch(
            $request->all()
        );
        if ($searchParams->query) {

            $results = $this->searchService->quickSearchProducts($searchParams, 5);
        } else {

            $results = Suggest::orderBy('count', 'desc')->select('query')->take(5)->get()->pluck('query');
        }


        return $results; //new SearchCollection($results);
    }
}
