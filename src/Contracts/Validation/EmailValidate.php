<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Maksde\Support\DomainZone;

/**
 * Валидация email адреса с поддержкой латиницы и кириллицы
 *
 * Регулярное выражение проверяет:
 *
 * 1. Структура:
 *    - (?!\.) - не должен начинаться с точки
 *    - (?!.*\.\.) - не должно быть двух точек подряд (в любом месте)
 *    - ([A-Za-zА-Яа-яЁё0-9_'+\-\.]*) - локальная часть может содержать:
 *      * латинские буквы (A-Z, a-z)
 *      * кириллические буквы (А-Я, а-я, Ё, ё)
 *      * цифры (0-9)
 *      * специальные символы: _ ' + - .
 *    - ([A-Za-zА-Яа-яЁё0-9_+-]) - локальная часть должна заканчиваться буквой, цифрой или символом _ + -
 *      (НЕ точкой)
 *    - @ - обязательный разделитель
 *    - ([A-Za-zА-Яа-яЁё0-9][A-Za-zА-Яа-яЁё0-9\-]*\.)+ - доменная часть:
 *      * должна начинаться с буквы или цифры
 *      * может содержать буквы, цифры и дефис
 *      * должна заканчиваться точкой (для разделения уровней домена)
 *      * может повторяться для поддоменов (example.mail.ru)
 *    - [A-Za-zА-Яа-яЁё]{2,} - доменная зона (TLD):
 *      * минимум 2 буквы
 *      * только буквы (латиница или кириллица)
 *
 * 2. Примеры валидных email:
 *    - user@domain.com
 *    - test.user@example.co.uk
 *    - user_123@domain.com
 *    - имя@домен.рф
 *    - user-name@mail.example.com
 *    - user'o+tag@domain.com
 *
 * 3. Примеры невалидных email:
 *    - .user@domain.com (начинается с точки)
 *    - user..name@domain.com (две точки подряд)
 *    - user.@domain.com (локальная часть заканчивается точкой)
 *    - user@domain (нет точки в домене)
 *    - user@@domain.com (два символа @)
 *    - @domain.com (нет локальной части)
 *    - user@.domain.com (домен начинается с точки)
 *    - user@domain-.com (дефис в конце части домена)
 *
 * 4. Дополнительные проверки:
 *    - Общая длина email ≤ 255 символов
 *    - Локальная часть ≤ 64 символа
 *    - Доменная часть ≤ 253 символа
 */
class EmailValidate implements ValidationRule
{
    /**
     * Максимальная длина E-mail
     */
    public const MAX_EMAIL_LENGTH = 255;

    /**
     * Максимальная длина локальной части E-mail (до @)
     */
    public const MAX_LOCAL_LENGTH = 64;

    /**
     * Максимальная длина доменной части E-mail (после @)
     */
    public const MAX_DOMAIN_LENGTH = 253;

    /**
     * Регулярное выражение для валидации email с поддержкой латиницы и кириллицы
     *
     * Паттерн для доменной части ([A-Za-zА-Яа-яЁё0-9]([A-Za-zА-Яа-яЁё0-9-]*[A-Za-zА-Яа-яЁё0-9])?\.)+
     * гарантирует, что каждая часть домена:
     * - Начинается с буквы или цифры
     * - Заканчивается буквой или цифрой (не дефисом)
     * - Может содержать дефисы только в середине
     */
    private const PATTERN = '/^(?!\.)(?!.*\.\.)([A-Za-zА-Яа-яЁё0-9_\'+\-\.]*)([A-Za-zА-Яа-яЁё0-9_+-])@([A-Za-zА-Яа-яЁё0-9]([A-Za-zА-Яа-яЁё0-9-]*[A-Za-zА-Яа-яЁё0-9])?\.)+[A-Za-zА-Яа-яЁё]{2,}$/u';

    /**
     * Выполнить валидацию
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Обрезаем пробелы
        $email = trim($value);

        // Проверка регулярным выражением
        if (! preg_match(self::PATTERN, $email)) {
            $fail($attribute, __('support::support.validate.errors.email.format'));

            return;
        }

        // Разделяем на локальную часть и домен
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            $fail($attribute, __('support::support.validate.errors.email.format'));

            return;
        }

        [$local, $domain] = $parts;

        // Проверка длины email
        if (mb_strlen($email) > self::MAX_EMAIL_LENGTH) {
            $fail($attribute, __('support::support.validate.errors.email.max_length', [
                'max' => self::MAX_EMAIL_LENGTH,
            ]));

            return;
        }

        // Проверка длины локальной части
        if (mb_strlen($local) > self::MAX_LOCAL_LENGTH) {
            $fail($attribute, __('support::support.validate.errors.email.local_max_length', [
                'max' => self::MAX_LOCAL_LENGTH,
            ]));

            return;
        }

        // Проверка длины доменной части
        if (mb_strlen($domain) > self::MAX_DOMAIN_LENGTH) {
            $fail($attribute, __('support::support.validate.errors.email.domain_max_length', [
                'max' => self::MAX_DOMAIN_LENGTH,
            ]));

            return;
        }

        // Проверка валидности TLD (доменной зоны)
        $domainParts = explode('.', $domain);
        $tld = end($domainParts);

        if (! DomainZone::isValidTLD($tld)) {
            $fail($attribute, __('support::support.validate.errors.email.invalid_tld', [
                'tld' => $tld,
            ]));

            return;
        }
    }
}
