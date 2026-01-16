# Тестовые случаи для валидации Email

Полный список валидных и невалидных email адресов для тестирования `EmailValidate`.

## 📋 Содержание

- [Валидные email](#валидные-email)
  - [Локальная часть (до @)](#валидная-локальная-часть)
  - [Доменная часть (после @)](#валидная-доменная-часть)
- [Невалидные email](#невалидные-email)
  - [Локальная часть (до @)](#невалидная-локальная-часть)
  - [Доменная часть (после @)](#невалидная-доменная-часть)
  - [Общие проблемы](#общие-проблемы)

---

## ✅ ВАЛИДНЫЕ EMAIL

### Валидная локальная часть

#### 1. Базовые символы

```
# Латиница
user@domain.com
test@domain.com
admin@domain.com
contact@domain.com

# Цифры
123@domain.com
user123@domain.com
123user@domain.com
user123test@domain.com

# Кириллица
пользователь@domain.com
имя@domain.com
тест@domain.com
админ@domain.com
ёжик@domain.com
```

#### 2. Подчеркивание (_)

```
# Одинарное
user_name@domain.com
test_user@domain.com
my_email@domain.com

# Двойное
user__name@domain.com
test__user@domain.com

# Тройное и более
user___name@domain.com
test____user@domain.com

# В начале
_user@domain.com
_test@domain.com
__user@domain.com

# В конце
user_@domain.com
test_@domain.com
user__@domain.com

# Множественное
_user_name_@domain.com
__test__user__@domain.com
```

#### 3. Дефис (-)

```
# Одинарное
user-name@domain.com
test-user@domain.com
my-email@domain.com

# Двойное
user--name@domain.com
test--user@domain.com

# Тройное и более
user---name@domain.com
test----user@domain.com

# В начале
-user@domain.com
-test@domain.com
--user@domain.com

# В конце
user-@domain.com
test-@domain.com
user--@domain.com

# Множественное
-user-name-@domain.com
--test--user--@domain.com
```

#### 4. Точка (.)

```
# Одинарная
user.name@domain.com
test.user@domain.com
first.last@domain.com

# Множественные
user.name.test@domain.com
first.middle.last@domain.com
a.b.c.d.e@domain.com

# НЕ в начале и НЕ в конце (это валидно)
u.ser@domain.com
use.r@domain.com
```

#### 5. Плюс (+)

```
# Одинарный
user+tag@domain.com
test+filter@domain.com
name+alias@domain.com

# Множественные
user+tag+filter@domain.com
test++filter@domain.com

# В начале
+user@domain.com
++user@domain.com

# В конце
user+@domain.com
user++@domain.com
```

#### 6. Апостроф (')

```
# Одинарный
user'name@domain.com
o'brien@domain.com
d'angelo@domain.com

# Множественные
user''name@domain.com
test'''user@domain.com

# В начале
'user@domain.com
''user@domain.com

# В конце
user'@domain.com
user''@domain.com
```

#### 7. Комбинации символов

```
# Точка + подчеркивание
user._name@domain.com
user_.name@domain.com
user._._name@domain.com

# Точка + дефис
user.-name@domain.com
user-.name@domain.com
user.-.-name@domain.com

# Дефис + подчеркивание
user-_name@domain.com
user_-name@domain.com
user-_-_name@domain.com

# Точка + подчеркивание + дефис
user._-name@domain.com
user-_.name@domain.com
user._-._-name@domain.com

# Все символы вместе
user._-'+name@domain.com
-_.'test'+user_-.@domain.com
_.-+user'name'+test.-_@domain.com

# Цифры + спецсимволы
user_1.2-3+test'4@domain.com
123-456_789.test@domain.com
```

#### 8. Кириллица + спецсимволы

```
имя_фамилия@domain.com
пользователь-тест@domain.com
юзер.нейм@domain.com
тест+фильтр@domain.com
имя'фамилия@domain.com
пользователь_1.2-3+тест@domain.com
ёжик-в_тумане.тест@domain.com
```

#### 9. Длинные локальные части

```
# До 64 символов (максимум)
a234567890123456789012345678901234567890123456789012345678901234@domain.com
user1234567890123456789012345678901234567890123456789012345678901@domain.com
verylongusernamewithlotsofcharactersinittotestmaximumlength12345@domain.com
```

---

### Валидная доменная часть

#### 1. Простые домены

```
user@domain.com
user@example.org
user@test.net
user@site.io
user@mysite.dev
user@company.app
```

#### 2. Цифры в домене

```
user@domain123.com
user@123domain.com
user@d0main.com
user@123.com
user@domain2024.com
```

#### 3. Дефис в домене

```
# Одинарный
user@my-domain.com
user@test-site.com
user@example-test.com

# Двойной
user@my--domain.com
user@test--site.com

# Множественные
user@my-test-domain.com
user@a-b-c-d.com
user@very-long-domain-name.com

# Дефис в середине
user@do-main.com
user@exa-mple.com
```

#### 4. Поддомены

```
# Один уровень
user@mail.domain.com
user@test.example.com
user@subdomain.site.org

# Множественные уровни
user@mail.test.domain.com
user@a.b.c.d.example.com
user@very.long.subdomain.chain.site.com

# С дефисами
user@my-subdomain.test-domain.com
user@sub-domain.my-site.example-site.com
```

#### 5. Кириллические домены

```
# Простые
user@домен.рф
user@сайт.рф
user@компания.рф
user@тест.рф

# С поддоменами
user@почта.домен.рф
user@тест.сайт.рф
user@mail.компания.рф

# Смешанные (кириллица + латиница)
пользователь@домен.рф
user123@сайт.рф
тест-user@компания.рф
```

#### 6. Длинные домены

```
# Длинное доменное имя (до 253 символов в домене)
user@verylongdomainnamewithlotsofcharacters.com
user@thisisaverylongdomainnametotestmaximumlength.example.com
user@averylongsubdomainname.averylongdomainname.com

# Максимальная длина домена (253 символа)
user@aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb.ccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc.ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd.com
```

#### 7. Различные TLD

```
# Популярные
user@domain.com
user@domain.net
user@domain.org
user@domain.io
user@domain.dev
user@domain.app
user@domain.tech
user@domain.online

# Национальные
user@domain.ru
user@domain.uk
user@domain.de
user@domain.fr
user@domain.jp
user@domain.cn
user@domain.br

# Длинные TLD
user@domain.museum
user@domain.photography
user@domain.international

# Кириллические TLD
user@domain.рф
user@domain.бел
user@domain.срб
user@domain.укр
user@domain.рус

# Короткие (2 символа)
user@domain.ru
user@domain.uk
user@domain.de
user@domain.fr
user@domain.jp
user@domain.io
user@domain.co
```

#### 8. Сложные домены

```
# Множественные поддомены с дефисами
user@mail-server.test-domain.example-site.com
user@sub-domain-1.sub-domain-2.main-domain.org

# Длинные имена с поддоменами
user@verylongsubdomainname.verylongdomainname.verylongtld.com
user@a1-b2-c3.d4-e5-f6.g7-h8-i9.example.com

# Цифры + дефисы + поддомены
user@mail123.test-456.domain789.com
user@1-2-3.4-5-6.domain.com
```

#### 9. Граничные случаи

```
# Минимальный домен (1 символ + точка + 2 символа TLD)
user@a.ru
user@x.io
user@1.co

# Максимальная длина email (255 символов)
aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb.com

# Максимальная длина локальной части (64 символа)
aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@domain.com

# Максимальная длина домена (253 символа)
a@bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb.com
```

---

## ❌ НЕВАЛИДНЫЕ EMAIL

### Невалидная локальная часть

#### 1. Начинается с точки

```
.user@domain.com
..user@domain.com
...user@domain.com
.test.user@domain.com
```

#### 2. Заканчивается точкой

```
user.@domain.com
test.@domain.com
user.name.@domain.com
test.user.data.@domain.com
```

#### 3. Двойные точки подряд

```
user..name@domain.com
test...user@domain.com
name....test@domain.com
user..test..name@domain.com
.user..name.@domain.com
```

#### 4. Запрещенные символы (!#$%^&*)

```
# Восклицательный знак
use!r@domain.com
user!@domain.com
!user@domain.com

# Решетка
use#r@domain.com
user#name@domain.com
#user@domain.com

# Доллар
use$r@domain.com
user$name@domain.com
$user@domain.com

# Процент
use%r@domain.com
user%name@domain.com
%user@domain.com

# Крышка
use^r@domain.com
user^name@domain.com
^user@domain.com

# Амперсанд
use&r@domain.com
user&name@domain.com
&user@domain.com

# Звездочка
use*r@domain.com
user*name@domain.com
*user@domain.com
```

#### 5. Запрещенные символы (скобки, слеши)

```
# Круглые скобки
use(r@domain.com
use)r@domain.com
user(name)@domain.com
(user)@domain.com

# Квадратные скобки
use[r@domain.com
use]r@domain.com
user[name]@domain.com
[user]@domain.com

# Фигурные скобки
use{r@domain.com
use}r@domain.com
user{name}@domain.com
{user}@domain.com

# Угловые скобки
use<r@domain.com
use>r@domain.com
user<name>@domain.com
<user>@domain.com

# Слеши
use/r@domain.com
user/name@domain.com
/user@domain.com
use\r@domain.com
user\name@domain.com
\user@domain.com

# Вертикальная черта
use|r@domain.com
user|name@domain.com
|user@domain.com
```

#### 6. Запрещенные символы (пунктуация)

```
# Запятая
use,r@domain.com
user,name@domain.com
,user@domain.com

# Двоеточие
use:r@domain.com
user:name@domain.com
:user@domain.com

# Точка с запятой
use;r@domain.com
user;name@domain.com
;user@domain.com

# Двойные кавычки
use"r@domain.com
user"name"@domain.com
"user"@domain.com
use""r@domain.com

# Обратная кавычка
use`r@domain.com
user`name@domain.com
`user@domain.com

# Тильда
use~r@domain.com
user~name@domain.com
~user@domain.com

# Вопросительный знак
use?r@domain.com
user?name@domain.com
?user@domain.com

# Знак равенства
use=r@domain.com
user=name@domain.com
=user@domain.com
```

#### 7. Пробелы

```
# В начале
 user@domain.com
  user@domain.com

# В середине
use r@domain.com
user name@domain.com
test user name@domain.com

# В конце
user @domain.com
user  @domain.com

# Множественные
user   name@domain.com
test    user    name@domain.com
```

#### 8. Превышение длины

```
# Локальная часть > 64 символов (65 символов)
aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@domain.com
user1234567890123456789012345678901234567890123456789012345678901@domain.com
verylongusernamewithlotsofcharactersinittotestmaximumlength123456@domain.com

# Еще больше (70+ символов)
aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@domain.com
user12345678901234567890123456789012345678901234567890123456789012345678@domain.com
```

---

### Невалидная доменная часть

#### 1. Отсутствие TLD

```
user@domain
user@localhost
user@example
user@test
user@site
```

#### 2. Начинается с точки

```
user@.domain.com
user@..domain.com
user@...domain.com
user@.example.org
```

#### 3. Заканчивается точкой

```
user@domain.com.
user@example.org.
user@test.net.
user@site.io.
```

#### 4. Двойные точки подряд

```
user@domain..com
user@test...example.com
user@site....org
user@my..domain..com
user@a..b..c.com
```

#### 5. Начинается с дефиса

```
user@-domain.com
user@-test.com
user@-example.org
user@--domain.com
user@my.-domain.com
user@test.-example.com
```

#### 6. Заканчивается дефисом

```
user@domain-.com
user@test-.com
user@example-.org
user@domain--.com
user@my-domain-.com
user@test-example-.com
```

#### 7. Подчеркивание в домене (ЗАПРЕЩЕНО)

```
# Одинарное
user@domain_name.com
user@test_site.com
user@my_domain.com

# Двойное
user@domain__name.com
user@test__site.com

# В начале
user@_domain.com
user@__domain.com

# В конце
user@domain_.com
user@domain__.com

# В поддомене
user@my_subdomain.domain.com
user@test_site.example.com

# Комбинации
user@my__domain.com
user@test_domain_name.com
user@_domain_.com
user@domain._com
user@domain.com_

# С точками
user@my._domain.com
user@domain_.test.com
user@my._test.com
user@a.b_.c.com

# С дефисами
user@my_-domain.com
user@domain-_name.com
user@test_-site.com
user@my-_domain-_.com

# Комбинации точка + подчеркивание + дефис
user@my._-domain.com
user@test-_..domain.com
user@._-domain.com
```

#### 8. Плюс в домене (ЗАПРЕЩЕН)

```
user@domain+name.com
user@test+site.com
user@my+domain.com
user@do+main.com
user@exa+mple.com
user@test.do+main.com
```

#### 9. Проблемы с TLD

```
# TLD из 1 символа
user@domain.c
user@test.x
user@example.a

# TLD с цифрами
user@domain.c0m
user@domain.c9m
user@domain.123
user@domain.co9
user@domain.xyz123

# TLD с дефисом
user@domain.co-m
user@domain.c-om
user@domain.ex-ample

# TLD с подчеркиванием
user@domain.co_m
user@domain.c_om
user@domain.ex_ample

# TLD заканчивается дефисом
user@domain.com-
user@domain.org-
user@domain.ru-

# TLD заканчивается подчеркиванием
user@domain.com_
user@domain.org_
user@domain.ru_

# TLD начинается с цифры
user@domain.1com
user@domain.2org
user@domain.9ru

# Несуществующие TLD
user@domain.fake
user@domain.test
user@domain.invalid
user@domain.notexist
user@domain.xyz789
user@domain.абв
user@domain.фейк
```

#### 10. Превышение длины

```
# Домен > 253 символов (254+ символов)
user@aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb.cccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc.dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd.com

# TLD > 63 символов (64 символа)
user@domain.aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
user@domain.com.bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb
```

---

### Общие проблемы

#### 1. Отсутствие обязательных частей

```
# Нет символа @
userdomain.com
testexample.org
nameatsite.com

# Два символа @ подряд
user@@domain.com
test@@example.com

# Множественные @
user@test@domain.com
user@example@test@site.com
my@email@address@domain.com

# Нет локальной части
@domain.com
@example.org
@test.net

# Нет домена
user@
test@
name@

# Только @
@
```

#### 2. Пустые значения

```
# Пустая строка
(пусто)

# Только пробелы
   
     

# Пробелы вокруг
 user@domain.com 
  test@example.com  
```

#### 3. Превышение общей длины

```
# Email > 255 символов (256+ символов)
aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb.com

# Еще длиннее (300+ символов)
aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb.com
```

#### 4. Специфические edge cases

```
# Начинается и заканчивается точкой
.user.@domain.com
..user..@domain.com

# Точки в домене (неправильные)
user@domain.com.
user@.domain.com
user@domain..com

# Комбинация проблем
.user..name.@domain..com
user@@domain
@.domain.com
user@domain.
```

---

## 📊 Статистика

### Валидные email:
- **Локальная часть:** ~90 вариантов
- **Доменная часть:** ~70 вариантов
- **Итого:** ~160 примеров

### Невалидные email:
- **Локальная часть:** ~80 вариантов
- **Доменная часть:** ~90 вариантов
- **Общие проблемы:** ~20 вариантов
- **Итого:** ~190 примеров

### **Всего:** ~350 тестовых случаев

---

## 🔗 Связанные файлы

- [Анализ валидации Email](EMAIL_VALIDATION_ANALYSIS.md) - подробный разбор регулярного выражения
- [README.md](../README.md) - основная документация пакета
- [EmailValidateTest.php](../tests/Unit/EmailValidateTest.php) - unit-тесты (137 тестов)
