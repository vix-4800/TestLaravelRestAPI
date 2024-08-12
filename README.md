# Тестовое задание - REST API на Laravel

## Установка

1. После клонирования репозитория, установите приложение. Для этого можно воспользоваться `docker` командой или `make` файлом

В первом случае выполните команду:

```bash
docker run --rm -v $(pwd):/var/www/html -w /var/www/html composer:latest composer install --ignore-platform-reqs --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts
```

Во втором случае выполните команду:

```bash
make install
```

2. Настройте `.env` файл, для этого:

    1. Скопируйте содержимое `.env.example` в `.env`

    2. Замените значения для базы данных в `.env` на свои. Нужно установить `DB_DATABASE`, `DB_USERNAME` и `DB_PASSWORD`

3. Запустите приложение. Для этого выполните команды:

```bash
docker compose build
docker compose up -d
docker exec -it laravel_php php artisan key:generate
docker exec -it laravel_php php artisan migrate --seed
```

Или воспользуйтесь `make` файлом:

```bash
make post-install
```

Если команда `php artisan migrate` выдаёт ошибку, попробуйте подождать несколько секунд и запустить её снова (также можно воспользовать `make migrate`). Иногда контейнеру MySQL требуется некоторое время на запуск

При использовании Makefile команды `post-install` по умолчанию будет выдержана пауза в **10** секунд

## Управление приложением

Для управления приложением можно использовать следующие команды:

-   `docker compose up -d` - запустить приложение
-   `docker compose down` - остановить приложение
-   `docker compose ps` - показать состояние приложения
-   `docker compose logs` - показать логи
-   `docker compose restart` - перезапустить приложение
-   `docker exec -it laravel_php /bin/bash` - открыть терминал внутри контейнера

Доступные `make` команды:

-   `make up` - запустить приложение
-   `make down` - остановить приложение
-   `make restart` - перезапустить приложение
-   `make ps` - показать состояние приложения
-   `make logs` - показать логи
-   `make shell` - открыть терминал внутри контейнера
-   `make test` - запустить тесты
-   `make migrate` - запустить миграции
-   `make seed` - запустить сиды
-   `make pint` - запустить Laravel Pint
-   `make larastan` - запустить Larastan

## Логирование запросов

В приложении настроено логирование SQL запросов в локальном окружении. Для этого был создан и зарегестрирован сервис-провайдер - `QueryLoggerServiceProvider`. Все логи записываются в файл `storage/logs/database_monitoring.log`

Для отключения логирования в локальном окружении можно добавить параметр `LOG_DATABASE_QUERIES` в `.env` файл и установить значение на `false` _(потребуется перезапустить приложение)_

## Дополнительные возможности

### PhpMyAdmin

Для облегчения управления базой MySQL в приложении установлен PhpMyAdmin. Получить доступ к веб-интерфейсу можно по адресу: [localhost:8081](http://localhost:8081)

Порт можно изменить через конфигурационный файл `.env`, используя параметр `PHPMYADMIN_PORT`

### RedisInsight

Для управления Redis в приложении установлен RedisInsight. Получить доступ к его веб-интерфейсу можно по адресу: [localhost:5540](http://localhost:5540). Используйте **redis** в качестве хоста для подключения

Порт можно изменить через конфигурационный файл `.env`, используя параметр `REDISINSIGHT_PORT`

### MailHog

Для отслеживания отправки почты в приложении установлен Mailhog. Получить доступ к его веб-интерфейсу можно по адресу: [localhost:8025](http://localhost:8025)

Порт можно изменить через конфигурационный файл `.env`, используя параметр `MAILHOG_PORT`

### Supervisor

Для управления ассинхронными задачами в приложении установлен Supervisor
