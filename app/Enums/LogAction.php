<?php

namespace App\Enums;

use App\Traits\Enumerable;

class LogAction
{
    use Enumerable;

    const create = 'create';
    const update = 'update';
    const delete = 'delete';
    const login = 'login';
    const search = 'search';


}
