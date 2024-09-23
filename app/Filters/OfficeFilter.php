<?php

namespace App\Filters;

use Kblais\QueryFilter\QueryFilter;

class OfficeFilter extends QueryFilter
{
    public function status($value)
    {
        return $this->where('status', $value);
    }
    public function shopId($value)
    {
        return $this->where('shop_id', $value);
    }
}
