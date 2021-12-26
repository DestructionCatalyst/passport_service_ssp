-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: mysql
-- Время создания: Дек 26 2021 г., 23:43
-- Версия сервера: 8.0.27
-- Версия PHP: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `passport_service`
--

-- --------------------------------------------------------

--
-- Структура таблицы `application`
--

CREATE TABLE `application` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `employee_id` int DEFAULT NULL,
  `status` enum('Заполнено','Принято в обработку','Отправлено на доработку','Паспорт оформляется','Паспорт готов к получению','В оформлении отказано','Паспорт выдан') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Заполнено',
  `reason` enum('Первичное получение','Взамен действующего','Взамен утраченного','Взамен испорченного') NOT NULL,
  `application_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `application`
--

INSERT INTO `application` (`id`, `user_id`, `employee_id`, `status`, `reason`, `application_date`) VALUES
(1, 1, 1, 'В оформлении отказано', 'Первичное получение', '2021-12-01'),
(2, 1, 1, 'Паспорт выдан', 'Взамен действующего', '2021-12-21'),
(3, 1, 1, 'Принято в обработку', 'Первичное получение', '2021-12-23'),
(6, 3, 3, 'Принято в обработку', 'Первичное получение', '2021-12-26');

-- --------------------------------------------------------

--
-- Структура таблицы `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `application_id` int NOT NULL,
  `stage` int NOT NULL,
  `description` text NOT NULL,
  `status` enum('К исправлению','Внесены правки','Исправлено') NOT NULL DEFAULT 'К исправлению',
  `creation_date` date NOT NULL,
  `last_change_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `comment`
--

INSERT INTO `comment` (`id`, `application_id`, `stage`, `description`, `status`, `creation_date`, `last_change_date`) VALUES
(1, 3, 2, 'qwe', 'Исправлено', '2021-11-25', '2021-12-25'),
(2, 3, 3, 'asd', 'Исправлено', '2021-11-25', '2021-12-25'),
(3, 3, 5, 'zxc', 'Исправлено', '2021-11-25', '2021-12-25'),
(4, 3, 7, 'vbn', 'Исправлено', '2021-11-25', '2021-12-25'),
(18, 3, 3, 'asd', 'Внесены правки', '2021-11-25', '2021-12-26');

-- --------------------------------------------------------

--
-- Структура таблицы `employee`
--

CREATE TABLE `employee` (
  `id` int NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `patronym` varchar(64) NOT NULL,
  `login` varchar(266) NOT NULL,
  `password` char(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `employee`
--

INSERT INTO `employee` (`id`, `first_name`, `last_name`, `patronym`, `login`, `password`) VALUES
(1, 'Сергей', 'Сергеев', 'Сергеевич', 'sss001', '$2y$10$2xsIR6HSu3aPFUADchNkzOKRKKv9Ve3lkqR1YazS7cnOcmfGuVbxO'),
(3, 'Михаил', 'Михайлов', 'Михайлович', 'mmm001', '$2y$10$GsDrCsoaSO.vGuRdWlseTecZMs/TopXeatKDC4b9Sb6C3G5iP5IUa');

-- --------------------------------------------------------

--
-- Структура таблицы `passport`
--

CREATE TABLE `passport` (
  `id` int NOT NULL,
  `series` char(4) NOT NULL,
  `number` char(6) NOT NULL,
  `issue_date` date NOT NULL,
  `issue_organ` varchar(500) NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `passport`
--

INSERT INTO `passport` (`id`, `series`, `number`, `issue_date`, `issue_organ`, `user_id`) VALUES
(1, '1234', '987654', '2021-12-02', 'УМВД г.Москвы', 1),
(6, '9876', '654321', '2021-12-03', 'УМВД г.Москвы', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `permanent_registration`
--

CREATE TABLE `permanent_registration` (
  `id` int NOT NULL,
  `address` text NOT NULL,
  `registration_date` date NOT NULL,
  `registration_organ` varchar(500) NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `permanent_registration`
--

INSERT INTO `permanent_registration` (`id`, `address`, `registration_date`, `registration_organ`, `user_id`) VALUES
(5, '111111, Россия, г. Москва, ул. Ленина, д. 1', '2021-11-03', 'УМВД г.Москвы', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `temporary_registration`
--

CREATE TABLE `temporary_registration` (
  `id` int NOT NULL,
  `address` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `registration_organ` varchar(500) NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `temporary_registration`
--

INSERT INTO `temporary_registration` (`id`, `address`, `start_date`, `end_date`, `registration_organ`, `user_id`) VALUES
(1, '111111, Россия, г. Санкт-Петербург, Невский пр., д. 1', '2021-12-01', '2021-12-29', 'УМВД г.Санкт-Петербург', 2),
(2, 'г. Москва, ул. Льва Толстого, 1', '2021-12-06', '2022-01-09', 'УМВД г.Москвы', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `patronym` varchar(64) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(16) NOT NULL,
  `password` char(60) NOT NULL COMMENT 'Stores the hashed value',
  `sex` enum('М','Ж') NOT NULL,
  `birth_date` date NOT NULL,
  `birth_place` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `patronym`, `email`, `phone_number`, `password`, `sex`, `birth_date`, `birth_place`) VALUES
(1, 'Иван', 'Иванов', 'Иванович', 'ivanov.ivan@mail.ru', '+79101234567', '$2y$10$a1pgDBerBsqD24D7qOAsl.QFTvwxQQGe4r1oWhD7f9yEnDvx4i7tW', 'М', '1999-09-09', 'г. Москва'),
(2, 'Петр', 'Петров', 'Петрович', 'petrov.petr@ya.ru', '+79333333333', '$2y$10$S5rC9e52DD74EcXmF58L5O4q3iiWpfccbVun4CPmWsFXYGGoU3NUW', 'М', '1986-12-04', 'г. Москва'),
(3, 'Сидор', 'Сидоров', 'Сидорович', 'sidorov@gmail.com', '+79109999999', '$2y$10$LXZqv9pnVjHk/CI7nZSqLuG5SoPTf0X9okBMnJ87nLsxckcrOhm.m', 'М', '1980-08-08', 'г. Санкт-Петербург');

-- --------------------------------------------------------

--
-- Структура таблицы `work_place`
--

CREATE TABLE `work_place` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(500) NOT NULL,
  `employment_date` date NOT NULL,
  `unemployment_date` date DEFAULT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `work_place`
--

INSERT INTO `work_place` (`id`, `name`, `address`, `employment_date`, `unemployment_date`, `user_id`) VALUES
(1, 'фыв1', 'йцу1', '2021-12-01', '2021-12-15', 1),
(2, 'фыв2', 'йцу2', '2021-12-02', '2021-12-16', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Индексы таблицы `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_ibfk_1` (`application_id`);

--
-- Индексы таблицы `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Индексы таблицы `passport`
--
ALTER TABLE `passport`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `permanent_registration`
--
ALTER TABLE `permanent_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `temporary_registration`
--
ALTER TABLE `temporary_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Индексы таблицы `work_place`
--
ALTER TABLE `work_place`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_place_ibfk_1` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `application`
--
ALTER TABLE `application`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `passport`
--
ALTER TABLE `passport`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `permanent_registration`
--
ALTER TABLE `permanent_registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `temporary_registration`
--
ALTER TABLE `temporary_registration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `work_place`
--
ALTER TABLE `work_place`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `passport`
--
ALTER TABLE `passport`
  ADD CONSTRAINT `passport_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `permanent_registration`
--
ALTER TABLE `permanent_registration`
  ADD CONSTRAINT `permanent_registration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `temporary_registration`
--
ALTER TABLE `temporary_registration`
  ADD CONSTRAINT `temporary_registration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `work_place`
--
ALTER TABLE `work_place`
  ADD CONSTRAINT `work_place_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
