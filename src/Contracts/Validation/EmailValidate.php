<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Maksde\Support\DomainZone;

class EmailValidate implements ValidationRule
{
    /**
     * Максимальная длина E-mail
     */
    const MAX_EMAIL_LENGTH = 255;

    /**
     * Максимальная длина подписи E-mail
     */
    const MAX_NAME_LENGTH = 64;

    /**
     * Максимальная длина домена E-mail
     */
    const MAX_DOMAIN_LENGTH = 253;

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validate = true;
        $emailArray = explode('@', $value);

        // Проверка формата email
        if (count($emailArray) !== 2) {
            $validate = false;
        } else {
            [$name, $domain] = $emailArray;
            $domainArray = explode('.', $domain);
            $domainZone = end($domainArray);

            // Проверка запрещенных символов
            if (! $this->validateCharacters($value)) {
                $validate = false;
            }

            // Проверка формата имени и домена
            if (! $this->validateNameAndDomain($name, $domain)) {
                $validate = false;
            }

            // Проверка длины
            if (! $this->validateLengths($value, $name, $domain)) {
                $validate = false;
            }

            // Проверка зоны домена
            if (! DomainZone::isValidTLD($domainZone)) {
                $validate = false;
            }
        }

        if (! $validate) {
            $fail($attribute, __('support::support.validate.errors.email'));
        }
    }

    /**
     * @param  string  $email  E-mail
     * @return bool Прошла ли проверка
     */
    private function validateCharacters(string $email): bool
    {
        $pattern = '/[!#$%^&*\\\|\/\(\)\{\}<>[\],:;\'"`]/';

        return ! preg_match($pattern, $email);
    }

    /**
     * @param  string  $name  Подпись E-mail
     * @param  string  $domain  Домен E-mail
     * @return bool Прошла ли проверка
     */
    private function validateNameAndDomain(string $name, string $domain): bool
    {
        // Регулярное выражение для имени (разрешает буквы, цифры, '.', '_', '-', и '+')
        $namePattern = '/^[\p{L}0-9]+(?:[._+-][\p{L}0-9]+)*$/u';

        // Регулярное выражение для домена (разрешает только буквы, цифры, '.', '_', '-')
        $domainPattern = '/^[\p{L}0-9]+(?:[._-][\p{L}0-9]+)*$/u';

        return preg_match($namePattern, $name) === 1 && preg_match($domainPattern, $domain) === 1;
    }

    /**
     * @param  string  $email  E-mail
     * @param  string  $name  Подпись E-mail
     * @param  string  $domain  Домен E-mail
     * @return bool Прошла ли проверка
     */
    private function validateLengths(string $email, string $name, string $domain): bool
    {
        $emailCount = mb_strlen($email);
        $nameCount = mb_strlen($name);
        $domainCount = mb_strlen($domain);

        return $emailCount <= self::MAX_EMAIL_LENGTH &&
            $nameCount <= self::MAX_NAME_LENGTH &&
            $domainCount <= self::MAX_DOMAIN_LENGTH;
    }
}
