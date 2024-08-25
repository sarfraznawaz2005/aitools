<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class NotificationManager
{
    private static string $tempFile = 'last_notification.json';

    public static function setLastNotification($windowName, $route, $routeParams = []): void
    {
        $data = json_encode(['window' => $windowName, 'route' => $route, 'routeParams' => $routeParams]);

        File::put(self::$tempFile, $data);
    }

    public static function getLastNotification(): ?array
    {
        if (File::exists(self::$tempFile)) {
            $data = File::get(self::$tempFile);

            return json_decode($data, true);
        }

        return null;
    }

    public static function clearLastNotification(): void
    {
        if (File::exists(self::$tempFile)) {
            File::delete(self::$tempFile);
        }
    }
}
