SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

INSERT INTO `phpls_settingfields` (`id`, `settinggroup_id`, `type`, `upload_id`, `name`, `options_type`, `options`, `constraints`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(60, 2, 'separator', NULL, 'separator_google', NULL, NULL, NULL, NULL, 'account_google', NULL, NULL, NULL, 60),
(61, 2, 'text', NULL, 'google_client_id', NULL, NULL, NULL, NULL, 'google_client_id', NULL, NULL, NULL, 61),
(62, 2, 'text', NULL, 'google_client_secret', NULL, NULL, NULL, NULL, 'google_client_secret', NULL, NULL, NULL, 62);

INSERT INTO `phpls_settings` (`id`, `settinggroup_id`, `name`, `value`) VALUES
(44, 2, 'google_client_id', NULL),
(45, 2, 'google_client_secret', NULL);

INSERT INTO `phpls_uploadtypes` (`id`, `customizable`, `public`, `name`, `max_files`, `max_size`, `small_image_resize_type`, `small_image_width`, `small_image_height`, `small_image_quality`, `medium_image_resize_type`, `medium_image_width`, `medium_image_height`, `medium_image_quality`, `large_image_resize_type`, `large_image_width`, `large_image_height`, `large_image_quality`, `watermark_file_path`, `watermark_position_vertical`, `watermark_position_horizontal`, `watermark_transparency`, `cropbox_width`, `cropbox_height`) VALUES
(20, NULL, NULL, '{\"en\":\"Two-column Teaser Image\"}', 1, 10, 1, 300, 225, 95, 1, 400, 300, 95, 1, 1024, 768, 95, '', 'top', 'left', 50, 300, 225);

INSERT INTO `phpls_filetype_uploadtype` (`filetype_id`, `uploadtype_id`) VALUES
(1, 20),
(2, 20),
(3, 20);

INSERT INTO `phpls_widgets` (`name`, `slug`) VALUES
('{\"en\":\"2-column Teaser\"}', 'twocolumnteaser');

INSERT INTO `phpls_widgets` (`name`, `slug`) VALUES
('{\"en\":\"User\"}', 'user');

INSERT INTO `phpls_gateways` (`active`, `offsite`, `subscription`, `offline`, `name`, `description`, `slug`, `settings`, `weight`) VALUES
(1, 1, 1, NULL, '{\"en\":\"Stripe Subscription\"}', '{\"en\":\"\"}', 'stripe', '{\"currency\":\"USD\",\"apiKey\":\"test\",\"publicKey\":\"test\",\"webhookSecret\":\"test\"}', 5);

ALTER TABLE `phpls_pages` ADD `active` tinyint(1) UNSIGNED DEFAULT NULL;
UPDATE `phpls_pages` SET active = 1;

ALTER TABLE `phpls_categories` MODIFY `meta_title` text DEFAULT NULL;

ALTER TABLE `phpls_cronjobs` MODIFY `response` text DEFAULT NULL;

ALTER TABLE `phpls_emails` MODIFY `error` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `failed` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `from_email` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `from_name` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `to_email` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `to_name` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `reply_to` text DEFAULT NULL;
ALTER TABLE `phpls_emails` MODIFY `subject` text DEFAULT NULL;

ALTER TABLE `phpls_emailtemplates` MODIFY `from_email` text DEFAULT NULL;
ALTER TABLE `phpls_emailtemplates` MODIFY `from_name` text DEFAULT NULL;
ALTER TABLE `phpls_emailtemplates` MODIFY `to_email` text DEFAULT NULL;
ALTER TABLE `phpls_emailtemplates` MODIFY `to_name` text DEFAULT NULL;
ALTER TABLE `phpls_emailtemplates` MODIFY `reply_to` text DEFAULT NULL;
ALTER TABLE `phpls_emailtemplates` MODIFY `subject` text DEFAULT NULL;

ALTER TABLE `phpls_fieldconstraints` MODIFY `value` text DEFAULT NULL;

ALTER TABLE `phpls_fieldoptions` MODIFY `name` text NOT NULL;

ALTER TABLE `phpls_files` MODIFY `name` text DEFAULT NULL;
ALTER TABLE `phpls_files` MODIFY `extension` text DEFAULT NULL;
ALTER TABLE `phpls_files` MODIFY `crop_data` text DEFAULT NULL;
ALTER TABLE `phpls_files` MODIFY `title` text DEFAULT NULL;

ALTER TABLE `phpls_filetypes` MODIFY `mime` text NOT NULL;
ALTER TABLE `phpls_filetypes` MODIFY `extension` text NOT NULL;

ALTER TABLE `phpls_listingfieldconstraints` MODIFY `value` text DEFAULT NULL;

ALTER TABLE `phpls_listingfieldoptions` MODIFY `name` text NOT NULL;
ALTER TABLE `phpls_listingfieldoptions` MODIFY `schema_itemprop` text DEFAULT NULL;

ALTER TABLE `phpls_listingfields` MODIFY `schema_itemprop` text DEFAULT NULL;

ALTER TABLE `phpls_listings` MODIFY `zip` text DEFAULT NULL;

ALTER TABLE `phpls_locations` MODIFY `meta_title` text DEFAULT NULL;

ALTER TABLE `phpls_messages` MODIFY `title` text NOT NULL;

ALTER TABLE `phpls_pages` MODIFY `meta_title` text DEFAULT NULL;

ALTER TABLE `phpls_reviews` MODIFY `title` text NOT NULL;

ALTER TABLE `phpls_transactions` MODIFY `reference` text DEFAULT NULL;

ALTER TABLE `phpls_updates` MODIFY `address` text DEFAULT NULL;
ALTER TABLE `phpls_updates` MODIFY `zip` text DEFAULT NULL;

ALTER TABLE `phpls_uploadtypes` MODIFY `watermark_file_path` text DEFAULT NULL;

ALTER TABLE `phpls_users` MODIFY `first_name` text DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `last_name` text DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `email` text DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `address` text DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `zip` text DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `latitude` decimal(9,6) DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `longitude` decimal(9,6) DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `zoom` tinyint(2) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `timezone` char(6) DEFAULT NULL;
ALTER TABLE `phpls_users` MODIFY `location_id` bigint(20) UNSIGNED DEFAULT NULL;

ALTER TABLE `phpls_widgetfieldconstraints` MODIFY `value` text DEFAULT NULL;

ALTER TABLE `phpls_widgetfieldoptions` MODIFY `name` text NOT NULL;

ALTER TABLE `phpls_widgetmenuitems` MODIFY `link` text DEFAULT NULL;

ALTER TABLE `phpls_listings`
  ADD KEY `locationIdx` (`location_id`),
  ADD KEY `categoryIdx` (`category_id`);

ALTER TABLE `phpls_users`
  ADD KEY `locationIdx` (`location_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
