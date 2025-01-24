SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------


DROP TABLE `phpls_themes`;
ALTER TABLE `phpls_pages` DROP COLUMN `theme_id`;
ALTER TABLE `phpls_widgets` DROP COLUMN `theme_id`;

--
-- Table structure for table `phpls_category_export`
--

CREATE TABLE `phpls_category_export` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `export_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_cronjobs`
--

UPDATE `phpls_cronjobs` SET `exec_interval` = '1440' WHERE `name` = 'daily';

INSERT INTO `phpls_cronjobs` (`id`, `locked`, `name`, `exec_interval`, `last_run_datetime`, `response`) VALUES
(9, NULL, 'export', 5, NULL, NULL),
(10, NULL, 'import', 5, NULL, NULL),
(11, NULL, 'sitemap', 1440, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_exports`
--

CREATE TABLE `phpls_exports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_export_listingfield`
--

CREATE TABLE `phpls_export_listingfield` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `export_id` bigint(20) UNSIGNED NOT NULL,
  `listingfield_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_export_pricing`
--

CREATE TABLE `phpls_export_pricing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `export_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

DELETE FROM `phpls_gateways` WHERE id > 1;

INSERT INTO `phpls_gateways` (`id`, `active`, `offsite`, `subscription`, `offline`, `name`, `description`, `slug`, `settings`, `weight`) VALUES
(2, 1, 1, NULL, NULL, '{\"en\":\"PayPal Checkout API\"}', '{\"en\":\"\"}', 'paypal', '{\"currency\":\"USD\",\"clientId\":\"\",\"secret\":\"\",\"testMode\":\"1\"}', 2),
(3, 1, 1, NULL, NULL, '{\"en\":\"Authorize.net API\"}', '{\"en\":\"\"}', 'authorizenet', '{\"currency\":\"USD\",\"authName\":\"test\",\"transactionKey\":\"test\",\"signatureKey\":\"test\",\"testMode\":\"1\"}', 3),
(4, 1, 1, NULL, NULL, '{\"en\":\"Mollie API\"}', '{\"en\":\"\"}', 'mollie', '{\"currency\":\"USD\",\"apiKey\":\"test\"}', 4);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_imports`
--

CREATE TABLE `phpls_imports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `claimed` tinyint(1) UNSIGNED DEFAULT NULL,
  `pricing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notification` tinyint(1) UNSIGNED DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listings`
--

ALTER TABLE `phpls_listings` ADD `import_id` bigint(20) UNSIGNED DEFAULT NULL;
ALTER TABLE `phpls_listings` MODIFY `event_weeks` text DEFAULT NULL;

--
-- Table structure for table `phpls_updates`
--

ALTER TABLE `phpls_updates` MODIFY `event_frequency` text DEFAULT NULL;
ALTER TABLE `phpls_updates` MODIFY `event_weekdays` text DEFAULT NULL;
ALTER TABLE `phpls_updates` MODIFY `event_weeks` text DEFAULT NULL;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_users`
--

ALTER TABLE `phpls_users` ADD `import_id` bigint(20) UNSIGNED DEFAULT NULL;

INSERT INTO `phpls_userroles` (`id`, `name`) VALUES
(17, 'admin_import'),
(18, 'admin_export');

--
-- Indexes for table `phpls_category_export`
--
ALTER TABLE `phpls_category_export`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_exports`
--
ALTER TABLE `phpls_exports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_export_listingfield`
--
ALTER TABLE `phpls_export_listingfield`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_export_pricing`
--
ALTER TABLE `phpls_export_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_imports`
--
ALTER TABLE `phpls_imports`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `phpls_category_export`
--
ALTER TABLE `phpls_category_export`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_exports`
--

ALTER TABLE `phpls_exports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_export_listingfield`
--
ALTER TABLE `phpls_export_listingfield`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_export_pricing`
--
ALTER TABLE `phpls_export_pricing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_imports`
--
ALTER TABLE `phpls_imports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
