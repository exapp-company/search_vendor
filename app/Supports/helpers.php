<?php


if (!function_exists('apiRoutes')) {
    function apiRoutes($version, $path, $returnClosure = true)
    {
        if ($returnClosure) {
            return function () use ($version, $path) {
                require base_path("routes/api/v{$version}/{$path}.php");
            };
        } else {
            require base_path("routes/api/v{$version}/{$path}.php");
        }
    }
}

if (!function_exists('getRequestIp')) {
    function getRequestIp(): array|string|null
    {
        $ip = request()->header('CF-Connecting-IP');

        if (!$ip) {
            $ip = request()->ip();
        }

        return $ip;
    }
}
