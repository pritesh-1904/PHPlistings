SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

CREATE TABLE `phpls_themes` (
  `id` bigint UNSIGNED NOT NULL,
  `customizable` tinyint UNSIGNED DEFAULT NULL,
  `version` mediumint UNSIGNED DEFAULT NULL,
  `name` text,
  `slug` varchar(255) NOT NULL,
  `settings` mediumtext CHARACTER SET utf8mb4,
  `weight` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `phpls_themes` (`id`, `customizable`, `version`, `name`, `slug`, `settings`, `weight`) VALUES
(1, NULL, 1, 'Default', 'default', '[]', 1);

ALTER TABLE `phpls_widgetmenuitems` ADD `highlighted` tinyint(1) UNSIGNED DEFAULT NULL;

ALTER TABLE `phpls_themes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `phpls_themes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
