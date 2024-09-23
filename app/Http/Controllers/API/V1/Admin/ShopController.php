<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\Collections\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShopController extends ApiController
{
    public function __construct(
        protected ShopRepository $shopRepository,
    ) {
    }

    public function index()
    {
        return new ShopCollection(Shop::with(['feeds', 'supplier'])->paginate(30));
    }

    public function store(ShopRequest $request)
    {
        $user = User::findOrFail($request->supplier_id);
        $shop = $this->shopRepository->create($user, $request->validated());
        return new ShopResource($shop->load('files'));
    }

    public function show(Shop $shop)
    {
        return new ShopResource($shop->load(['feeds', 'feeds.status', 'supplier']));
    }

    public function update(ShopRequest $request, Shop $shop)
    {
        $shop = $this->shopRepository->update($shop, $request->validated());
        return new ShopResource($shop->load(['feeds', 'supplier']));
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
        if ($shop->delete()) {
            return $this->success(__('Магазин успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении объекта.'), HttpStatus::internalServerError);
        }
    }
}
