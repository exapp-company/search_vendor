<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Shop;
use Illuminate\Http\Request;

class ChatController extends ApiController
{
    public function index()
    {
        // Здесь можно вернуть список чатов, если это необходимо
    }

    public function activateToggle(Shop $shop)
    {
        if (!$this->shopExists($shop)) {
            return $this->error(__('Магазин не найден.'));
        }

        if ($this->chatExists($shop)) {
            $shop->chat->is_active = !$shop->chat->is_active;
            $shop->chat->save();
            $message = $shop->chat->is_active ? __('Chat activated.') : __('Chat deactivated.');
            return $this->success($message);
        }

        $chat = $shop->chat()->create(['is_active' => true]);
        if ($chat) {
            return $this->success(__('Chat activated.'));
        }

        return $this->error(__('Не удалось активировать чат.'));
    }

    private function shopExists(Shop $shop): bool
    {
        return Shop::query()->find($shop->id) !== null;
    }

    private function chatExists(Shop $shop): bool
    {
        return $shop->chat !== null;
    }
}
