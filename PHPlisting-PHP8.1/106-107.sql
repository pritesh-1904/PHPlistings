SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

INSERT INTO `phpls_widgets` (`name`, `slug`) VALUES
('{\"en\":\"Reviews\"}', 'reviews'),
('{\"en\":\"Quad-box Teaser\"}', 'quadboxteaser'),
('{\"en\":\"Popup\"}', 'popup');

INSERT INTO `phpls_uploadtypes` (`id`, `customizable`, `public`, `name`, `max_files`, `max_size`, `small_image_resize_type`, `small_image_width`, `small_image_height`, `small_image_quality`, `medium_image_resize_type`, `medium_image_width`, `medium_image_height`, `medium_image_quality`, `large_image_resize_type`, `large_image_width`, `large_image_height`, `large_image_quality`, `watermark_file_path`, `watermark_position_vertical`, `watermark_position_horizontal`, `watermark_transparency`, `cropbox_width`, `cropbox_height`) VALUES
(30, NULL, NULL, '{\"en\":\"Quad-box Teaser Image\"}', 1, 10, 1, 320, 240, 95, 1, 640, 480, 95, 1, 1024, 768, 95, '', 'top', 'left', 50, 320, 240);

INSERT INTO `phpls_filetype_uploadtype` (`filetype_id`, `uploadtype_id`) VALUES
(1, 30),
(2, 30),
(3, 30);

INSERT INTO `phpls_filetypes` (`name`, `mime`, `extension`) VALUES
('{\"en\":\"WebP Image\"}', 'image/webp', 'webp');

INSERT INTO `phpls_socialprofiletypes` (`id`, `name`, `icon_filename`) VALUES
(16, 'Alignable', 'alignable.png'),
(17, 'Indeed', 'indeed.png'),
(18, 'Nextdoor', 'nextdoor.png');

ALTER TABLE `phpls_listings` ADD `_description_links_limit` mediumint(8) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_products` ADD `_description_links_limit` mediumint(8) UNSIGNED DEFAULT NULL;

UPDATE `phpls_listings` SET `_description_links_limit` = 0;
UPDATE `phpls_products` SET `_description_links_limit` = 0;

INSERT INTO `phpls_settingfields` (`id`, `settinggroup_id`, `type`, `upload_id`, `name`, `options_type`, `options`, `constraints`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(86, 7, 'separator', NULL, 'separator_seo', NULL, NULL, NULL, NULL, 'seo_robots_txt', NULL, NULL, NULL, 86),
(87, 7, 'textarea', NULL, 'robots_txt', NULL, NULL, NULL, NULL, 'robots_txt', NULL, NULL, NULL, 87),
(88, 7, 'separator', NULL, 'separator_openai', NULL, NULL, NULL, NULL, 'openai', NULL, NULL, NULL, 88),
(89, 7, 'text', NULL, 'openai_api_key', NULL, NULL, NULL, NULL, 'openai_api_key', NULL, NULL, NULL, 89),
(90, 7, 'number', NULL, 'openai_daily_limit', NULL, NULL, NULL, NULL, 'openai_daily_limit', NULL, NULL, NULL, 90);

INSERT INTO `phpls_settings` (`id`, `settinggroup_id`, `name`, `value`) VALUES
(66, 7, 'robots_txt', 'Sitemap: {sitemap}\r\nUser-agent: *\r\nAllow: /'),
(67, 7, 'openai_api_key', ''),
(68, 7, 'openai_daily_limit', 10);

ALTER TABLE `phpls_pricings` ADD `cancellable` tinyint(1) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_orders` ADD `cancellable` tinyint(1) UNSIGNED DEFAULT NULL;

UPDATE `phpls_pricings` SET `cancellable` = null;
UPDATE `phpls_orders` SET `cancellable` = null;

INSERT INTO `phpls_emailtemplates` (`customizable`, `priority`, `active`, `moderatable`, `name`, `from_email`, `from_name`, `to_email`, `to_name`, `reply_to`, `subject`, `body`) VALUES
(NULL, 0, 1, NULL, 'admin_order_cancelled', '', '', '', '', '', '{listing_type_singular} order has been cancelled.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} order has been cancelled.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;View Listing Summary&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {listing_product}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;');

CREATE TABLE `phpls_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL,
  `added_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `phpls_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `typeIdx` (`type`);

ALTER TABLE `phpls_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

UPDATE `phpls_themes` SET `version` = `version` + 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

