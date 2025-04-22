# Helpers for Laravel

* Правила валидации для форм
* Форматировщик даты и времени для отдачи по api в UTC
* Вспомогательные функции
  * urlFront($path)

## Installation

Install the package with Composer:

```bash
composer require maksde/support
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="support-config"
```

Publish the translation files:

```bash
php artisan vendor:publish --tag="support-lang"
```

## Примеры использования валидаторов

| Валидация                       | Пример использования                                                             |
|---------------------------------|----------------------------------------------------------------------------------|
| Почты                           | `'email' => ['required', new EmailValidate()],`                                  |
| Телефона                        | `'phone' => 'required\|'.config('support.validate.format.phone'),`               |
| Телефона международного формата | `'phone' => 'required\|'.config('support.validate.format.phone_international'),` |
| Даты                            | `'date' => 'required\|'.config('support.validate.format.date'),`                 |
| Времени                         | `'time' => 'required\|'.config('support.validate.format.time'),`                 |
| Даты и времени                  | `'datetime' => 'required\|'.config('support.validate.format.datetime'),`         |


## Форматирование дат и времени для передачи по api

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