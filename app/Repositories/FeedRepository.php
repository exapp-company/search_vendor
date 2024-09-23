<?php

namespace App\Repositories;

use App\Models\Feed;
use App\Models\Shop;

class FeedRepository
{
    public function create(Shop $shop, array $data)
    {
        return $shop->feeds()->create($data);
    }


    public function update(Feed $feed, array $data): Feed
    {
        $feed->fill($data);
        $feed->save();
        return $feed;
    }
}
