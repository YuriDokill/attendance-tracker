-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Дек 11 2025 г., 09:49
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `attendance_tracker`
--

-- --------------------------------------------------------

--
-- Структура таблицы `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` enum('late','absence','vacation','own_account') NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `user_id`, `date`, `type`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, '2025-12-10', 'late', 'Проспал..', 'approved', '2025-12-10 15:43:55', '2025-12-10 15:55:18'),
(2, 5, '2025-12-16', 'vacation', 'Устал парни...', 'rejected', '2025-12-10 18:41:04', '2025-12-10 19:27:47'),
(3, 5, '2025-12-15', 'absence', '.', 'approved', '2025-12-10 19:30:25', '2025-12-10 19:40:25'),
(4, 5, '2025-12-26', 'own_account', 'В больничку', 'rejected', '2025-12-10 19:40:07', '2025-12-10 19:40:32');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('employee','admin') DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Шабит', 'testZ@mail.ru', '$2y$10$eYpCz047yq5mHf4uB5CrB.u8PHXQoiZ9pQTE2jxnksWD4EuhL9N6e', 'employee', '2025-12-10 15:16:44'),
(3, 'admin', 'adminZ@mail.ru', '$2y$10$uM5fMLYnQvB1f0vwfqSUROOXMzP3XAGHAfWTU9F7u1xJgmLfcuVPm', 'admin', '2025-12-10 15:21:50'),
(4, 'Администратор', 'admin@mail.ru', '$2y$10$YourHashHere...', 'admin', '2025-12-10 15:34:44'),
(5, 'Хапаев', 'hapaev@gmail.com', '$2y$10$9rcfkQq4JIDBKwQQkf2uX.2ZO0qEQz17WUybHgT93C6gL531M/r4y', 'employee', '2025-12-10 15:42:16'),
(6, 'Админ', 'admin123@gmail.com', '$2y$10$Z9.Zyu5hYQ5YRQCUY5Z50.sHRC0gQOCDFyyO3obG/97opRtrrQGcW', 'admin', '2025-12-10 15:46:14');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
