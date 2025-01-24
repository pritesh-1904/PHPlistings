SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

ALTER TABLE `phpls_category_listing`
    ADD INDEX `lookupIdx` (`category_id`,`listing_id`);

ALTER TABLE `phpls_category_listingfield` 
    ADD INDEX `lookupIdx` (`category_id`, `listingfield_id`);

ALTER TABLE `phpls_category_product` 
    ADD INDEX `lookupIdx` (`category_id`, `product_id`);

ALTER TABLE `phpls_category_update` 
    ADD INDEX `lookupIdx` (`category_id`, `update_id`);

ALTER TABLE `phpls_listingfield_product` 
    ADD INDEX `lookupIdx` (`listingfield_id`, `product_id`);

ALTER TABLE `phpls_listing_linked` 
    ADD INDEX `lookupIdx` (`child_id`, `parent_id`);

ALTER TABLE `phpls_page_widget` 
    ADD INDEX `lookupIdx` (`page_id`);

ALTER TABLE `phpls_usergroup_userrole` 
    ADD INDEX `lookupIdx` (`usergroup_id`, `userrole_id`);

ALTER TABLE `phpls_page_widget` MODIFY `settings` mediumtext DEFAULT NULL;

INSERT INTO `phpls_settings` (`id`, `settinggroup_id`, `name`, `value`) VALUES
(47, 7, 'banned_email_domains', ''),
(48, 7, 'deadlinkchecker', NULL),
(49, 7, 'deadlinkchecker_interval', 7),
(50, 7, 'deadlinkchecker_retry_interval', 6),
(51, 7, 'deadlinkchecker_max_retry_count', 8),
(52, 7, 'deadlinkchecker_client_notification_retry', 4),
(53, 7, 'deadlinkchecker_admin_notification_retry', 4),
(54, 7, 'deadlinkchecker_autoremove_failed_link', NULL),
(55, 7, 'backlinkchecker', NULL),
(56, 7, 'backlinkchecker_url', ''),
(57, 7, 'backlinkchecker_url_template', '&lt;a href=&quot;{link}&quot;&gt;{site_name}&lt;/a&gt;'),
(58, 7, 'backlinkchecker_follow_only', NULL),
(59, 7, 'backlinkchecker_interval', 7),
(60, 7, 'backlinkchecker_retry_interval', 6),
(61, 7, 'backlinkchecker_max_retry_count', 8),
(62, 7, 'backlinkchecker_client_notification_retry', 4),
(63, 7, 'backlinkchecker_admin_notification_retry', 4),
(64, 7, 'backlinkchecker_cancel_failed_listing', NULL),
(65, 7, 'backlinkchecker_cancel_failed_listing_refund', 1);

INSERT INTO `phpls_settingfields` (`id`, `settinggroup_id`, `type`, `upload_id`, `name`, `options_type`, `options`, `constraints`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(65, 7, 'textarea', NULL, 'banned_email_domains', NULL, NULL, '', NULL, 'banned_email_domains', NULL, NULL, NULL, 65),
(66, 7, 'separator', NULL, 'separator_deadlinkchecker', NULL, NULL, NULL, NULL, 'other_deadlinkchecker', NULL, NULL, NULL, 66),
(67, 7, 'toggle', NULL, 'deadlinkchecker', NULL, NULL, '', NULL, 'deadlinkchecker', NULL, NULL, NULL, 67),
(68, 7, 'number', NULL, 'deadlinkchecker_interval', NULL, NULL, 'required|min:1|max:365', NULL, 'deadlinkchecker_interval', NULL, NULL, NULL, 68),
(69, 7, 'number', NULL, 'deadlinkchecker_retry_interval', NULL, NULL, 'required|min:1|max:24', NULL, 'deadlinkchecker_retry_interval', NULL, NULL, NULL, 69),
(70, 7, 'number', NULL, 'deadlinkchecker_max_retry_count', NULL, NULL, 'required|min:1|max:99', NULL, 'deadlinkchecker_max_retry_count', NULL, NULL, NULL, 70),
(71, 7, 'number', NULL, 'deadlinkchecker_client_notification_retry', NULL, NULL, 'min:1|max:99', NULL, 'deadlinkchecker_client_notification_retry', NULL, NULL, 'client', 71),
(72, 7, 'number', NULL, 'deadlinkchecker_admin_notification_retry', NULL, NULL, 'min:1|max:99', NULL, 'deadlinkchecker_admin_notification_retry', NULL, NULL, 'admin', 72),
(73, 7, 'toggle', NULL, 'deadlinkchecker_autoremove_failed_link', NULL, NULL, '', NULL, 'deadlinkchecker_autoremove_failed_link', NULL, NULL, NULL, 73),
(74, 7, 'separator', NULL, 'separator_backlinkchecker', NULL, NULL, NULL, NULL, 'other_backlinkchecker', NULL, NULL, NULL, 74),
(75, 7, 'toggle', NULL, 'backlinkchecker', NULL, NULL, '', NULL, 'backlinkchecker', NULL, NULL, NULL, 75),
(76, 7, 'url', NULL, 'backlinkchecker_url', NULL, NULL, '', NULL, 'backlinkchecker_url', NULL, NULL, 'url', 76),
(77, 7, 'text', NULL, 'backlinkchecker_url_template', NULL, NULL, 'required', NULL, 'backlinkchecker_url_template', NULL, NULL, 'template', 77),
(78, 7, 'toggle', NULL, 'backlinkchecker_follow_only', NULL, NULL, '', NULL, 'backlinkchecker_follow_only', NULL, NULL, NULL, 78),
(79, 7, 'number', NULL, 'backlinkchecker_interval', NULL, NULL, 'required|min:1|max:365', NULL, 'backlinkchecker_interval', NULL, NULL, NULL, 79),
(80, 7, 'number', NULL, 'backlinkchecker_retry_interval', NULL, NULL, 'required|min:1|max:24', NULL, 'backlinkchecker_retry_interval', NULL, NULL, NULL, 80),
(81, 7, 'number', NULL, 'backlinkchecker_max_retry_count', NULL, NULL, 'required|min:1|max:99', NULL, 'backlinkchecker_max_retry_count', NULL, NULL, NULL, 81),
(82, 7, 'number', NULL, 'backlinkchecker_client_notification_retry', NULL, NULL, 'min:1|max:99', NULL, 'backlinkchecker_client_notification_retry', NULL, NULL, 'client', 82),
(83, 7, 'number', NULL, 'backlinkchecker_admin_notification_retry', NULL, NULL, 'min:1|max:99', NULL, 'backlinkchecker_admin_notification_retry', NULL, NULL, 'admin', 83),
(84, 7, 'toggle', NULL, 'backlinkchecker_cancel_failed_listing', NULL, NULL, '', NULL, 'backlinkchecker_cancel_failed_listing', NULL, NULL, NULL, 84),
(85, 7, 'toggle', NULL, 'backlinkchecker_cancel_failed_listing_refund', NULL, NULL, '', NULL, 'backlinkchecker_cancel_failed_listing_refund', NULL, NULL, NULL, 85);

ALTER TABLE `phpls_listings` ADD `deadlinkchecker_datetime` datetime DEFAULT NULL;
ALTER TABLE `phpls_listings` ADD `deadlinkchecker_retry` tinyint(2) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_listings` ADD `deadlinkchecker_code` varchar(255) DEFAULT NULL;
ALTER TABLE `phpls_listings` ADD `backlinkchecker_datetime` datetime DEFAULT NULL;
ALTER TABLE `phpls_listings` ADD `backlinkchecker_retry` tinyint(2) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_listings` ADD `backlinkchecker_code` varchar(255) DEFAULT NULL;
ALTER TABLE `phpls_listings` ADD `backlinkchecker_linkrelation` varchar(255) DEFAULT NULL;

ALTER TABLE `phpls_listings` ADD `_backlink` tinyint(1) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_products` ADD `_backlink` tinyint(1) UNSIGNED DEFAULT NULL;

INSERT INTO `phpls_cronjobs` (`locked`, `name`, `exec_interval`, `last_run_datetime`, `response`) VALUES
(NULL, 'deadlinkchecker', 1, NULL, NULL),
(NULL, 'backlinkchecker', 1, NULL, NULL);

INSERT INTO `phpls_emailtemplates` (`id`, `customizable`, `priority`, `active`, `moderatable`, `name`, `from_email`, `from_name`, `to_email`, `to_name`, `reply_to`, `subject`, `body`) VALUES
(47, NULL, 0, 1, NULL, 'user_invalid_link', '', '', '', '', '', 'An unreachable {listing_type_singular} website URL has been detected.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} website URL is currently unreachable for our service. Please, ensure that the URL is correct and the server is active. Use the link below to update your record.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(48, NULL, 0, 1, NULL, 'admin_invalid_link', '', '', '', '', '', 'An unreachable {listing_type_singular} website URL has been detected.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;we have detected an invalid or misspelled {listing_type_singular} website URL.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(49, NULL, 0, 1, NULL, 'user_invalid_backlink', '', '', '', '', '', 'An invalid {listing_type_singular} backlink has been detected.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} backlink has been flagged as invalid. For your subscription to remain active, please, ensure the backlink is posted on the listing website and the server is up and running.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(50, NULL, 0, 1, NULL, 'admin_invalid_backlink', '', '', '', '', '', 'An invalid {listing_type_singular} backlink has been detected.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;we have detected an invalid {listing_type_singular} backlink.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(51, NULL, 0, 1, NULL, 'user_order_cancelled_invalid_backlink', '', '', '', '', '', 'Your {listing_type_singular} order has been suspended.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your listing order has been suspended due to an invalid backlink. You can re-activate this listing by choosing a new listing product.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;');

INSERT INTO `phpls_widgets` (`name`, `slug`) VALUES
('{\"en\":\"YouTube Video\"}', 'youtube');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
