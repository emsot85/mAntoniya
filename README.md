<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

# mAntoniya Project

## Быстрый старт

1. Клонируйте репозиторий:  
```bash
git clone https://github.com/ваш-репозиторий.git
cd ваш-репозиторий

2. Установите зависимости:
composer install
npm install

3. Настройте .env файл (копируйте .env.example и укажите данные БД, URL сайта и ключи).

4. Обновите зависимости и подготовьте базу данных:

composer update
php artisan migrate
php artisan db:seed

5. Доступ к админке:

URL: https://ваш-сайт.com/admin/categories
Логин: admin@a.ru
Пароль: 12345678

6. Импорт бэкапа с контентом:

Перейдите в админку, на страницу Бэкап.

Загрузите файл backup.json, который лежит в корне сайта.

Все статьи и страницы будут импортированы автоматически.


Используемые технологии

Laravel — PHP фреймворк

Livewire

Filament Admin

Vite + TailwindCSS

Alpine.js для интерактивности на фронтенде


SEO рекомендации

Заголовки и мета-описания страниц хранятся в базе данных и автоматически подставляются в шаблоны.

Для страниц без SEO данных используется fallback на название сайта.


Contributing

Если вы хотите внести изменения в проект, пожалуйста, следуйте руководству по внесению изменений Laravel и соблюдайте кодекс поведения.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
