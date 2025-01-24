SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

UPDATE `phpls_fields` SET `type` = 'ro' WHERE `type` = 'readonly';
UPDATE `phpls_listingfields` SET `type` = 'ro' WHERE `type` = 'readonly';
UPDATE `phpls_widgetfields` SET `type` = 'ro' WHERE `type` = 'readonly';

ALTER TABLE `phpls_uploadtypes` CHANGE `max_size` `max_size` VARCHAR(255) NULL DEFAULT NULL; 

ALTER TABLE `phpls_listings` ADD `_page` tinyint(1) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_products` ADD `_page` tinyint(1) UNSIGNED DEFAULT NULL;
UPDATE `phpls_listings` SET `_page` = 1;
UPDATE `phpls_products` SET `_page` = 1;

ALTER TABLE `phpls_types` ADD `active` tinyint(1) UNSIGNED DEFAULT NULL;
UPDATE `phpls_types` SET `active` = 1;

CREATE TABLE `phpls_socialprofiletypes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `icon_filename` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `phpls_socialprofiletypes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `phpls_socialprofiletypes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO `phpls_socialprofiletypes` (`id`, `name`, `icon_filename`) VALUES
(1, 'Facebook', 'facebook.png'),
(2, 'Twitter', 'twitter.png'),
(3, 'Instagram', 'instagram.png'),
(4, 'LinkedIn', 'linkedin.png'),
(5, 'YouTube', 'youtube.png'),
(6, 'Pinterest', 'pinterest.png'),
(7, 'Snapchat', 'snapchat.png'),
(8, 'Reddit', 'reddit.png'),
(9, 'TikTok', 'tiktok.png'),
(10, 'Tumblr', 'tumblr.png'),
(11, 'Flickr', 'flickr.png'),
(12, 'Quora', 'quora.png'),
(13, 'Vimeo', 'vimeo.png'),
(14, 'Twitch', 'twitch.png'),
(15, 'Yelp', 'yelp.png');

ALTER TABLE `phpls_listingfields` ADD `socialprofiletype_id` bigint(20) UNSIGNED DEFAULT NULL;

UPDATE `phpls_themes` SET `version` = `version` + 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

