<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class VideoValidate implements ValidationRule
{
    /**
     * @param  array<string>|null  $extensions  Разрешенные расширения
     * @param  array<string>|null  $mimes  Разрешенные MIME-типы
     * @param  int|null  $maxSize  Максимальный размер в килобайтах
     */
    public function __construct(
        protected ?array $extensions = null,
        protected ?array $mimes = null,
        protected ?int $maxSize = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Если значение не является файлом, пропускаем валидацию
        if (! $value instanceof UploadedFile) {
            return;
        }

        // Получаем настройки из конфига или используем переданные
        $extensions = $this->extensions ?? config('support.validate.file.video.extensions', ['mp4', 'webm', 'hevc']);
        $mimes = $this->mimes ?? config('support.validate.file.video.mimes', ['video/mp4', 'video/webm', 'video/h265']);
        $maxSize = $this->maxSize ?? config('support.validate.file.video.max_size', 20480);

        // Проверка расширения
        $clientExtension = strtolower($value->getClientOriginalExtension());
        if (! in_array($clientExtension, $extensions, true)) {
            $fail($attribute, __('support::support.validate.errors.video.extension', [
                'extensions' => implode(', ', array_map('strtoupper', $extensions)),
            ]));

            return;
        }

        // Проверка MIME-типа
        $mimeType = $value->getMimeType();
        if (! in_array($mimeType, $mimes, true)) {
            $fail($attribute, __('support::support.validate.errors.video.mime'));

            return;
        }

        // Проверка размера (в килобайтах)
        $fileSize = $value->getSize() / 1024;
        if ($fileSize > $maxSize) {
            $fail($attribute, __('support::support.validate.errors.video.size', [
                'max' => $maxSize / 1024,
            ]));

            return;
        }
    }
}
