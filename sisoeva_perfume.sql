-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Дек 19 2025 г., 10:23
-- Версия сервера: 11.4.7-MariaDB-ubu2404
-- Версия PHP: 8.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sisoeva_perfume`
--

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id_category` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id_category`, `category_name`) VALUES
(1, 'Любимая классика'),
(2, 'Сладкий и чувственный'),
(3, 'Свежесть с первых нот');

-- --------------------------------------------------------

--
-- Структура таблицы `fragrances`
--

CREATE TABLE `fragrances` (
  `id_fragrances` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` varchar(355) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `volume_ml` int(11) NOT NULL,
  `gender` enum('Female','Male','Unisex') NOT NULL,
  `raiting_f` int(11) NOT NULL,
  `image_f` varchar(500) NOT NULL,
  `creat_fr` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Дамп данных таблицы `fragrances`
--

INSERT INTO `fragrances` (`id_fragrances`, `product_name`, `category_id`, `description`, `price`, `volume_ml`, `gender`, `raiting_f`, `image_f`, `creat_fr`) VALUES
(1, 'Dior Sauvage Eau de Toilette', 1, 'Иконический современный аромат. Верхние ноты - калабрийский бергамот, сердце - нори и лабданум, база - амброксан. Символ свободы и мужественности.', 8990.00, 100, 'Male', 4, 'dior_sauvage_edt_100ml.jpg', '2025-12-13 19:42:01'),
(2, 'Yves Saint Laurent Black Opium Eau de Parfum', 2, 'Навязчиво сладкий, бодрящий и затягивающий аромат. Сочетание эспрессо, ванили и белых цветов создает образ современной, энергичной женщины.', 7490.00, 90, 'Female', 5, 'ysl_black_opium_edp_90ml.jpg', '2025-12-13 19:43:03'),
(3, 'Maison Francis Kurkdjian Aqua Universalis Forte', 3, 'Абсолютная свежесть и чистота. Цитрусовые аккорды бергамота, лимона и нероли в обрамлении белых мускусов и цветочных нот.', 15600.00, 80, 'Female', 5, 'mfk_aqua_universalis_forte_70ml.jpg', '2025-12-13 19:44:15'),
(4, 'Nautica Voyage', 1, 'Кристально чистый водный аромат для ежедневной носки. Открывается нотами зеленого яблока и листьев водяной лилии, переходит в древесные аккорды кедра.', 2590.00, 100, 'Male', 4, 'nautica_voyage_100ml.jpg', '2025-12-13 19:45:16');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id_orders` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fragrances_id` int(11) NOT NULL,
  `adress` varchar(500) NOT NULL,
  `status` enum('Confirmed','Assembly','Sent','Received') NOT NULL,
  `pay_metod` enum('Card','Cash') NOT NULL,
  `comment` varchar(500) NOT NULL,
  `creat_orders` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id_orders`, `user_id`, `fragrances_id`, `adress`, `status`, `pay_metod`, `comment`, `creat_orders`) VALUES
(1, 10, 3, 'ул Оптиков 55, кв 55', 'Confirmed', 'Card', 'Позвоните за 15 минут до доставки', '2025-12-15 19:41:35'),
(2, 9, 4, 'Ул. Петрова 45, кв 22', 'Assembly', 'Cash', 'Оставить у двери', '2025-12-15 19:46:59'),
(3, 7, 2, 'Ул. Лаврова 123, кв 11', 'Assembly', 'Cash', 'Оставить у двери', '2025-12-15 19:47:38'),
(4, 9, 1, 'г. Москва, ул. Примерная, д. 10, кв. 25', 'Confirmed', 'Card', 'Позвонить за час до доставки', '2025-12-16 05:49:16');

-- --------------------------------------------------------

--
-- Структура таблицы `post`
--

CREATE TABLE `post` (
  `id_post` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `fragrances_id` int(11) NOT NULL,
  `raiting` tinyint(4) NOT NULL,
  `text` varchar(500) NOT NULL,
  `creat_post` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Дамп данных таблицы `post`
--

INSERT INTO `post` (`id_post`, `user_id`, `category_id`, `fragrances_id`, `raiting`, `text`, `creat_post`) VALUES
(1, 3, 1, 1, 5, 'Очень понравился аромат, куплю еще)', '2025-12-15 19:36:30'),
(2, 2, 2, 3, 5, 'приятный аромат, брала маме в подарок\r\n', '2025-12-15 19:37:20'),
(3, 9, 3, 4, 4, 'Брал в подарок', '2025-12-15 19:38:28'),
(4, 9, 1, 1, 4, 'Отличный аромат, держит-ся долго, но немного резковат в начальных нотах', '2025-12-16 06:11:25'),
(5, 9, 1, 1, 4, 'Отличный аромат, держит-ся долго, но немного резковат в начальных нотах', '2025-12-16 06:32:59'),
(6, 9, 1, 1, 4, 'Отличный аромат, держит-ся долго, но немного резковат в начальных нотах', '2025-12-16 06:37:56'),
(7, 9, 1, 1, 4, 'Отличный аромат, держит-ся долго, но немного резковат в начальных нотах', '2025-12-16 06:40:14'),
(8, 9, 1, 1, 4, 'Отличный аромат, держит-ся долго, но немного резковат в начальных нотах', '2025-12-16 06:40:58');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `fio` varchar(255) NOT NULL,
  `phone` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `password` varchar(355) NOT NULL,
  `creat_user` timestamp NOT NULL DEFAULT current_timestamp(),
  `access_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id_user`, `fio`, `phone`, `email`, `role`, `password`, `creat_user`, `access_token`) VALUES
(2, 'Лаврова Елена Владиславовна', '8(999)797-02-44', 'lav@gmail.com', 'user', '$2y$13$xFylrm67kFGZ0pfYmmjOieyQqzfcdm3ozH/E4VUtL67mvrwea1w1q', '2025-12-12 10:53:10', NULL),
(3, 'Константинова Алина Петрова', '8(237)797-55-44', 'konst@gmail.com', 'user', '$2y$13$jM0fafqmlkEWk4LJpkA9AO3t99jOtPDpLqFZNw9MrRpQ5V6Dx36wO', '2025-12-12 11:01:52', NULL),
(4, 'Петров Иван Андреевич', '8(900)123-45-67', 'petrov1@gmail.com', 'user', '$2y$13$Fti/aQRRbjmvZxQ0qhA9h.TEx.Aim.bZQZNlAmolTUOpmefrsLV/2', '2025-12-13 19:29:15', NULL),
(5, 'Васильев Иван Андреевич', '8(900)123-99-77', 'vasya1@gmail.com', 'user', '$2y$13$2wgTAfoz0c3Qzdyv8WhAr.dtCBLMlPSKLKgLOioG3rw9gXtQnZSc.', '2025-12-13 19:59:54', NULL),
(6, 'Васильева Алина Андреевна', '8(222)123-99-77', 'all1@gmail.com', 'user', '$2y$13$YvHB.WF6UUuML69VBY19AOETkjpjElTEgSYPlY7oTCb37hZWLZJKC', '2025-12-13 20:13:44', NULL),
(7, 'Поплутина Василиса Андреевна', '8(124)123-99-77', 'popl1@gmail.com', 'user', '$2y$13$mV599qgAYG0iXfKHgCRg2ehBqwM6pvl9ulC4E2ds0IOPMDKygZTs6', '2025-12-13 20:17:05', NULL),
(9, 'Андреев Михаил Евгкньевич', '8(777)123-99-77', 'and1@gmail.com', 'user', '$2y$13$OKiKn4TkaFVw6xQN.mJ3yeWkM0ejS68D6lpj7zE2766sK7.Jiz.dy', '2025-12-14 18:46:48', 'Aku4Q0tIUOSIxwDJ6P610RZjmvzNmQuM'),
(10, 'Админ', '8(237)797-55-44', 'admin@gmail.com', 'admin', '$2y$13$6ErXSBpLvBQFqENkwO3bP.xVKu/lRS8.rnAh6o/rSVBllSYodlxHi', '2025-12-15 07:35:52', 'OYYXdkwN7149DhO2IcrB4J9u6SBGhOPY');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id_category`);

--
-- Индексы таблицы `fragrances`
--
ALTER TABLE `fragrances`
  ADD PRIMARY KEY (`id_fragrances`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_orders`),
  ADD KEY `fragrances_id` (`fragrances_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fragrances_id` (`fragrances_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `fragrances`
--
ALTER TABLE `fragrances`
  MODIFY `id_fragrances` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id_orders` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `fragrances`
--
ALTER TABLE `fragrances`
  ADD CONSTRAINT `fragrances_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`fragrances_id`) REFERENCES `fragrances` (`id_fragrances`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);

--
-- Ограничения внешнего ключа таблицы `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`),
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`fragrances_id`) REFERENCES `fragrances` (`id_fragrances`),
  ADD CONSTRAINT `post_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
