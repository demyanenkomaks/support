<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('urlFront')) {
    /**
     * Создание полного пути файла с протоколом и доменом для фронта
     */
    function urlFront(?string $path = null): ?string
    {
        return empty($path) ? null : url(Storage::url($path));
    }
}
