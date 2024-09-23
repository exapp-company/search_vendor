<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\FeedRequest;
use App\Http\Resources\FeedResource;
use App\Http\Resources\ShopResource;
use App\Models\Feed;
use App\Models\Shop;
use App\Repositories\FeedRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedController extends ApiController
{

    public function __construct(
        protected FeedRepository $feedRepository,
    ) {}


    public function store(FeedRequest $request)
    {
        $shop = Shop::find($request->shop_id);
        $data = $request->validated();
        if (!isset($data['status_id'])) {
            $data['status_id'] = 1;
        }
        $feed = $this->feedRepository->create($shop, $data);
        return new FeedResource($feed->load('status'));
    }


    public function show(Feed $feed)
    {
        return new FeedResource($feed->load('status'));
    }


    public function update(FeedRequest $request, Feed $feed)
    {
        $this->feedRepository->update($feed, $request->validated());
        return new FeedResource($feed->load('status'));
    }

    public function destroy(Feed $feed)
    {
        if ($feed->delete()) {
            return $this->success(__('Документ успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении объекта.'), HttpStatus::internalServerError);
        }
    }

    public function updateShopFeed(Request $request, $type, $id)
    {
        $model = Model::getActualClassNameForMorph($type);
        $instanace = $model::findOrFail($id);
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (Str::startsWith($key, "feed_")) {
                $instanace->$key = $value;
            }
        }
        // $instanace->feed_url = $request->input('feed_url');
        // $instanace->feed_mapping = $request->input('feed_mapping');
        $instanace->save();
        return new ShopResource($instanace->load('city', 'supplier'));
    }
}
