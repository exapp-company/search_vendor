<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\FeedStatusRequest;
use App\Http\Resources\FeedStatusResource;
use App\Models\FeedStatus;

class FeedStatusController extends ApiController
{

    public function index()
    {
        return FeedStatusResource::collection(FeedStatus::all());
    }


    public function store(FeedStatusRequest $request)
    {
        $status = FeedStatus::create($request->validated());
        return new FeedStatusResource($status);
    }


    public function show(FeedStatus $feedStatus)
    {
        return new FeedStatusResource($feedStatus);
    }


    public function update(FeedStatusRequest $request, FeedStatus $feedStatus)
    {
        $feedStatus->fill($request->validated());
        $feedStatus->save();
        return new FeedStatusResource($feedStatus);
    }


    public function destroy(FeedStatus $feedStatus)
    {
        if ($feedStatus->delete()) {
            return $this->success(__('Статус успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении объекта.'), HttpStatus::internalServerError);
        }
    }
}
