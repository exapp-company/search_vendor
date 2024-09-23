<?php

namespace App\Enums;

enum HttpStatus
{
    const ok = 200;
    const created = 201;
    const accepted = 202;
    const noContent = 204;
    const badRequest = 400;
    const unauthorized = 401;
    const forbidden = 403;
    const notFound = 404;
    const unprocessableEntity = 422;
    const internalServerError = 500;
}
