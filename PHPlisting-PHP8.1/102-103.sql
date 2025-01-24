SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

ALTER TABLE `phpls_page_widget` ADD `comment` text DEFAULT NULL;
ALTER TABLE `phpls_page_widget` ADD `access_level` tinyint(1) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_page_widget` ADD `access_level_pricing_ids` text DEFAULT NULL;

UPDATE `phpls_page_widget` SET `access_level` = 1;

INSERT INTO `phpls_pages` (`id`, `active`, `type_id`, `customizable`, `title`, `slug`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(1000, 1, NULL, NULL, '{\"en\":\"Maintenance Mode\"}', 'maintenance', NULL, NULL, NULL);

INSERT INTO `phpls_page_widget` (`active`, `page_id`, `widget_id`, `weight`, `settings`, `access_level`) VALUES
(1, 1000, 1, 1000, '[]', 1),
(1, 1000, 33, 1001, '[]', 1),
(1, 1000, 2, 1002, '[]', 1);

INSERT INTO `phpls_widgets` (`id`, `name`, `slug`) VALUES
(33, '{\"en\":\"Maintenance\"}', 'maintenance');

INSERT INTO `phpls_settingfields` (`id`, `settinggroup_id`, `type`, `upload_id`, `name`, `options_type`, `options`, `constraints`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(63, 1, 'separator', NULL, 'separator_maintenance', NULL, NULL, NULL, NULL, 'general_maintenance', NULL, NULL, NULL, 63),
(64, 1, 'toggle', NULL, 'maintenance', NULL, NULL, NULL, NULL, 'maintenance', NULL, NULL, NULL, 64);

INSERT INTO `phpls_settings` (`id`, `settinggroup_id`, `name`, `value`) VALUES
(46, 1, 'maintenance', NULL);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
