<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\FeedRequest;
use App\Http\Resources\Collections\FeedCollection;
use App\Http\Resources\FeedResource;
use App\Models\Feed;
use App\Models\Shop;
use App\Repositories\FeedRepository;


class FeedController extends ApiController
{

    public function __construct(
        protected FeedRepository $feedRepository,
    )
    {
    }

    public function index()
    {
        return new FeedCollection(Feed::with(['status', 'shop', 'shop.supplier'])->paginate(30));
    }


    public function store(FeedRequest $request)
    {
        $shop = Shop::find($request->shop_id);
        $feed = $this->feedRepository->create($shop, $request->validated());
        return new FeedResource($feed->load(['status', 'shop']));
    }


    public function show(Feed $feed)
    {
        return new FeedResource($feed);
    }


    public function update(FeedRequest $request, Feed $feed)
    {
        $this->feedRepository->update($feed, $request->validated());
        return new FeedResource($feed);
    }

    public function destroy(Feed $feed)
    {
        if ($feed->delete()) {
            return $this->success(__('Документ успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении объекта.'), HttpStatus::internalServerError);
        }
    }
}
