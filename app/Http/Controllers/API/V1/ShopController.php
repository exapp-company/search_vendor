<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatus;
use App\Filters\ShopFilter;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\Collections\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Jobs\ImportProductJob;
use App\Models\Shop;
use App\Repositories\ShopRepository;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShopController extends ApiController
{

    public function __construct(
        protected ShopRepository $shopRepository,
        public StatusService     $statusService
    )
    {
    }

    public function index(Request $request, ShopFilter $shopFilter)
    {
        $shops = Shop::with(['supplier', 'offices.city'])->filter($shopFilter);
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $shops = $shops->where('supplier_id', $user->id);
        }
        $shops = $shops->get();
        return new ShopCollection($shops->load('offices.city', 'city', 'supplier'));
    }

    public function store(ShopRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $shop = $this->shopRepository->create($request->user(), $request->validated());
            $shop->chat()->create();

            return new ShopResource($shop->load('offices', 'files', 'chat'));
        });
    }


    public function show(Shop $shop)
    {
        return new ShopResource($shop->load(['feeds', 'feeds.status', 'offices.city', 'files']));
    }

    public function update(ShopRequest $request, Shop $shop)
    {
        $shop = $this->shopRepository->update($shop, $request->validated());
        return new ShopResource($shop->load('files'));
    }

    public function uploadLogo(Request $request, Shop $shop)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), HttpStatus::unprocessableEntity);
        }

        try {
            $this->shopRepository->uploadLogo($shop, $request->file('logo'));
            return $this->success('Logo uploaded successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error('File upload failed', HttpStatus::unprocessableEntity);
        }
    }


    public function destroy(Shop $shop)
    {
        $shop->offices()->delete();
        $shop->stocks()->delete();
        $shop->products()->unsearchable();
        $shop->products()->delete();
        $shop->delete();
        //$shop->offices()->delete();
        //
        return response()->noContent();
    }

    public function startImport(Shop $shop)
    {
        ImportProductJob::dispatch($shop);
        return response()->noContent();
    }

    public function refreshIndex(Shop $shop)
    {
        $shop->products()->searchable();
        return response()->noContent();
    }
}
