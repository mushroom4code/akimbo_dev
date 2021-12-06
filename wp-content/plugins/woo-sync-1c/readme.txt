=== WooCommerce: Обмен данным с информационными системами на базе 1С ===
Contributors: sgtpep
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: 1c, 1c-enterprise, commerceml, integration, e-commerce, ecommerce, commerce, shop, cart, woothemes, woocommerce
Requires at least: 3.8
Tested up to: 4.6
Stable tag: 0.84

Provides data exchange between WooCommerce plugin and business application "1C:Enterprise 8. Trade Management" (and compatible ones).

== Description ==

= In Russian =

Предоставляет обмен данными между плагином для электронной коммерции WooCommerce и приложением для бизнеса "1C:Предприятие 8. в формате CML2.

> Для достижения корректной работы плагина могут потребоваться базовые навыки администрирования веб-серверов (просмотр логов, изменение настроек php и веб-серверов и др.) А настройка плагина осуществляется добавлением констант в `wp-config.php` (посмотреть доступные можно командой: `grep -r "define('WC1C_"`) и функций [фильтров и действий](https://codex.wordpress.org/Plugin_API) в `functions.php` в папке активной темы (посмотреть доступные можно командой: `grep -r "do_action\|apply_filters"`).

Особенности:

* Выгрузка товаров: группы (категории), свойства и значения, список товаров и вариантов, изображения, свойства, реквизиты, цены, остатки товаров.
* Обмен заказами: двусторонний обмен информацией о заказах на сайте и в приложении.
* Полная и частичная синхронизация.
* Экономичное использование оперативной памяти сервера.
* Поддержка передачи данных в сжатом виде.
* Транзакционность и строгая проверка на ошибки: данные обновляются в БД только в случае успешного обмена.

Пожалуйста, перед использованием плагина прочитайте следующее:

* [инструкцию по установке](./installation/)
* [часто задаваемые вопросы](./faq/)

Поддержать разработку и автора можно взносом через [банковскую карту или Яндекс.Деньги](https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=1000&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=).

= In English =

Provides data exchange between eCommerce plugin WooCommerce and business application "1C:Enterprise 8. Trade Management".

Features:

* Product exchange: group (categories), attributes and values, product list and product variations, images, properties, requisites, prices, remains for products.
* Order exchange: two way exchange of order information between website and application.
* Partial and full syncronization.
* Effective usage of RAM on server.
* Support for compressed data exchange.
* Transactions and strict error checking: DB updates on successfull data exchange only.

Please, read the following before using this plugin:

* [installation instructions](./installation/)
* [frequently asked questions](./faq/)

= License =

"WooCommerce and 1C:Enterprise Data Exchange" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
"WooCommerce and 1C:Enterprise Data Exchange" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "WooCommerce and 1C:Enterprise Data Exchange". If not, see http://www.gnu.org/licenses/gpl-3.0.html.

= Настройка =

Вначале вам необходимо установить и активировать плагин WooCommerce, т.к. этот плагин зависит от него. Для этого зайдите в панель управления WordPress, выберите "Плагины" → "Добавить новый". В поисковом поле введите название плагина (или часть) и кликните "Искать плагины". Установите найденный плагин, кликнув "Установить сейчас".

В 1С в качестве адреса в настройках обмена с сайтом необходимо один из адресов вида:

* http://example.com/?wc1c=exchange
* или http://example.com/wc1c/exchange/, если на сайте включены постоянные ссылки ("Настройки" → "Постоянные ссылки")

где example.com – доменное имя сайта интернет-магазина.

В качестве имени пользователя и пароля в 1С следует указать действующие на сайте имя и пароль активного пользователя с ролью "Shop Manager" или Администратор.

Весь процесс настройки 1С:Предприятия для обмена данными с сайтом хорошо описан в инструкции к одному из коммерческих движков интернет-магазина: http://www.cs-cart.ru/docs/4.1.x/rus_build_pack/1c/instruction/index.html#id3, которой можно следовать до раздела "Настройки в интернет-магазине".

= Технические рекомендации =

Рекомендуется изменить тип хранилища всех таблиц базы данных сайта на InnoDB. Это добавит транзакционность в процесс обмена данными: изменения в базе данных сайта будут применяться только в случае успешного завершения процесса обмена.

1С закачивает на сервер выгрузку с помощью POST-запроса. Возможно, понадобится увеличить лимит объема данных, отправляемых по POST. В php.ini за это отвечает значение post_max_size. В случае использования FastCGI и/или nginx может понадобится увеличить этот лимит также в их настройках (например, FcgidMaxRequestLen для mod_fcgid; client_max_body_size, send_timeout для nginx).

Если PHP выполняется в режиме FastCGI, а 1С при проверке соединения с сервером просит проверить имя пользователя и пароль, хотя они указаны верно, то необходимо в файл .htaccess после строки `RewriteEngine On` вставить строку `RewriteRule . - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]`, а также попробовать оба варианта адреса обмена (полный и короткий). Необходимо учесть, что изменения в .htaccess перезатираются при сохранении настроек постоянных ссылок и некоторых плагинов из админки WordPress.


== Changelog ==

= 0.8 =

Реализована поддержка работы с модулем 1С:Битрикс.


