# studios.boomroom.by

Сайт и админка репетиционных баз BOOM (PHP 8.3, MySQL 5.7).

## Локально
- `docker compose up -d` — сайт на http://localhost:8004/, база из `docker/mysql/init/*.sql.gz` (свежие дампы с сервера: `bash importDumpFromRelease.sh`)
- вход в админку: http://localhost:8004/admin, пароли ролей в `secrets-local.php`
- на сервере вместо `secrets-local.php` лежит `secrets-release.php` (не в git)

## e2e тесты (Playwright)
- `yarn install`, затем `yarn e2e:up` — отдельный стек: http://localhost:8005/, MySQL на 3309 (схема `tests/fixtures/schema.sql` + справочники `tests/fixtures/seed.sql`, без данных клиентов)
- `yarn e2e` — тесты; `yarn e2e:reset` — пересоздать базу
- тест падает на любых ошибках PHP, кроме старых notice/deprecated (список в `test-results/php-notices.log`)

## Деплой
- push в `main` — GitHub Actions копирует код на boom-ubuntu через rsync (`.github/workflows/deploy-prod.yml`)
- миграции БД в `migrations/` применяются вручную
- PHP-FPM на сервере: `docker/release/` (переключение с 5.6: `docker/release/switch-php83.sh`)
