SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

ALTER TABLE `phpls_files` ADD `_legacy` tinyint(1) UNSIGNED DEFAULT NULL;
UPDATE `phpls_files` SET `_legacy` = 1;

ALTER TABLE `phpls_files` ADD `image_width` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `image_height` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `small_image_width` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `small_image_height` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `medium_image_width` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `medium_image_height` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `large_image_width` smallint(5) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_files` ADD `large_image_height` smallint(5) UNSIGNED DEFAULT NULL;

CREATE TABLE `phpls_badges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text,
  `image_id` varchar(255) DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `phpls_badge_listing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `badge_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `product` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `phpls_badge_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `badge_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `phpls_badges`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `phpls_badges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `phpls_badge_listing`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `phpls_badge_listing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `phpls_badge_product`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `phpls_badge_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `phpls_uploadtypes` (`id`, `customizable`, `public`, `name`, `max_files`, `max_size`, `small_image_resize_type`, `small_image_width`, `small_image_height`, `small_image_quality`, `medium_image_resize_type`, `medium_image_width`, `medium_image_height`, `medium_image_quality`, `large_image_resize_type`, `large_image_width`, `large_image_height`, `large_image_quality`, `watermark_file_path`, `watermark_position_vertical`, `watermark_position_horizontal`, `watermark_transparency`, `cropbox_width`, `cropbox_height`) VALUES
(40, NULL, NULL, '{\"en\":\"Listing Badge\"}', 1, 10, 1, 200, 200, 95, 1, 300, 300, 95, 1, 400, 400, 95, '', 'top', 'left', 50, 300, 300);

INSERT INTO `phpls_filetype_uploadtype` (`filetype_id`, `uploadtype_id`) VALUES
(1, 40),
(2, 40),
(3, 40),
(5, 40);

INSERT INTO `phpls_socialprofiletypes` (`id`, `name`, `icon_filename`) VALUES
(19, 'Threads', 'threads.png'),
(20, 'TripAdvisor', 'tripadvisor.png'),
(21, 'Bluesky', 'bluesky.png');

UPDATE `phpls_socialprofiletypes` SET `name` = 'X', `icon_filename` = 'x.png' WHERE `id` = 2;

UPDATE `phpls_themes` SET `version` = `version` + 1;

ALTER TABLE `phpls_stats`
  ADD INDEX `lookupIdx` (`type`, `type_id`, `date`);

ALTER TABLE `phpls_listings` ADD `_dofollow` tinyint(1) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_products` ADD `_dofollow` tinyint(1) UNSIGNED DEFAULT NULL;

ALTER TABLE `phpls_discounts` ADD `immutable` tinyint(1) UNSIGNED DEFAULT NULL;
UPDATE `phpls_discounts` SET `immutable` = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

