# API статистики

## Установка

Система, на которой требуется развернуть приложения, должна иметь установленные пакеты:
- Docker (вместе с Docker Compose)
- Git

Для первоначальной установки приложения необходимо выполнить следующие команды в терминале (bash):

```shell
git clone https://github.com/yageorgiy/prj01
cd prj01
cp .env.example .env
docker-compose up -d
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate:fresh --seeder=DatabaseSeeder
```

Сервис будет доступен по адресу `http://127.0.0.1:8095/`.

## Доступные методы

TODO: сделать описание методов по спецификации OpenAPI

Ниже приведен список доступных для использования методов, их возвращаемые данные и входные параметры.

### Регистрация
```http request
TODO
```

### Отправка события
```http request
TODO
```

### Вывод статистики
```http request
TODO
```

