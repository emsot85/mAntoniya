Вот аккуратная и структурированная версия твоего README, с выделением заголовков, шагов и важной информации. Она выглядит профессионально и легко читается:

````markdown
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

## 🚀 Быстрый старт

### 1. Клонирование репозитория
```bash
git clone git@github.com:emsot85/mAntoniya.git
cd mAntoniya
````

### 2. Установка зависимостей

```bash
composer install
npm install
```

### 3. Настройка `.env`

Скопируйте файл `.env.example` в `.env` и укажите данные:

* База данных
* URL сайта
* Ключи приложения

### 4. Обновление зависимостей и подготовка базы данных

```bash
composer update
php artisan migrate
php artisan db:seed
```

### 5. Доступ к админке

* **URL:** `https://ваш-сайт.com/admin/login`
* **Логин:** `admin@a.ru`
* **Пароль:** `12345678`

### 6. Импорт бэкапа с контентом

1. Перейдите в админку на страницу **Бэкап**.
2. Загрузите файл `backup.json`, который лежит в корне сайта.
3. Все статьи и страницы будут импортированы автоматически.
4. Для автоматического перевода статей, нужно будет подключить yandex переводчик с Вашими ключами для API, в .env файле!

---

## 🛠 Используемые технологии

* **Laravel** — PHP фреймворк
* **Livewire** — интерактивные компоненты на фронтенде
* **Filament Admin** — админка
* **Vite + TailwindCSS** — сборка и стили
* **Alpine.js** — интерактивность на фронтенде

---

## 📈 SEO рекомендации

* Заголовки и мета-описания страниц хранятся в базе данных и автоматически подставляются в шаблоны.
* Для страниц без SEO данных используется fallback на название сайта.

---

## 🤝 Contributing

Если вы хотите внести изменения в проект:

* Следуйте [руководству по внесению изменений Laravel](https://laravel.com/docs/contributions).
* Соблюдайте [кодекс поведения](https://laravel.com/docs/contributions#code-of-conduct).

---

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

````


```markdown
## 📞 Поддержка
По вопросам установки и запуска проекта пишите в telegram: emsot
````

