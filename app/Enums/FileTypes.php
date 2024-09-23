<?php

namespace App\Enums;

use App\Traits\Enumerable;

enum FileTypes
{
    use Enumerable;
    const png = 'image/png';
    const jpeg = 'image/jpeg';
    const gif = 'image/gif';
    const svg = 'image/svg';


}
