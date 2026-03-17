<?php

/**
 * Конфигурация пакета maksde/support
 *
 * Публикация: php artisan vendor:publish --tag="support-config"
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Форматы для возврата данных
    |--------------------------------------------------------------------------
    */

    'view' => [
        'format' => [
            'date' => 'Y-m-d',           // Формат даты (2024-12-31)
            'time' => 'H:i:s',           // Формат времени (23:59:59)
            'datetime' => 'Y-m-d H:i:s', // Формат даты и времени
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Форматы для возврата данных через API
    |--------------------------------------------------------------------------
    */

    'api' => [
        'format' => [
            'date' => 'Y-m-d',              // Формат даты для API
            'time' => 'H:i:s',              // Формат времени для API
            'datetime' => 'Y-m-d\TH:i:s\Z', // Формат даты-времени для API (ISO 8601 UTC)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Форматы для хранения в БД (всегда UTC)
    |--------------------------------------------------------------------------
    */

    'storage' => [
        'format' => [
            'date' => 'Y-m-d',
            'time' => 'H:i:s',
            'datetime' => 'Y-m-d H:i:s',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Настройки валидации
    |--------------------------------------------------------------------------
    */

    'validate' => [

        /*
        | Форматы даты и времени
        | Используются: DateValidate, TimeValidate, DateTimeValidate
        */

        'format' => [
            'date' => 'Y-m-d',           // Формат даты (2024-12-31)
            'time' => 'H:i:s',           // Формат времени (23:59:59)
            'datetime' => 'Y-m-d H:i:s', // Формат даты и времени
        ],

        /*
        | Ограничения длины
        */

        'length' => [
            'name' => 50,                      // Максимальная длина имени (NameValidate)
            'comment' => 1000,                 // Максимальная длина комментария
            'phone' => 11,                     // Кол-во цифр в российском номере (PhoneValidate)
            'phone_international_min' => 7,    // Минимум цифр в международном номере (PhoneInternationalValidate)
            'phone_international_max' => 15,   // Максимум цифр в международном номере (PhoneInternationalValidate)
        ],

        /*
        | Валидация файлов
        | Параметры можно переопределить: new ImageValidate(extensions: ['jpg'], maxSize: 5120)
        */

        'file' => [

            // Изображения (ImageValidate)
            'image' => [
                'extensions' => ['jpg', 'jpeg', 'png', 'heic', 'webp'],
                'mimes' => ['image/jpeg', 'image/png', 'image/heic', 'image/webp'],
                'max_size' => 10240, // KB (10 MB)
            ],

            // Видео (VideoValidate)
            'video' => [
                'extensions' => ['mp4', 'webm', 'hevc'],
                'mimes' => ['video/mp4', 'video/webm', 'video/h265'],
                'max_size' => 20480, // KB (20 MB)
            ],

            // Документы (DocumentValidate)
            'document' => [
                'extensions' => ['docx', 'xlsx', 'pdf'],
                'mimes' => [
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',       // .xlsx
                    'application/pdf',                                                         // .pdf
                ],
                'max_size' => 10240, // KB (10 MB)
            ],
        ],
    ],
];
