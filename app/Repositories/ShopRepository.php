<?php

namespace App\Repositories;

use App\Enums\ShopStatus;
use App\Models\File;
use App\Models\Office;
use App\Models\Shop;
use App\Models\User;
use App\Services\Files\UploadImage;
use App\Services\StatusService;
use Illuminate\Support\Str;

class ShopRepository
{

    public function __construct(public StatusService $statusService) {}
    public function create(User $user, array $data)
    {
        $shop = new Shop($data);
        if (!$user->isAdmin()) {
            $shop->supplier()->associate($user);
            $user->role = 'supplier';
            $user->save();
        }

        $shop->save();
        $office = Office::withoutEvents(function () use ($shop) {
            $office = new Office();
            $office->shop_id = $shop->id;
            $office->fill([
                'city_id' => $shop->city_id,
                'name' => $shop->name,
                'phone' => $shop->phone,
                'address' => $shop->address,
                'shop_id' => $shop->id
            ]);
            $this->statusService->changeStatus($office, ShopStatus::pending);
            return $office;
        });

        $this->statusService->changeStatus($shop, ShopStatus::pending);

        return $shop;
    }

    public function update(Shop $shop, array $data): Shop
    {
        $shop->fill($data);
        $shop->save();
        $this->statusService->changeStatus($shop, ShopStatus::pending);
        return $shop;
    }

    public function uploadLogo(Shop $shop, $file): void
    {


        $fileName = $shop->id . Str::uuid()->toString();

        $uploadImageService = new UploadImage();
        $path = $uploadImageService
            ->file($file)
            ->name($fileName)
            ->directory('shops/logos')
            ->resize(300, 300)
            ->store();

        $fileRecord = File::create([
            'name' => $uploadImageService->getName() ?? $file->getClientOriginalName(),
            'path' => $path,
            'type' => $file->getClientMimeType(),
        ]);

        $shop->update(['logo_id' => $fileRecord->id]);
    }
}
