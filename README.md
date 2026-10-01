# Perfume API — REST API интернет-магазина парфюмерии

Учебный backend-проект: REST API для каталога ароматов, заказов и отзывов с аутентификацией по токену.

**Стек:** PHP, Yii2 (REST), MySQL, Postman

## Возможности

- регистрация и вход пользователя, выдача Bearer-токена (срок действия 30 дней);
- каталог ароматов: получение по id, поиск и фильтрация (например, по полу `gender`);
- заказы: создание, список, просмотр, смена статуса (`PUT /orders/{id}/status`);
- отзывы к ароматам: создание, просмотр, список по аромату;
- админ-операции: создание ароматов;
- корректные HTTP-коды ответов (`201`, `401`, `409`, `422`).

## Эндпоинты

| Метод | URL | Описание |
|---|---|---|
| POST | `/register` | регистрация |
| POST | `/login` | вход, возвращает токен |
| GET | `/fragrances` | поиск ароматов (`?gender=Female`) |
| GET | `/fragrances/{id}` | один аромат |
| POST | `/orders` | создать заказ |
| GET | `/orders`, `/orders/{id}` | список и просмотр заказов |
| PUT | `/orders/{id}/status` | сменить статус заказа |
| GET | `/posts`, `/posts/{id}` | отзывы (`?fragrances_id=1`) |
| POST | `/posts` | создать отзыв |
| POST | `/admin/fragrances` | создать аромат (админ) |

Готовая коллекция запросов: [`fragrances.postman_collection.json`](fragrances.postman_collection.json).
Для защищённых запросов передавайте заголовок `Authorization: Bearer <token>`.

## База данных

MySQL, 5 таблиц (дамп: [`sisoeva_perfume.sql`](sisoeva_perfume.sql)): `user`, `category`, `fragrances`, `orders`, `post`.

## Запуск

```bash
composer install
cp config/db-local.example.php config/db-local.php   # впишите свои данные подключения к MySQL
# импортируйте sisoeva_perfume.sql в созданную базу
php yii serve
```

Данные подключения хранятся в `config/db-local.php`; он в `.gitignore` и в репозиторий не попадает.

## Структура

```
controllers/   ApiController, FragrancesController, OrdersController, PostController, UserController
models/        Fragrances, Orders, Post, User, Category
config/        конфигурация и правила маршрутизации (REST UrlRule)
```
