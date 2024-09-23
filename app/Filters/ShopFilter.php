<?php

namespace App\Filters;

use Kblais\QueryFilter\QueryFilter;

class ShopFilter extends QueryFilter
{
    public function status($value)
    {
        return $this->where('status', $value);
    }
    public function progress($value)
    {
        return $this->where('import_progress', $value);
    }
}
