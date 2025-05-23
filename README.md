# Помощник для Laravel

[![Packagist Version](https://img.shields.io/packagist/v/maksde/support)](https://packagist.org/packages/maksde/support)
[![Packagist Downloads](https://img.shields.io/packagist/dt/maksde/support)](https://packagist.org/packages/maksde/support)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/maksde/support/php)](https://packagist.org/packages/maksde/support)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/maksde/support/laravel%2Fframework)](https://packagist.org/packages/maksde/support)
[![Packagist License](https://img.shields.io/packagist/l/maksde/support)](https://packagist.org/packages/maksde/support)


* [Правила валидации для форм](#правила-валидации-для-форм)
* [Форматирование даты и времени для передачи по api в UTC](#форматирование-даты-и-времени-для-передачи-по-api-в-utc)
* Вспомогательные функции
  * urlFront($path) - формирование полного пути файла со storage

## Установка

Установить пакет с помощью Composer:
```bash
composer require maksde/support
```

Опубликуйте файл конфигурации:
```bash
php artisan vendor:publish --tag="support-config"
```

Опубликовать файлы перевода:
```bash
php artisan vendor:publish --tag="support-lang"
```

## Правила валидации для форм

| Валидация                       | Пример использования                                                             |
|---------------------------------|----------------------------------------------------------------------------------|
| Почты                           | `'email' => ['required', new EmailValidate()],`                                  |
| Телефона                        | `'phone' => 'required\|'.config('support.validate.format.phone'),`               |
| Телефона международного формата | `'phone' => 'required\|'.config('support.validate.format.phone_international'),` |
| Даты                            | `'date' => 'required\|'.config('support.validate.format.date'),`                 |
| Времени                         | `'time' => 'required\|'.config('support.validate.format.time'),`                 |
| Даты и времени                  | `'datetime' => 'required\|'.config('support.validate.format.datetime'),`         |


## Форматирование даты и времени для передачи по api в UTC

```php
use Maksde\Support\Formation\TemporalFormat;

TemporalFormat::datetime($datetime);
TemporalFormat::date($date);
TemporalFormat::time($time);
```

### Создание своего типа форматирования

В опубликованный конфиг добавить свой тип форматирования

```php
TemporalFormat::type($datetime, 'myType');
```

### Передача формата сразу в функцию

```php
TemporalFormat::format($datetime, 'j M Y H:i:s');
```