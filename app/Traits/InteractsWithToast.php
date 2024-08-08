<?php

namespace App\Traits;

trait InteractsWithToast
{
    protected function info($message): void
    {
        $this->dispatch('toast-message', [
            'style' => 'info',
            'message' => $message,
        ]);
    }

    protected function success($message): void
    {
        $this->dispatch('toast-message', [
            'style' => 'success',
            'message' => $message,
        ]);
    }

    protected function warning($message): void
    {
        $this->dispatch('toast-message', [
            'style' => 'warning',
            'message' => $message,
        ]);
    }

    protected function danger($message): void
    {
        $this->dispatch('toast-message', [
            'style' => 'error',
            'message' => $message,
        ]);
    }
}
