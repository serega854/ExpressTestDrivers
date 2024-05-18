-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 19 2024 г., 01:19
-- Версия сервера: 5.7.39
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test_drivers`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Administrators`
--

CREATE TABLE `Administrators` (
  `AdminID` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Administrators`
--

INSERT INTO `Administrators` (`AdminID`, `Name`, `Email`, `Password`) VALUES
(2, 'q', 'q@q.ru', 'q'),
(3, 'w', 'w@w.ru', 'w');

-- --------------------------------------------------------

--
-- Структура таблицы `AssignedTests`
--

CREATE TABLE `AssignedTests` (
  `AssignmentID` int(11) NOT NULL,
  `AdminID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DateAssigned` datetime DEFAULT NULL,
  `IsCompleted` tinyint(1) DEFAULT '0',
  `TimeToComplete` int(11) DEFAULT '0',
  `DateEndAssigned` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `AssignedTests`
--

INSERT INTO `AssignedTests` (`AssignmentID`, `AdminID`, `DriverID`, `TestID`, `DateAssigned`, `IsCompleted`, `TimeToComplete`, `DateEndAssigned`) VALUES
(1, 2, 52, 2, '2024-05-18 04:35:03', 0, 1, '2024-05-18 05:35:03'),
(2, 2, 52, 5, '2024-05-18 04:40:03', 0, 1, '2024-05-18 05:40:03'),
(3, 2, 52, 6, '2024-05-18 04:40:03', 0, 1, '2024-05-18 05:40:03'),
(5, 2, 52, 9, '2024-05-18 04:40:03', 0, 1, '2024-05-18 05:40:03'),
(7, 2, 50, 9, '2024-05-18 04:42:59', 0, 1, '2024-05-18 05:42:59'),
(8, 2, 52, 8, '2024-05-18 04:44:59', 0, 1, '2024-05-18 05:44:59');

-- --------------------------------------------------------

--
-- Структура таблицы `Drivers`
--

CREATE TABLE `Drivers` (
  `DriverID` int(11) NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_license_issue` date DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Drivers`
--

INSERT INTO `Drivers` (`DriverID`, `Email`, `Password`, `last_name`, `first_name`, `middle_name`, `date_of_birth`, `gender`, `phone_number`, `date_of_license_issue`, `registration_date`, `height`, `weight`) VALUES
(47, 'test@test.ru', '123', 'Слонов', 'Антон', 'Владимирович', '2002-12-17', 'Male', '89093880812', '2024-05-17', '2024-05-17', '176.00', '75.00'),
(48, 'ivanov@test.ru', '123', 'Иванов', 'Иван', 'Иванович', '1999-01-17', 'Male', '89123123123', '2024-05-17', '2024-05-17', '180.00', '90.00'),
(50, 'anton@test.ru', '123', 'Антонов', 'Антон', 'Антонович', '1988-09-13', 'Male', '89456456456', '2024-05-17', '2024-05-17', '190.00', '100.00'),
(51, 'kirill@test.ru', '123', 'Кириллов', 'Кирилл', 'Кириллович', '1992-09-30', 'Male', '89567567567', '2024-05-17', '2024-05-17', '185.00', '83.00'),
(52, 'sergey@test.ru', '123', 'Сергеев', 'Сергей', 'Сергеевич', '2002-08-02', 'Male', '89678678678', '2024-05-17', '2024-05-17', '175.00', '75.00');

-- --------------------------------------------------------

--
-- Структура таблицы `drivertests`
--

CREATE TABLE `drivertests` (
  `DriverTestID` int(11) NOT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `TestID` int(11) DEFAULT NULL,
  `Score` int(11) DEFAULT NULL,
  `DateCompleted` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `LicenseCategories`
--

CREATE TABLE `LicenseCategories` (
  `category_id` int(11) NOT NULL,
  `category_code` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `LicenseCategories`
--

INSERT INTO `LicenseCategories` (`category_id`, `category_code`, `category_description`, `DriverID`) VALUES
(451, 'b', 'легковой автомобиль', 47),
(452, 'be', 'легковой автомобиль с прицепом', 47),
(453, 'c', 'грузовой автомобиль', 48),
(454, 'ce', 'грузовой автомобиль с прицепом', 48),
(457, 'b', 'легковой автомобиль', 50),
(458, 'c', 'грузовой автомобиль', 50),
(459, 'be', 'легковой автомобиль с прицепом', 50),
(460, 'ce', 'грузовой автомобиль с прицепом', 50),
(461, 'b', 'легковой автомобиль', 51),
(462, 'c', 'грузовой автомобиль', 51),
(463, 'be', 'легковой автомобиль с прицепом', 51),
(464, 'ce', 'грузовой автомобиль с прицепом', 51),
(465, 'b', 'легковой автомобиль', 52),
(466, 'c', 'грузовой автомобиль', 52),
(467, 'be', 'легковой автомобиль с прицепом', 52),
(468, 'ce', 'грузовой автомобиль с прицепом', 52);

-- --------------------------------------------------------

--
-- Структура таблицы `PassedPilot`
--

CREATE TABLE `PassedPilot` (
  `PassedPilotID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `BestAttemptResult` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `PassedPilot`
--

INSERT INTO `PassedPilot` (`PassedPilotID`, `TestID`, `DriverID`, `DateTimeCompleted`, `BestAttemptResult`) VALUES
(26, 2, 47, '2024-05-17 02:06:26', 13.097),
(27, 2, 50, '2024-05-17 12:34:51', 8.292),
(28, 2, 48, '2024-05-17 12:46:56', 13.494),
(29, 2, 51, '2024-05-17 12:53:54', 10.705),
(30, 2, 50, '2024-05-17 13:02:13', 4.979),
(31, 2, 47, '2024-05-17 13:11:59', 10.161),
(32, 2, 50, '2024-05-18 03:14:22', 8.131);

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_Baevsky`
--

CREATE TABLE `Passed_Baevsky` (
  `PassedBaevskyID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `AdaptabilityCoefficient` float DEFAULT NULL,
  `PulsePhoto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PressurePhoto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_Baevsky`
--

INSERT INTO `Passed_Baevsky` (`PassedBaevskyID`, `TestID`, `DriverID`, `DateTimeCompleted`, `AdaptabilityCoefficient`, `PulsePhoto`, `PressurePhoto`) VALUES
(24, 6, 47, '2024-05-17 02:10:02', 2.755, '../../img_user/_44476.970.jpg', '../../img_user/davlenie-verhnee-i-nizhnee.jpg'),
(25, 6, 50, '2024-05-17 12:39:16', 2.739, '../../img_user/_44476.970.jpg', '../../img_user/davlenie-verhnee-i-nizhnee.jpg'),
(26, 6, 48, '2024-05-17 12:49:37', 2.58, '../../img_user/_034954857 (2) — копия.jpg', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg'),
(27, 6, 51, '2024-05-17 12:57:33', 2.616, '../../img_user/_034954857 (2) — копия.jpg', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg'),
(28, 6, 50, '2024-05-17 13:04:00', 2.89, '../../img_user/_44476.970.jpg', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg'),
(29, 6, 47, '2024-05-17 13:13:50', 3.215, '../../img_user/_034954857 (2) — копия.jpg', '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png'),
(30, 6, 50, '2024-05-18 03:18:31', 2.83, '../../img_user/_44476.970.jpg', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg');

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_Dynamometry`
--

CREATE TABLE `Passed_Dynamometry` (
  `PassedDynamometryID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `LeftHandStrength` float DEFAULT NULL,
  `RightHandStrength` float DEFAULT NULL,
  `LeftPhoto` text COLLATE utf8mb4_unicode_ci,
  `RightPhoto` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_Dynamometry`
--

INSERT INTO `Passed_Dynamometry` (`PassedDynamometryID`, `TestID`, `DriverID`, `DateTimeCompleted`, `LeftHandStrength`, `RightHandStrength`, `LeftPhoto`, `RightPhoto`) VALUES
(30, 5, 46, '2024-05-16 13:22:48', 123, 213, '../../img_user/photo1712833136.jpeg', '../../img_user/034954857 (2).jpg'),
(36, 5, 47, '2024-05-17 02:08:15', 77, 59, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png'),
(37, 5, 50, '2024-05-17 12:38:35', 75, 67, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png'),
(38, 5, 48, '2024-05-17 12:48:55', 70, 80, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png'),
(39, 5, 51, '2024-05-17 12:55:59', 60, 65, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png'),
(40, 5, 50, '2024-05-17 13:03:26', 70, 80, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png'),
(41, 5, 47, '2024-05-17 13:13:25', 80, 70, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png'),
(42, 5, 50, '2024-05-18 03:17:35', 56, 76, '../../img_user/1c2eb6a93754e1d103fe35f167641a99.png', '../../img_user/23cofw40m04bqnz1vxgdepblwngag3i3.png');

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_PulseOximetry`
--

CREATE TABLE `Passed_PulseOximetry` (
  `PassedPulseOximetryID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `PulseRate` int(11) DEFAULT NULL,
  `BloodOxygenSaturation` int(11) DEFAULT NULL,
  `ResultPhoto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_PulseOximetry`
--

INSERT INTO `Passed_PulseOximetry` (`PassedPulseOximetryID`, `TestID`, `DriverID`, `DateTimeCompleted`, `PulseRate`, `BloodOxygenSaturation`, `ResultPhoto`) VALUES
(14, 8, 47, '2024-05-17 02:10:39', 60, 100, '../../img_user/_44476.970.jpg'),
(15, 8, 50, '2024-05-17 12:41:36', 70, 90, '../../img_user/_44476.970.jpg'),
(16, 8, 48, '2024-05-17 12:50:38', 70, 100, '../../img_user/_44476.970.jpg'),
(17, 8, 51, '2024-05-17 13:00:41', 100, 80, '../../img_user/_44476.970.jpg'),
(18, 8, 50, '2024-05-17 13:04:13', 80, 90, '../../img_user/_44476.970.jpg'),
(19, 8, 47, '2024-05-17 13:14:36', 70, 100, '../../img_user/_034954857 (2) — копия.jpg'),
(20, 8, 50, '2024-05-18 03:20:35', 90, 80, '../../img_user/_034954857 (2) — копия.jpg'),
(21, 8, 52, '2024-05-18 04:41:36', 123, 123, '../../img_user/Безымянный проект.mp4'),
(22, 8, 50, '2024-05-18 04:43:23', 123, 123, '../../img_user/Безымянный проект.mp4');

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_SAN`
--

CREATE TABLE `Passed_SAN` (
  `PassedSANID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `SelfPerception` float DEFAULT NULL,
  `Activity` float DEFAULT NULL,
  `Mood` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_SAN`
--

INSERT INTO `Passed_SAN` (`PassedSANID`, `TestID`, `DriverID`, `DateTimeCompleted`, `SelfPerception`, `Activity`, `Mood`) VALUES
(11, 7, 47, '2024-05-17 02:11:28', 6.1, 5.9, 6.1),
(12, 7, 50, '2024-05-17 12:41:14', 6.4, 6, 6.2),
(13, 7, 48, '2024-05-17 12:49:59', 6, 6, 6),
(14, 7, 51, '2024-05-17 12:58:27', 3.7, 3.1, 2.6),
(15, 7, 50, '2024-05-17 13:01:57', 6.4, 6.3, 6.4),
(16, 7, 47, '2024-05-17 13:14:18', 6.1, 5.9, 6.1),
(17, 7, 50, '2024-05-18 03:19:46', 3.2, 3.6, 2.9);

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_Shulte_Table`
--

CREATE TABLE `Passed_Shulte_Table` (
  `PassedShulteTableID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `Count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_Shulte_Table`
--

INSERT INTO `Passed_Shulte_Table` (`PassedShulteTableID`, `TestID`, `DriverID`, `DateTimeCompleted`, `Count`) VALUES
(14, 4, 47, '2024-05-17 02:07:22', 17),
(15, 4, 50, '2024-05-17 12:35:56', 20),
(16, 4, 48, '2024-05-17 12:48:10', 25),
(17, 4, 51, '2024-05-17 12:55:29', 10),
(18, 4, 50, '2024-05-17 13:03:09', 18),
(19, 4, 47, '2024-05-17 13:12:58', 13),
(20, 4, 50, '2024-05-18 03:16:34', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_Tonometer`
--

CREATE TABLE `Passed_Tonometer` (
  `PassedTonometerID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `UpperPressure` decimal(10,2) DEFAULT NULL,
  `LowerPressure` decimal(10,2) DEFAULT NULL,
  `ImagePath` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_Tonometer`
--

INSERT INTO `Passed_Tonometer` (`PassedTonometerID`, `TestID`, `DriverID`, `DateTimeCompleted`, `UpperPressure`, `LowerPressure`, `ImagePath`) VALUES
(119, 9, 47, '2024-05-17 02:10:51', '120.00', '80.00', '../../img_user/davlenie-verhnee-i-nizhnee.jpg'),
(120, 9, 50, '2024-05-17 12:41:51', '130.00', '84.00', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg'),
(121, 9, 48, '2024-05-17 12:50:57', '130.00', '90.00', '../../img_user/davlenie-verhnee-i-nizhnee.jpg'),
(122, 9, 51, '2024-05-17 13:00:52', '150.00', '120.00', '../../img_user/davlenie-verhnee-i-nizhnee.jpg'),
(123, 9, 50, '2024-05-17 13:04:24', '130.00', '80.00', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg'),
(124, 9, 47, '2024-05-17 13:14:47', '120.00', '80.00', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg'),
(125, 9, 50, '2024-05-18 03:20:59', '152.00', '52.00', '../../img_user/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg');

-- --------------------------------------------------------

--
-- Структура таблицы `Passed_Traffic_Lights_FalseStarts`
--

CREATE TABLE `Passed_Traffic_Lights_FalseStarts` (
  `PassedTrafficLightFalseStartsID` int(11) NOT NULL,
  `TestID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `DateTimeCompleted` datetime DEFAULT NULL,
  `Count` int(11) DEFAULT NULL,
  `AverageTimeTaken` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Passed_Traffic_Lights_FalseStarts`
--

INSERT INTO `Passed_Traffic_Lights_FalseStarts` (`PassedTrafficLightFalseStartsID`, `TestID`, `DriverID`, `DateTimeCompleted`, `Count`, `AverageTimeTaken`) VALUES
(3, 3, 47, '2024-05-17 02:06:53', 0, 300.333),
(4, 3, 50, '2024-05-17 12:35:26', 1, 260.667),
(5, 3, 48, '2024-05-17 12:47:24', 7, 314.667),
(6, 3, 51, '2024-05-17 12:54:39', 4, 1211.67),
(7, 3, 50, '2024-05-17 13:02:40', 1, 746.667),
(8, 3, 47, '2024-05-17 13:12:29', 5, 538.333),
(9, 3, 50, '2024-05-18 03:15:35', 7, 871);

-- --------------------------------------------------------

--
-- Структура таблицы `pressure_reference`
--

CREATE TABLE `pressure_reference` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age_range` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_value` decimal(5,2) DEFAULT NULL,
  `max_value` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `pressure_reference`
--

INSERT INTO `pressure_reference` (`id`, `title`, `age_range`, `gender`, `min_value`, `max_value`) VALUES
(1, 'Нормальное верхнее давление', '18-30', 'Мужской', '100.00', '120.00'),
(2, 'Нормальное верхнее давление', '18-30', 'Женский', '95.00', '110.00'),
(3, 'Нормальное верхнее давление', '31-45', 'Мужской', '105.00', '130.00'),
(4, 'Нормальное верхнее давление', '31-45', 'Женский', '100.00', '120.00'),
(5, 'Нормальное верхнее давление', '46-60', 'Мужской', '110.00', '135.00'),
(6, 'Нормальное верхнее давление', '46-60', 'Женский', '105.00', '130.00'),
(7, 'Нормальное верхнее давление', '61+', 'Мужской', '115.00', '140.00'),
(8, 'Нормальное верхнее давление', '61+', 'Женский', '110.00', '135.00');

-- --------------------------------------------------------

--
-- Структура таблицы `Tests`
--

CREATE TABLE `Tests` (
  `TestID` int(11) NOT NULL,
  `Title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Tests`
--

INSERT INTO `Tests` (`TestID`, `Title`, `Description`, `img`) VALUES
(2, 'Тест пилотов', 'Тест на реакцию, где пользователь должен удерживая курсор на красном квадрате уклоняться от препятствий \r\n ', 'pilot.png'),
(3, 'Светофор', 'реагировать на изменение светофора', 'svetofor.png'),
(4, 'Таблицы Шульте', 'по порядку на скорость раставить цифры', 'shulte.png'),
(5, 'Кистевая динамометрия', 'Кистевая динамометрия – метод определения сгибательной силы кисти.', 'dinamometr.png'),
(6, 'Тест Баевского', 'Определение уровня физического состояния и адаптационного потенциала', 'baevsky.png'),
(7, 'Опросник сан', ' Оперативная оценка самочувствия, активности и настроения. Описание методики: Опросник состоит из 30 пар противоположных характеристик', 'san.png'),
(8, 'Пульсоксиметрия', 'Измерение пульса и содержание кислорода в крови', 'puls.jpeg'),
(9, 'Тонометр', 'Измерение артериального давления', 'tonometr.jpeg');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Administrators`
--
ALTER TABLE `Administrators`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Индексы таблицы `AssignedTests`
--
ALTER TABLE `AssignedTests`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `DriverID` (`DriverID`),
  ADD KEY `TestID` (`TestID`);

--
-- Индексы таблицы `Drivers`
--
ALTER TABLE `Drivers`
  ADD PRIMARY KEY (`DriverID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Индексы таблицы `drivertests`
--
ALTER TABLE `drivertests`
  ADD PRIMARY KEY (`DriverTestID`),
  ADD KEY `fk_DriverID1` (`DriverID`),
  ADD KEY `fk_TestID` (`TestID`);

--
-- Индексы таблицы `LicenseCategories`
--
ALTER TABLE `LicenseCategories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `fk_DriverID` (`DriverID`);

--
-- Индексы таблицы `PassedPilot`
--
ALTER TABLE `PassedPilot`
  ADD PRIMARY KEY (`PassedPilotID`),
  ADD KEY `TestID` (`TestID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Индексы таблицы `Passed_Baevsky`
--
ALTER TABLE `Passed_Baevsky`
  ADD PRIMARY KEY (`PassedBaevskyID`),
  ADD KEY `TestID` (`TestID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Индексы таблицы `Passed_Dynamometry`
--
ALTER TABLE `Passed_Dynamometry`
  ADD PRIMARY KEY (`PassedDynamometryID`);

--
-- Индексы таблицы `Passed_PulseOximetry`
--
ALTER TABLE `Passed_PulseOximetry`
  ADD PRIMARY KEY (`PassedPulseOximetryID`),
  ADD KEY `TestID` (`TestID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Индексы таблицы `Passed_SAN`
--
ALTER TABLE `Passed_SAN`
  ADD PRIMARY KEY (`PassedSANID`),
  ADD KEY `TestID` (`TestID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Индексы таблицы `Passed_Shulte_Table`
--
ALTER TABLE `Passed_Shulte_Table`
  ADD PRIMARY KEY (`PassedShulteTableID`),
  ADD KEY `TestID` (`TestID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Индексы таблицы `Passed_Tonometer`
--
ALTER TABLE `Passed_Tonometer`
  ADD PRIMARY KEY (`PassedTonometerID`);

--
-- Индексы таблицы `Passed_Traffic_Lights_FalseStarts`
--
ALTER TABLE `Passed_Traffic_Lights_FalseStarts`
  ADD PRIMARY KEY (`PassedTrafficLightFalseStartsID`),
  ADD KEY `TestID` (`TestID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Индексы таблицы `pressure_reference`
--
ALTER TABLE `pressure_reference`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Tests`
--
ALTER TABLE `Tests`
  ADD PRIMARY KEY (`TestID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Administrators`
--
ALTER TABLE `Administrators`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `AssignedTests`
--
ALTER TABLE `AssignedTests`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `Drivers`
--
ALTER TABLE `Drivers`
  MODIFY `DriverID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT для таблицы `drivertests`
--
ALTER TABLE `drivertests`
  MODIFY `DriverTestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `LicenseCategories`
--
ALTER TABLE `LicenseCategories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=469;

--
-- AUTO_INCREMENT для таблицы `PassedPilot`
--
ALTER TABLE `PassedPilot`
  MODIFY `PassedPilotID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `Passed_Baevsky`
--
ALTER TABLE `Passed_Baevsky`
  MODIFY `PassedBaevskyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT для таблицы `Passed_Dynamometry`
--
ALTER TABLE `Passed_Dynamometry`
  MODIFY `PassedDynamometryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT для таблицы `Passed_PulseOximetry`
--
ALTER TABLE `Passed_PulseOximetry`
  MODIFY `PassedPulseOximetryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `Passed_SAN`
--
ALTER TABLE `Passed_SAN`
  MODIFY `PassedSANID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `Passed_Shulte_Table`
--
ALTER TABLE `Passed_Shulte_Table`
  MODIFY `PassedShulteTableID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `Passed_Tonometer`
--
ALTER TABLE `Passed_Tonometer`
  MODIFY `PassedTonometerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT для таблицы `Passed_Traffic_Lights_FalseStarts`
--
ALTER TABLE `Passed_Traffic_Lights_FalseStarts`
  MODIFY `PassedTrafficLightFalseStartsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `pressure_reference`
--
ALTER TABLE `pressure_reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `Tests`
--
ALTER TABLE `Tests`
  MODIFY `TestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `AssignedTests`
--
ALTER TABLE `AssignedTests`
  ADD CONSTRAINT `assignedtests_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `Administrators` (`AdminID`),
  ADD CONSTRAINT `assignedtests_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`),
  ADD CONSTRAINT `assignedtests_ibfk_3` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`);

--
-- Ограничения внешнего ключа таблицы `drivertests`
--
ALTER TABLE `drivertests`
  ADD CONSTRAINT `fk_DriverID1` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_TestID` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `LicenseCategories`
--
ALTER TABLE `LicenseCategories`
  ADD CONSTRAINT `fk_DriverID` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);

--
-- Ограничения внешнего ключа таблицы `PassedPilot`
--
ALTER TABLE `PassedPilot`
  ADD CONSTRAINT `passedpilot_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`),
  ADD CONSTRAINT `passedpilot_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);

--
-- Ограничения внешнего ключа таблицы `Passed_Baevsky`
--
ALTER TABLE `Passed_Baevsky`
  ADD CONSTRAINT `passed_baevsky_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`),
  ADD CONSTRAINT `passed_baevsky_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);

--
-- Ограничения внешнего ключа таблицы `Passed_PulseOximetry`
--
ALTER TABLE `Passed_PulseOximetry`
  ADD CONSTRAINT `passed_pulseoximetry_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`),
  ADD CONSTRAINT `passed_pulseoximetry_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);

--
-- Ограничения внешнего ключа таблицы `Passed_SAN`
--
ALTER TABLE `Passed_SAN`
  ADD CONSTRAINT `passed_san_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`),
  ADD CONSTRAINT `passed_san_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);

--
-- Ограничения внешнего ключа таблицы `Passed_Shulte_Table`
--
ALTER TABLE `Passed_Shulte_Table`
  ADD CONSTRAINT `passed_shulte_table_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`),
  ADD CONSTRAINT `passed_shulte_table_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);

--
-- Ограничения внешнего ключа таблицы `Passed_Traffic_Lights_FalseStarts`
--
ALTER TABLE `Passed_Traffic_Lights_FalseStarts`
  ADD CONSTRAINT `passed_traffic_lights_falsestarts_ibfk_1` FOREIGN KEY (`TestID`) REFERENCES `Tests` (`TestID`),
  ADD CONSTRAINT `passed_traffic_lights_falsestarts_ibfk_2` FOREIGN KEY (`DriverID`) REFERENCES `Drivers` (`DriverID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
