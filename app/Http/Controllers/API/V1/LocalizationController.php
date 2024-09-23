<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocalizationController extends ApiController
{
    public function __invoke(Request $request)
    {
        $locale = $request->input('locale');
        $supportedLocales = config('app.locales');

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            return $this->success(__('Language changed successfully'));
        }

        $defaultLocale = config('app.locale');
        App::setLocale($defaultLocale);

        return $this->error(__('Unsupported language'));

    }
}

