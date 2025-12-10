# Attendance Tracker - Система учета посещаемости сотрудников

Веб-приложение для учета опозданий, прогулов и отсутствий сотрудников с разделением ролей (сотрудник/администратор).

## Технологии
- **Backend:** PHP 7.4+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **База данных:** MySQL
- **Сервер:** Apache (XAMPP)
- **Доступ к БД:** PDO

## Функциональность
- Регистрация и авторизация пользователей
- Две роли: сотрудник и администратор
- Сотрудники: добавление/редактирование записей об отсутствиях
- Администраторы: просмотр всех записей, подтверждение/отклонение
- Фильтрация и поиск записей
- Валидация форм

## Структура базы данных

### Таблица `users`
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee', 'admin') DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);