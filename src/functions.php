<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('urlFront')) {
    /**
     * Формирование полного пути файла со storage
     *
     * @param string|null $path Путь файла (пример пути файла сохраненного в бд 'folder/image.png')
     * @return string|null Полный путь с протоколом и доменом (вернет 'https://localhost/storage/folder/image.png')
     */
    function urlFront(?string $path = null): ?string
    {
        return empty($path) ? null : url(Storage::url($path));
    }
}
