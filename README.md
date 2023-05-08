# API статистики

## Установка

Система, на которой требуется развернуть приложение, должна иметь установленные пакеты:
- Docker (вместе с Docker Compose)
- Git

Для первоначальной установки приложения необходимо выполнить следующие команды в терминале (bash):

```shell
git clone https://github.com/yageorgiy/prj01
cd prj01
cp .env.example .env

# Настройка прав для директории проекта
chgrp -R 33 ./
chmod -R 775 ./

# Запуск контейнера
docker-compose up -d
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate:fresh --seeder=DatabaseSeeder
```

Сервис будет доступен по адресу `http://127.0.0.1:8095/`.

## Доступные методы

TODO: сделать описание методов по спецификации OpenAPI

Ниже приведен список доступных для использования методов, их входные параметры.

### Регистрация пользователя

Регистрация пользователя, от имени которого будут приниматься запросы.

```http request
POST /api/register

{
	"name": "string",
	"email": "string",
	"password": "string"
}
```

Параметры запроса:
- name: обязательное поле, строка, имя пользователя
- email: обязательное поле, строка, адрес электронной почты пользователя в формате user@example.com
- password: обязательное поле, строка, пароль пользователя

Пример запроса:

```http request
POST /api/register HTTP/1.1
Host: localhost:8095
Content-Type: application/json
Accept: */*
Content-Length: 81

{
	"name": "user1",
	"email": "user1@example.com",
	"password": "user_password"
}
```

### Создание типа события

Создание типа события, который будет использоваться в фильтрах статистики.

```http request
POST /api/createEventType

{
	"event_name": "string"
}
```

Параметры запроса:
- event_name: обязательное поле, строка, наименование типа события, которое нужно создать

Пример запроса:

```http request
POST /api/createEventType HTTP/1.1
Host: localhost:8095
Content-Type: application/json
Accept: */*
Content-Length: 33

{
	"event_name": "sample_event"
}
```

### Отправка (регистрация) события

Добавление события в базу данных.

```http request
POST /api/submit

{
	"event_name": "string",
	"user_id": "integer"
}
```

Параметры запроса:
- event_name: обязательное поле, строка, наименование события, которое нужно зафиксировать
- user_id: обязательное поле, целое число, идентификатор пользователя, от имени которого фиксируется событие 
  (используйте 1 для фиксации события от имени анонимного пользователя)

Пример запроса:

```http request
POST /api/submit HTTP/1.1
Host: localhost:8095
Content-Type: application/json
Accept: */*
Content-Length: 48

{
	"event_name": "sample_event",
	"user_id": 1
}
```

### Вывод статистики

Вывод статистики из базы данных с применением фильтров.

```http request
GET /api/stats?type=integer&event_type_name=string&event_date_begin=datetime&event_date_end=datetime
```

Параметры запроса:
- type: обязательное поле, целое число от 1 до 4, тип агрегации
  (1 = счетчик конкретного события, 
   2 = счетчик события по пользователю, 
   3 = счетчик события по IP-адресу, 
   4 = счетчик события по статусу пользователя)
- event_type_name: обязательное поле, строка, наименование типа события
- event_date_begin: обязательное поле, строка, дата начала диапазона в формате год-месяц-день час-минута-секунда (2000-01-31 23:10:00)
- event_date_end: обязательное поле, строка, дата конца диапазона в формате год-месяц-день час-минута-секунда (2000-01-31 23:10:00)

Пример запроса:

```http request
GET /api/stats?type=1&event_type_name=sample_event&event_date_begin=2001-01-01%2000%3A00%3A00&event_date_end=2101-01-01%2000%3A00%3A00 HTTP/1.1
Host: localhost:8095
Accept: */*
```
