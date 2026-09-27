# CRUD REST API

Тестовое задание: REST API для списка задач (tasks) на чистом PHP и MySQL, без фреймворков.

## Стек

- PHP 7.4+ (mysqli)
- MySQL / MariaDB
- Apache с mod_rewrite (OpenServer, XAMPP и т.п.)

## Запуск

1. Создать базу и таблицу:
   ```
   mysql -u root < database.sql
   ```
2. Если нужно, поменять доступы к бд в `rest_api/config.php` (или задать переменные окружения `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`).
3. Положить папку `rest_api` в корень сайта. Запросы идут на `http://localhost/rest_api/tasks`.

Без Apache можно запустить через встроенный сервер PHP, тогда путь передается в параметре `q`:
```
cd rest_api
php -S localhost:8000
curl "http://localhost:8000/index.php?q=tasks"
```

## Методы

| Метод | URL | Что делает |
|---|---|---|
| GET | `/tasks` | список всех задач |
| GET | `/tasks/{id}` | одна задача |
| POST | `/tasks` | создать задачу |
| PATCH | `/tasks/{id}` | изменить задачу (можно передать только часть полей) |
| DELETE | `/tasks/{id}` | удалить задачу |

Поля задачи:

- `title` - обязательно, до 255 символов
- `description` - обязательно
- `status` - необязательно, до 40 символов, по умолчанию `new`

Тело запроса принимается в JSON или как обычная форма.

## Примеры

Создать задачу:
```
curl -X POST http://localhost/rest_api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title": "Купить молоко", "description": "2 литра"}'
```
Ответ `201`:
```json
{"id": 1, "title": "Купить молоко", "description": "2 литра", "status": "new", "created_at": "...", "updated_at": "..."}
```

Изменить статус:
```
curl -X PATCH http://localhost/rest_api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{"status": "done"}'
```

Удалить:
```
curl -X DELETE http://localhost/rest_api/tasks/1
```

## Ошибки

Ошибки возвращаются в одном формате:
```json
{"status": false, "message": "Task not found"}
```

- `400` - не прошла валидация
- `404` - задача не найдена
- `405` - метод не поддерживается
- `500` - ошибка базы данных
