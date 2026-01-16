<?php

namespace Maksde\Support\Tests\Unit;

use Maksde\Support\Contracts\Validation\EmailValidate;
use Maksde\Support\Tests\TestCase;

/**
 * Тесты валидации email с регулярным выражением
 *
 * Регулярное выражение: /^(?!\.)(?!.*\.\.)([A-Za-zА-Яа-яЁё0-9_'+\-\.]*)([A-Za-zА-Яа-яЁё0-9_+-])@([A-Za-zА-Яа-яЁё0-9][A-Za-zА-Яа-яЁё0-9\-]*\.)+[A-Za-zА-Яа-яЁё]{2,}$/u
 */
class EmailValidateTest extends TestCase
{
    /**
     * Провайдер валидных email адресов
     */
    public static function validEmailsProvider(): array
    {
        return [
            // ========== Базовые латинские ==========
            'simple' => ['user@domain.com'],
            'with subdomain' => ['user@mail.domain.com'],
            'multiple subdomains' => ['user@mail.example.domain.com'],

            // ========== С точками в локальной части ==========
            'with dot in local' => ['user.name@domain.com'],
            'multiple dots in local' => ['first.middle.last@domain.com'],
            'long email with dots' => ['long.email.address@domain.com'],

            // ========== С подчеркиванием ==========
            'with underscore' => ['user_name@domain.com'],
            'multiple underscores' => ['user_name_test@domain.com'],
            'double underscore' => ['user__name@domain.com'], // Двойное подчеркивание РАЗРЕШЕНО
            'underscore at start' => ['_user@domain.com'], // Подчеркивание в начале РАЗРЕШЕНО
            'underscore at end' => ['user_@domain.com'], // Подчеркивание в конце РАЗРЕШЕНО

            // ========== С дефисом ==========
            'with dash in local' => ['user-name@domain.com'],
            'with dash in domain' => ['user@test-domain.com'],
            'double dash in local' => ['user--name@domain.com'], // Двойной дефис в локальной части РАЗРЕШЕН
            'double dash in domain' => ['user@my--domain.com'], // Двойной дефис в домене РАЗРЕШЕН
            'dash at start local' => ['-user@domain.com'], // Дефис в начале локальной части РАЗРЕШЕН
            'dash at end local' => ['user-@domain.com'], // Дефис в конце локальной части РАЗРЕШЕН

            // ========== С цифрами ==========
            'with numbers in local' => ['user123@domain.com'],
            'with numbers in domain' => ['user@domain123.com'],
            'only numbers in local' => ['12345@domain.com'],
            'with numbers dots dashes' => ['user_1.2-3@domain.com'],

            // ========== С апострофом и плюсом ==========
            'with apostrophe' => ["user'name@domain.com"], // Апостроф РАЗРЕШЕН
            'with plus' => ['user+tag@domain.com'],
            'with plus and apostrophe' => ["user+tag'test@domain.com"],
            'plus in local' => ['user+domain@domain.com'],

            // ========== Комбинации разрешенных символов ==========
            'dot underscore' => ['user._name@domain.com'], // Точка и подчеркивание
            'dot dash' => ['user.-name@domain.com'], // Точка и дефис
            'dash underscore' => ['user-_name@domain.com'], // Дефис и подчеркивание
            'dot underscore dash' => ['user._-name@domain.com'], // Все вместе

            // ========== Кириллица ==========
            'cyrillic simple' => ['имя@домен.рф'],
            'cyrillic with dot' => ['имя.фамилия@почта.рф'],
            'cyrillic full' => ['пользователь@почтовый.сервис.рф'],
            'cyrillic with yo' => ['ёжик@домён.рф'],
            'mixed latin cyrillic local' => ['user123@домен.рф'],
            'mixed latin cyrillic domain' => ['пользователь@domain.com'],
            'cyrillic with special chars' => ['имя_1.2-3@домен.рф'], // Кириллица + цифры + спецсимволы

            // ========== Длинные и короткие TLD ==========
            'long tld' => ['user@domain.museum'],
            'short tld' => ['user@domain.ru'],
            'uk domain' => ['test.user@example.co.uk'],
            'very long domain name' => ['user@veryverylongdomainname.com'],
            'very long username' => ['verylongusername@domain.com'],

            // ========== Валидные кириллические TLD ==========
            'cyrillic tld rf' => ['user@domain.рф'],
            'cyrillic tld bel' => ['user@domain.бел'],
            'cyrillic tld srb' => ['user@domain.срб'],
            'cyrillic tld ukr' => ['user@domain.укр'],
            'cyrillic tld rus' => ['user@domain.рус'],

            // ========== Сложные комбинации ==========
            'complex' => ['user.name+tag123@mail.subdomain.domain.com'],
            'with trailing underscore' => ['user_@domain.com'],
            'with trailing dash' => ['user-@domain.com'],
            'with trailing plus' => ['user+@domain.com'],

            // ========== Граничные случаи длины (валидные) ==========
            'max email length' => [str_repeat('a', 64).'@'.str_repeat('b', 186).'.com'], // 255 символов (64+1+186+4)
            'max local length' => [str_repeat('a', 64).'@domain.com'], // 64 символа в локальной части
            'max domain length' => ['a@'.str_repeat('b', 249).'.com'], // 253 символа в домене (249+4)
        ];
    }

    /**
     * Провайдер невалидных email адресов
     */
    public static function invalidEmailsProvider(): array
    {
        return [
            // ========== Отсутствие обязательных частей ==========
            'no at' => ['userdomain.com'],
            'double at' => ['user@@domain.com'],
            'no local' => ['@domain.com'],
            'no domain' => ['user@'],
            'no tld' => ['user@domain'],
            'empty' => [''],
            'only at' => ['@'],
            'at in domain' => ['user@my@domain.com'], // @ в домене

            // ========== Проблемы с точками (регулярка (?!\.) и (?!.*\.\.) ) ==========
            'starts with dot' => ['.user@domain.com'],
            'ends with dot in local' => ['user.@domain.com'],
            'double dots in local' => ['user..name@domain.com'],
            'double dots in domain' => ['user@domain..com'],
            'domain starts with dot' => ['user@.domain.com'],
            'triple dots in local' => ['user...name@domain.com'],
            'triple dots in domain' => ['user@my...domain.com'],
            'email ends with dot' => ['user@domain.com.'], // Заканчивается точкой

            // ========== Проблемы с дефисами ==========
            'domain starts with dash' => ['user@-domain.com'], // Домен начинается с дефиса
            'domain part ends with dash' => ['user@domain-.com'], // Часть домена заканчивается дефисом
            'tld ends with dash' => ['user@domain.com-'], // TLD заканчивается дефисом
            'domain part after dot starts with dash' => ['user@my.-domain.com'], // Часть домена после точки начинается с дефиса

            // ========== Проблемы с подчеркиванием в домене ==========
            'underscore in domain' => ['user@_domain.com'], // Домен начинается с подчеркивания
            'underscore in domain part' => ['user@domain_.com'], // Подчеркивание в части домена
            'underscore before tld' => ['user@domain._com'], // Подчеркивание перед TLD
            'tld ends with underscore' => ['user@domain.com_'], // TLD заканчивается подчеркиванием
            'double underscore in domain' => ['user@my__domain.com'], // Двойное подчеркивание в домене
            'mixed underscore dash in domain' => ['user@my_-domain.com'], // Подчеркивание и дефис в домене
            'mixed dot underscore in domain' => ['user@my._domain.com'], // Точка и подчеркивание в домене
            'mixed dot underscore dash in domain' => ['user@my._-domain.com'], // Все вместе в домене

            // ========== Запрещенные символы в локальной части ==========
            'with exclamation' => ['use!r@domain.com'],
            'with hash' => ['use#r@domain.com'],
            'with dollar' => ['use$r@domain.com'],
            'with percent' => ['use%r@domain.com'],
            'with caret' => ['use^r@domain.com'],
            'with ampersand' => ['use&r@domain.com'],
            'with star' => ['use*r@domain.com'],
            'with backslash' => ['use\\r@domain.com'],
            'with pipe' => ['use|r@domain.com'],
            'with slash' => ['use/r@domain.com'],
            'with open paren' => ['use(r@domain.com'],
            'with close paren' => ['use)r@domain.com'],
            'with open brace' => ['use{r@domain.com'],
            'with close brace' => ['use}r@domain.com'],
            'with less than' => ['use<r@domain.com'],
            'with greater than' => ['use>r@domain.com'],
            'with open bracket' => ['use[r@domain.com'],
            'with close bracket' => ['use]r@domain.com'],
            'with comma' => ['use,r@domain.com'],
            'with colon' => ['use:r@domain.com'],
            'with semicolon' => ['use;r@domain.com'],
            'with double quote' => ['use""r@domain.com'],
            'with backtick' => ['use`r@domain.com'],
            'with question' => ['user?name@domain.com'],

            // ========== Запрещенные символы в домене ==========
            'plus in domain' => ['user@do+main.com'], // Плюс в домене ЗАПРЕЩЕН

            // ========== Пробелы ==========
            'space in local' => ['use r@domain.com'],
            'spaces in local' => ['user name@domain.com'],
            'spaces in domain' => ['user@dom ain.com'],

            // ========== Неверная структура TLD (минимум 2 буквы, только буквы) ==========
            'tld too short' => ['user@domain.c'], // TLD из 1 буквы
            'tld with numbers' => ['user@domain.c0m'], // Цифры в TLD ЗАПРЕЩЕНЫ
            'tld with dash' => ['user@domain.co-m'], // Дефис в TLD ЗАПРЕЩЕН
            'tld with underscore' => ['user@domain.co_m'], // Подчеркивание в TLD ЗАПРЕЩЕНО
            'tld only numbers' => ['user@domain.123'], // Только цифры в TLD
            'tld with numbers at end' => ['user@domain.xyz123'], // Цифры в конце TLD

            // ========== Несуществующие TLD (проверка через DomainZone) ==========
            'invalid tld fake' => ['user@domain.fake'],
            'invalid tld test' => ['user@domain.test'],
            'invalid tld invalid' => ['user@domain.invalid'],
            'invalid cyrillic tld' => ['user@domain.абв'],
            'tld single letter a' => ['user@mail.a'], // TLD .a не существует
            'tld single letter after com' => ['user@mail.com.a'], // TLD .a не существует

            // ========== Множественные проблемы ==========
            'multiple at symbols' => ['user@test@domain.com'],
            'dot and space' => ['user. name@domain.com'],
            'starts and ends with dot' => ['.user.@domain.com'],

            // ========== Превышение длины ==========
            'total too long' => [str_repeat('a', 246).'@test.com'], // 256 символов
            'local too long' => [str_repeat('a', 65).'@domain.com'], // 65 символов в локальной части
            'local 65 chars specific' => ['user567890123456789012345678901234567890123456789012345678901234567890@domain.ru'], // 65 символов
            'domain too long' => ['a@'.str_repeat('b', 250).'.com'], // 254 символа в домене (250+4)
        ];
    }

    /**
     * @dataProvider validEmailsProvider
     */
    public function test_valid_emails(string $email): void
    {
        $validator = new EmailValidate;
        $this->assertValid($validator, $email);
    }

    /**
     * @dataProvider invalidEmailsProvider
     */
    public function test_invalid_emails(string $email): void
    {
        $validator = new EmailValidate;
        $this->assertInvalid($validator, $email);
    }

    /**
     * Тест на обрезку пробелов (trim)
     */
    public function test_trims_spaces(): void
    {
        $validator = new EmailValidate;

        // Email с пробелами по краям должен быть обрезан и валиден
        $this->assertValid($validator, '  user@domain.com  ');
        $this->assertValid($validator, "\tuser@domain.com\n");
        $this->assertValid($validator, " \t user@domain.com \n ");
    }

    /**
     * Тест проверки регулярного выражения на начало с точки (?!\.)
     */
    public function test_regex_not_start_with_dot(): void
    {
        $validator = new EmailValidate;

        $this->assertInvalid($validator, '.user@domain.com');
        $this->assertInvalid($validator, '..user@domain.com');
        $this->assertValid($validator, 'user@domain.com');
        $this->assertValid($validator, 'u.ser@domain.com');
    }

    /**
     * Тест проверки регулярного выражения на двойные точки (?!.*\.\.)
     */
    public function test_regex_no_double_dots(): void
    {
        $validator = new EmailValidate;

        $this->assertInvalid($validator, 'user..name@domain.com');
        $this->assertInvalid($validator, 'user@domain..com');
        $this->assertInvalid($validator, 'user...name@domain.com');
        $this->assertValid($validator, 'user.name@domain.com');
        $this->assertValid($validator, 'first.middle.last@domain.com');
    }

    /**
     * Тест проверки локальной части ([A-Za-zА-Яа-яЁё0-9_'+\-\.]*)
     */
    public function test_regex_local_part_characters(): void
    {
        $validator = new EmailValidate;

        // Валидные символы в локальной части
        $this->assertValid($validator, 'user123@domain.com');
        $this->assertValid($validator, 'user_name@domain.com');
        $this->assertValid($validator, 'user-name@domain.com');
        $this->assertValid($validator, 'user.name@domain.com');
        $this->assertValid($validator, "user'name@domain.com");
        $this->assertValid($validator, 'user+tag@domain.com');
        $this->assertValid($validator, 'пользователь@domain.com');
        $this->assertValid($validator, 'ёжик@domain.com');

        // Невалидные символы в локальной части
        $this->assertInvalid($validator, 'user#name@domain.com');
        $this->assertInvalid($validator, 'user@name@domain.com');
        $this->assertInvalid($validator, 'user*name@domain.com');
    }

    /**
     * Тест проверки что локальная часть НЕ заканчивается точкой ([A-Za-zА-Яа-яЁё0-9_+-])
     */
    public function test_regex_local_part_not_ends_with_dot(): void
    {
        $validator = new EmailValidate;

        $this->assertInvalid($validator, 'user.@domain.com');
        $this->assertInvalid($validator, 'user.name.@domain.com');
        $this->assertValid($validator, 'user_@domain.com');
        $this->assertValid($validator, 'user-@domain.com');
        $this->assertValid($validator, 'user+@domain.com');
    }

    /**
     * Тест проверки доменной части ([A-Za-zА-Яа-яЁё0-9][A-Za-zА-Яа-яЁё0-9\-]*\.)+
     */
    public function test_regex_domain_part(): void
    {
        $validator = new EmailValidate;

        // Валидные домены
        $this->assertValid($validator, 'user@domain.com');
        $this->assertValid($validator, 'user@sub.domain.com');
        $this->assertValid($validator, 'user@test-domain.com');
        $this->assertValid($validator, 'user@домен.рф');
        $this->assertValid($validator, 'user@123domain.com');

        // Невалидные домены
        $this->assertInvalid($validator, 'user@-domain.com'); // начинается с дефиса
        $this->assertInvalid($validator, 'user@domain-.com'); // заканчивается дефисом
        $this->assertInvalid($validator, 'user@.domain.com'); // начинается с точки
    }

    /**
     * Тест проверки TLD ([A-Za-zА-Яа-яЁё]{2,})
     */
    public function test_regex_tld(): void
    {
        $validator = new EmailValidate;

        // Валидные TLD (формат)
        $this->assertValid($validator, 'user@domain.com');
        $this->assertValid($validator, 'user@domain.ru');
        $this->assertValid($validator, 'user@domain.рф');
        $this->assertValid($validator, 'user@domain.museum');
        $this->assertValid($validator, 'user@domain.co');

        // Невалидные TLD (формат)
        $this->assertInvalid($validator, 'user@domain.c'); // только 1 символ
        $this->assertInvalid($validator, 'user@domain.c0m'); // с цифрой
        $this->assertInvalid($validator, 'user@domain.co-m'); // с дефисом
    }

    /**
     * Тест проверки валидности TLD через DomainZone
     */
    public function test_tld_validation_via_domainzone(): void
    {
        $validator = new EmailValidate;

        // Валидные существующие TLD
        $this->assertValid($validator, 'user@domain.com');
        $this->assertValid($validator, 'user@domain.net');
        $this->assertValid($validator, 'user@domain.org');
        $this->assertValid($validator, 'user@domain.io');
        $this->assertValid($validator, 'user@domain.dev');
        $this->assertValid($validator, 'user@domain.app');
        $this->assertValid($validator, 'user@domain.ru');
        $this->assertValid($validator, 'user@domain.uk');
        $this->assertValid($validator, 'user@domain.de');
        $this->assertValid($validator, 'user@domain.fr');

        // Валидные кириллические TLD
        $this->assertValid($validator, 'user@domain.рф');
        $this->assertValid($validator, 'user@domain.бел');
        $this->assertValid($validator, 'user@domain.срб');
        $this->assertValid($validator, 'user@domain.укр');
        $this->assertValid($validator, 'user@domain.рус');

        // Невалидные несуществующие TLD
        $this->assertInvalid($validator, 'user@domain.fake');
        $this->assertInvalid($validator, 'user@domain.invalid');
        $this->assertInvalid($validator, 'user@domain.notexist');
        $this->assertInvalid($validator, 'user@domain.абв');
        $this->assertInvalid($validator, 'user@domain.xyz123');
    }

    /**
     * Тест проверки длины email
     */
    public function test_length_limits(): void
    {
        $validator = new EmailValidate;

        // Максимальная общая длина: 255
        $validMaxEmail = str_repeat('a', 64).'@'.str_repeat('b', 186).'.com'; // 255 символов (64+1+186+4)
        $invalidMaxEmail = str_repeat('a', 64).'@'.str_repeat('b', 187).'.com'; // 256 символов (64+1+187+4)
        $this->assertValid($validator, $validMaxEmail);
        $this->assertInvalid($validator, $invalidMaxEmail);

        // Максимальная длина локальной части: 64
        $validMaxLocal = str_repeat('a', 64).'@domain.com';
        $invalidMaxLocal = str_repeat('a', 65).'@domain.com';
        $this->assertValid($validator, $validMaxLocal);
        $this->assertInvalid($validator, $invalidMaxLocal);

        // Максимальная длина доменной части: 253
        $validMaxDomain = 'a@'.str_repeat('b', 249).'.com'; // 253 символа в домене (249+4)
        $invalidMaxDomain = 'a@'.str_repeat('b', 250).'.com'; // 254 символа в домене (250+4)
        $this->assertValid($validator, $validMaxDomain);
        $this->assertInvalid($validator, $invalidMaxDomain);
    }
}
