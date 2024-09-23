<?php

namespace App\Traits;


use App\Models\Log;

trait Loggable
{

    protected function infoLog($action, $dataType, $message, $context = []): void
    {
        $this->log('info', $action, $dataType, $message, $context);
    }

    protected function warningLog($action, $dataType, $message, $context = []): void
    {
        $this->log('warning', $action, $dataType, $message, $context);
    }

    protected function errorLog($action, $dataType, $message, $context = []): void
    {
        $this->log('error', $action, $dataType, $message, $context);
    }

    private function log($level, $action, $dataType, $message, $context = []): void
    {
        Log::create([
            'level' => $level,
            'action' => $action,
            'data_type' => $dataType,
            'message' => $message,
            'context' => json_encode($context)
        ]);

    }
}
