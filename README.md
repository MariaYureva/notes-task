# Внутренняя система заметок для сотрудников

Тестовое задание: личные заметки сотрудника с привязкой к пользователю, поиском, цветными метками и закреплением.

## Стек

- PHP 8+
- Yii2 (шаблон advanced)
- MySQL 8
- Alpine.js (через CDN) - для выбора цвета и pin/unpin без перезагрузки
- Bootstrap 5 - поставляется с шаблоном

## Развёртывание

1. Клонировать репозиторий и установить зависимости:
   ```bash
   git clone <repo-url>
   cd notes-task
   composer install
   ```

2. Инициализировать окружение (выбрать Development):
   ```bash
   php init
   ```

3. Создать базу данных:
   ```sql
   CREATE DATABASE notes_task CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Настроить подключение к БД в `common/config/main-local.php` (DSN, username, password).

5. Прогнать миграции:
   ```bash
   php yii migrate
   ```

6. Открыть `frontend/web/` в браузере. Дополнительно нужно зарегистрировать пользователя (через `Signup`).
  
## Маршруты

| Метод | Путь                       | Действие             |
|-------|----------------------------|----------------------|
| GET   | /notes                     | Список заметок       |
| GET   | /notes/create              | Форма создания       |
| POST  | /notes                     | Сохранение           |
| GET   | /notes/{id}/edit           | Форма редактирования |
| POST  | /notes/{id}                | Обновление           |
| POST  | /notes/{id}/delete         | Удаление             |
| POST  | /notes/{id}/toggle-pin     | Переключение pin     |

Все маршруты требуют авторизации.

## Структура решения

- `console/migrations/m260526_120000_create_notes_table.php` — миграция таблицы `notes` (FK на `user`, индекс по `user_id`).
- `common/models/Note.php` — ActiveRecord с валидацией title/color/is_pinned.
- `frontend/models/NoteSearch.php` — поиск по title (LIKE) и пагинация.
- `frontend/controllers/NoteController.php` — CRUD + `toggle-pin` в JSON. Проверка `user_id` через `findOwnModel`.
- `frontend/views/note/` — список (grid из карточек) и общая форма create/update.
- `frontend/web/css/notes.css` — стили карточек и цветовых кружков.

## Безопасность

- `AccessControl` ограничивает доступ к заметкам только авторизованным.
- `findOwnModel` проверяет принадлежность заметки текущему пользователю — иначе 403/404.
- Все пользовательские данные экранируются через `Html::encode`.
- Запросы к БД идут через ActiveRecord (prepared statements).
- CSRF включён: `ActiveForm` и fetch для pin/unpin подставляют токен.
