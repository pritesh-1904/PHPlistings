SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_accounts`
--

CREATE TABLE `phpls_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `usergroup_id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `password` varchar(255) DEFAULT NULL,
  `last_activity_datetime` datetime DEFAULT NULL,
  `last_session_datetime` datetime DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_accounts`
--

INSERT INTO `phpls_accounts` (`id`, `user_id`, `usergroup_id`, `unique_id`, `provider`, `balance`, `password`, `last_activity_datetime`, `last_session_datetime`, `ip`) VALUES
(1, 1, 1, NULL, 'native', '0.00', '$2y$12$4b1GzEWsT/DPYOrvF6OQXeu4F.5UyTS0LDgZbMbQ7QcZ6CHsg7wPC', '2020-06-01 00:00:00', '2020-06-01 01:00:00', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_badges`
--

CREATE TABLE `phpls_badges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text,
  `image_id` varchar(255) DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_badge_listing`
--

CREATE TABLE `phpls_badge_listing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `badge_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `product` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_badge_product`
--

CREATE TABLE `phpls_badge_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `badge_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_bookmarks`
--

CREATE TABLE `phpls_bookmarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_cache`
--

CREATE TABLE `phpls_cache` (
  `cid` varchar(100) NOT NULL,
  `cdata` mediumblob DEFAULT NULL,
  `ctimestamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_categories`
--

CREATE TABLE `phpls_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `impressions` bigint(20) UNSIGNED DEFAULT NULL,
  `counter` mediumint(8) UNSIGNED DEFAULT NULL,
  `featured` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `icon_color` varchar(255) DEFAULT NULL,
  `marker_color` varchar(255) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_id` varchar(255) DEFAULT NULL,
  `header_id` varchar(255) DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `_left` bigint(20) UNSIGNED NOT NULL,
  `_right` bigint(20) UNSIGNED NOT NULL,
  `_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `_level` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_category_export`
--

CREATE TABLE `phpls_category_export` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `export_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_category_listing`
--

CREATE TABLE `phpls_category_listing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_category_listingfield`
--

CREATE TABLE `phpls_category_listingfield` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `listingfield_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_category_product`
--

CREATE TABLE `phpls_category_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_category_update`
--

CREATE TABLE `phpls_category_update` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `update_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_claims`
--

CREATE TABLE `phpls_claims` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_comments`
--

CREATE TABLE `phpls_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachments_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_cronjobs`
--

CREATE TABLE `phpls_cronjobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `locked` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `exec_interval` mediumint(8) UNSIGNED NOT NULL,
  `last_run_datetime` datetime DEFAULT NULL,
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_cronjobs`
--

INSERT INTO `phpls_cronjobs` (`id`, `locked`, `name`, `exec_interval`, `last_run_datetime`, `response`) VALUES
(1, NULL, 'everyminute', 1, NULL, NULL),
(2, NULL, 'hourly', 60, NULL, NULL),
(3, NULL, 'daily', 1440, NULL, NULL),
(4, NULL, 'mail', 1, NULL, NULL),
(5, NULL, 'counters', 80, NULL, NULL),
(6, NULL, 'orders', 70, NULL, NULL),
(7, NULL, 'invoices', 90, NULL, NULL),
(8, NULL, 'statistics', 5, NULL, NULL),
(9, NULL, 'export', 5, NULL, NULL),
(10, NULL, 'import', 5, NULL, NULL),
(11, NULL, 'sitemap', 1440, NULL, NULL),
(12, NULL, 'deadlinkchecker', 1, NULL, NULL),
(13, NULL, 'backlinkchecker', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_dates`
--

CREATE TABLE `phpls_dates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `event_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_discounts`
--

CREATE TABLE `phpls_discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(15,2) UNSIGNED NOT NULL,
  `recurring` tinyint(1) UNSIGNED DEFAULT NULL,
  `immutable` tinyint(1) UNSIGNED DEFAULT NULL,
  `new_user` tinyint(1) UNSIGNED DEFAULT NULL,
  `user_limit` mediumint(8) UNSIGNED NOT NULL,
  `peruser_limit` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_discount_pricing`
--

CREATE TABLE `phpls_discount_pricing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `discount_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_discount_required`
--

CREATE TABLE `phpls_discount_required` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `discount_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_emails`
--

CREATE TABLE `phpls_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `emailtemplate_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `error` text DEFAULT NULL,
  `failed` text DEFAULT NULL,
  `from_email` text DEFAULT NULL,
  `from_name` text DEFAULT NULL,
  `to_email` text DEFAULT NULL,
  `to_name` text DEFAULT NULL,
  `reply_to` text DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `data` text DEFAULT NULL,
  `schedule_datetime` datetime DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `processed_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_emailtemplates`
--

CREATE TABLE `phpls_emailtemplates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '5',
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `moderatable` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `from_email` text DEFAULT NULL,
  `from_name` text DEFAULT NULL,
  `to_email` text DEFAULT NULL,
  `to_name` text DEFAULT NULL,
  `reply_to` text DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `body` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_emailtemplates`
--

INSERT INTO `phpls_emailtemplates` (`id`, `customizable`, `priority`, `active`, `moderatable`, `name`, `from_email`, `from_name`, `to_email`, `to_name`, `reply_to`, `subject`, `body`) VALUES
(1, NULL, 0, 1, NULL, 'admin_account_created_approve', '', '', '', '', '', ' New user account is awaiting your approval.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new user account has been registered on {site_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;ID: {id}&lt;/p&gt;&lt;p&gt;Name: {first_name} {last_name}&lt;/p&gt;&lt;p&gt;Email: {email}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(2, NULL, 0, 1, NULL, 'admin_account_created', '', '', '', '', '', 'New user account has been registered.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new user account has been registered on {site_name}.&lt;/p&gt;&lt;p&gt;ID: {id}&lt;/p&gt;&lt;p&gt;Name: {first_name} {last_name}&lt;/p&gt;&lt;p&gt;Email: {email}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(3, NULL, 0, 1, NULL, 'user_account_created_verify', '', '', '', '', '', 'Account verification is required.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;you have successfully created an account on {site_name}!&lt;/p&gt;&lt;p&gt;Please click the link below to verify your email address and complete your registration.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;{link}&lt;/a&gt;&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(4, NULL, 0, 1, NULL, 'user_account_created', '', '', '', '', '', 'Welcome to {site_name}!', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;Congratulations! You have successfully created an account on {site_name}.&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(5, NULL, 0, 1, NULL, 'admin_account_updated', '', '', '', '', '', 'User account has been updated.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;user account has been updated on {site_name}.&lt;/p&gt;&lt;p&gt;ID: {id}&lt;/p&gt;&lt;p&gt;Name: {first_name} {last_name}&lt;/p&gt;&lt;p&gt;Email: {email}&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;{link}&lt;/a&gt;&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(6, NULL, 0, 1, NULL, 'user_account_approved', '', '', '', '', '', 'Your user account has been approved.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your user account has been approved on {site_name}.&lt;/p&gt;&lt;p&gt;Your can now login into your user control panel and start adding your listings.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;{link}&lt;/a&gt;&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(7, NULL, 0, 1, NULL, 'user_password_reset', '', '', '', '', '', 'Account password reset initiated.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;we have received a password reset request for your user account on {site_name}.&lt;/p&gt;&lt;p&gt;Please, use the link below to proceed:&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;{link}&lt;/a&gt;&lt;/p&gt;&lt;p&gt;This password reset link is valid for 24 hours.&lt;/p&gt;&lt;p&gt;Ignore this email if you do not want to reset your password.&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(8, NULL, 0, 1, NULL, 'user_password_reset_notification', '', '', '', '', '', 'Account password has been updated successfully.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;Congratulations! Your user account password has been reset successfully on {site_name}.&lt;/p&gt;&lt;p&gt;New password: {password}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(9, NULL, 0, 1, NULL, 'admin_listing_created_approve', '', '', '', '', '', 'New {listing_type_singular} is awaiting your approval.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} has been submitted to {site_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;by {first_name} {last_name} ({email})&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(10, NULL, 0, 1, NULL, 'admin_listing_created', '', '', '', '', '', 'New {listing_type_singular} has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} has been submitted to {site_name}.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;by {first_name} {last_name} ({email})&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(11, NULL, 0, 1, NULL, 'admin_listing_updated_approve', '', '', '', '', '', '{listing_type_singular} has been updated.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} has been updated on {site_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;by {first_name} {last_name} ({email})&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(12, NULL, 0, 1, NULL, 'admin_listing_updated', '', '', '', '', '', '{listing_type_singular} has been updated.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} has been updated on {site_name}.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;by {first_name} {last_name} ({email})&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(13, NULL, 0, 1, NULL, 'user_listing_removed', '', '', '', '', '', 'Your {listing_type_singular} has been removed.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} has been removed from {site_name}.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(14, NULL, 0, 1, NULL, 'user_listing_approved', '', '', '', '', '', 'Your {listing_type_singular} has been published.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} has been approved and published.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(15, NULL, 0, 1, NULL, 'user_listing_update_approved', '', '', '', '', '', '{listing_type_singular} update has been published.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} update has been approved and publisheed.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(16, NULL, 0, 1, NULL, 'user_listing_update_rejected', '', '', '', '', '', '{listing_type_singular} update has been rejected.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} update has been rejected.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(17, NULL, 0, 1, NULL, 'admin_listing_claimed', '', '', '', '', '', '{listing_type_singular} has been claimed.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} has been claimed on {site_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;Claimed by {first_name} {last_name} ({email})&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(18, NULL, 0, 1, NULL, 'user_listing_claim_approved', '', '', '', '', '', '{listing_type_singular} claim has been approved.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} claim has been approved.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(19, NULL, 0, 1, NULL, 'user_listing_claim_rejected', '', '', '', '', '', '{listing_type_singular} claim has been rejected.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;{listing_type_singular} claim has been rejected.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(20, NULL, 0, 1, NULL, 'admin_message_created_approve', '', '', '', '', '', 'New {listing_type_singular} message has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} message has been submitted by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;blockquote&gt;Listing ID: {listing_id}&lt;br /&gt;Listing Title: {listing_title}&lt;br /&gt;Message Title: {message_title}&lt;br /&gt;Message Content: {message_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(21, NULL, 0, 1, NULL, 'admin_message_created', '', '', '', '', '', 'New {listing_type_singular} message has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} message has been submitted by {sender_first_name} {sender_last_name}&lt;/p&gt;&lt;blockquote&gt;Listing ID: {listing_id}&lt;br /&gt;Listing Title: {listing_title}&lt;br /&gt;Message Title: {message_title}&lt;br /&gt;Message Content: {message_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(22, NULL, 0, 1, NULL, 'user_message_created', '', '', '', '', '', 'You have new {listing_type_singular} message.', '&lt;p&gt;Hello {recipient_first_name} {recipient_last_name},&lt;/p&gt;&lt;p&gt;you have received new {listing_type_singular} message from {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;You can reply to this message in your &lt;a href=&quot;{link}&quot;&gt;user control panel&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing ID: {listing_id}&lt;br /&gt;Listing Title: {listing_title}&lt;br /&gt;Message Title: {message_title}&lt;br /&gt;Message Content: {message_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(23, NULL, 0, 1, NULL, 'user_message_approved', '', '', '', '', '', 'Your {listing_type_singular} message has been approved.', '&lt;p&gt;Hello {sender_first_name} {sender_last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} message to {recipient_first_name} {recipient_last_name} has been approved and delivered.&lt;/p&gt;&lt;p&gt;You can monitor this message using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing ID: {listing_id}&lt;br /&gt;Listing Title: {listing_title}&lt;br /&gt;Message Title: {message_title}&lt;br /&gt;Message Content: {message_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(24, NULL, 0, 1, NULL, 'admin_reply_created_approve', '', '', '', '', '', 'New {listing_type_singular} message reply has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} message reply has been submitted by {sender_first_name} {sender_last_name}&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;blockquote&gt;Message Title: {message_title}&lt;br /&gt;Reply Content: {reply_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(25, NULL, 0, 1, NULL, 'admin_reply_created', '', '', '', '', '', 'New {listing_type_singular} message reply has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} message reply has been submitted by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;blockquote&gt;Message Title: {message_title}&lt;br /&gt;Reply Content: {reply_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(26, NULL, 0, 1, NULL, 'user_reply_created', '', '', '', '', '', 'You have new {listing_type_singular} message reply.', '&lt;p&gt;Hello {recipient_first_name} {recipient_last_name},&lt;/p&gt;&lt;p&gt;you have received new {listing_type_singular} message reply from {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;You can reply to this message in your &lt;a href=&quot;{link}&quot;&gt;user control panel&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Message Title: {message_title}&lt;br /&gt;Reply Content: {reply_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(27, NULL, 0, 1, NULL, 'user_reply_approved', '', '', '', '', '', 'Your {listing_type_singular} message reply has been approved.', '&lt;p&gt;Hello {sender_first_name} {sender_last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} message reply to {recipient_first_name} {recipient_last_name} has been approved and delivered.&lt;/p&gt;&lt;p&gt;You can monitor this message using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Message Title: {message_title}&lt;br /&gt;Reply Content: {reply_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(28, NULL, 0, 1, NULL, 'admin_review_created_approve', '', '', '', '', '', 'New {listing_type_singular} review has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} review of &quot;{listing_title}&quot; has been submitted by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Review Content: {review_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(29, NULL, 0, 1, NULL, 'admin_review_created', '', '', '', '', '', 'New {listing_type_singular} review has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} review of &quot;{listing_title}&quot; has been submitted by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Review Content: {review_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(30, NULL, 0, 1, NULL, 'user_review_created', '', '', '', '', '', 'You have new {listing_type_singular} review.', '&lt;p&gt;Hello {recipient_first_name} {recipient_last_name},&lt;/p&gt;&lt;p&gt;you have received new {listing_type_singular} review of &quot;{listing_title}&quot; from {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;You can leave a comment for this review in your &lt;a href=&quot;{link}&quot;&gt;user control panel&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Review Content: {review_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(31, NULL, 0, 1, NULL, 'user_review_approved', '', '', '', '', '', 'Your {listing_type_singular} review has been published.', '&lt;p&gt;Hello {sender_first_name} {sender_last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} review of &quot;{listing_title}&quot; has been approved and published.&lt;/p&gt;&lt;p&gt;You can leave extra review comments using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Review Content: {review_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(32, NULL, 0, 1, NULL, 'admin_comment_created_approve', '', '', '', '', '', 'New {listing_type_singular} review comment has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} review comment of &quot;{listing_title}&quot; has been submitted by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;&lt;strong&gt;Administrator &lt;a href=&quot;{link}&quot;&gt;manual approval&lt;/a&gt; is required.&lt;/strong&gt;&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Comment Content: {comment_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(33, NULL, 0, 1, NULL, 'admin_comment_created', '', '', '', '', '', 'New {listing_type_singular} review comment has been submitted.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new {listing_type_singular} review comment of &quot;{listing_title}&quot; has been submitted by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Comment Content: {comment_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(34, NULL, 0, 1, NULL, 'user_comment_created', '', '', '', '', '', 'You have new {listing_type_singular} review comment.', '&lt;p&gt;Hello {recipient_first_name} {recipient_last_name},&lt;/p&gt;&lt;p&gt;you have received new {listing_type_singular} review comment of &quot;{listing_title}&quot; by {sender_first_name} {sender_last_name}.&lt;/p&gt;&lt;p&gt;You can leave extra comments using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Comment Content: {comment_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(35, NULL, 0, 1, NULL, 'user_comment_approved', '', '', '', '', '', 'Your {listing_type_singular} review comment has been approved.', '&lt;p&gt;Hello {sender_first_name} {sender_last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} review comment of &quot;{listing_title}&quot; has been approved and published.&lt;/p&gt;&lt;p&gt;You can leave additional comments using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Review Title: {review_title}&lt;br /&gt;Comment Content: {comment_description}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(36, NULL, 0, 1, NULL, 'user_invoice_created', '', '', '', '', '', 'New {listing_type_singular} invoice has been created.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;a new invoice has been created on {invoice_addeddate}. Please use &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt; link to pay.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;br /&gt;Invoice Due Date: {invoice_duedate}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(37, NULL, 0, 1, NULL, 'user_invoice_paid', '', '', '', '', '', 'Your {listing_type_singular} invoice has been paid.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your payment has been processed successfully on {invoice_paiddate}. Please use &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt; to view the invoice details.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(38, NULL, 0, 1, NULL, 'admin_order_suspended', '', '', '', '', '', '{listing_type_singular} order has been suspended.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} order has been suspended.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(39, NULL, 0, 1, NULL, 'user_order_suspended', '', '', '', '', '', 'Your {listing_type_singular} order has been suspended.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} order has been suspended.&lt;/p&gt;&lt;p&gt;Please, &lt;a href=&quot;{link}&quot;&gt;pay the invoice&lt;/a&gt; to re-activate the listing.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(40, NULL, 0, 1, NULL, 'admin_order_changed', '', '', '', '', '', '{listing_type_singular} product has been changed.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} product has been changed.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;View Listing Summary&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Old Product: {listing_product}&lt;br /&gt;New Product: {listing_new_product}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(41, NULL, 0, 1, NULL, 'user_order_changed', '', '', '', '', '', 'Your {listing_type_singular} product has been changed.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;{listing_type_singular} product has been changed.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;View Listing Summary&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Old Product: {listing_product}&lt;br /&gt;New Product: {listing_new_product}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(42, NULL, 0, 1, NULL, 'user_order_cancelled', '', '', '', '', '', 'Your {listing_type_singular} order has been cancelled.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} order has been cancelled.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;View Listing Summary&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {listing_product}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(43, NULL, 0, 1, NULL, 'user_invoice_reminder_1', '', '', '', '', '', 'Unpaid invoice reminder.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;you have an unpaid invoice which is due on {invoice_duedate}. Please pay using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(44, NULL, 0, 1, NULL, 'user_invoice_reminder_2', '', '', '', '', '', 'Unpaid invoice reminder.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;you have an unpaid invoice which is due on {invoice_duedate}. Please pay using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(45, NULL, 0, 1, NULL, 'user_invoice_reminder_3', '', '', '', '', '', 'Unpaid invoice reminder.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;you have an unpaid invoice which is due on {invoice_duedate}. Please pay using &lt;a href=&quot;{link}&quot;&gt;this link&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {invoice_product}&lt;br /&gt;Invoice Total: {invoice_total}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(46, NULL, 0, 1, NULL, 'admin_contact_form_submitted', '', '', '', '', '{email}', 'New contact form submission has been received.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;new contact form submission has been received.&lt;/p&gt;&lt;blockquote&gt;Name: {name}&lt;br /&gt;Email: {email}&lt;br /&gt;&lt;br /&gt;Inquiry: {comment}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(47, NULL, 0, 1, NULL, 'user_invalid_link', '', '', '', '', '', 'An unreachable {listing_type_singular} website URL has been detected.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} website URL is currently unreachable for our service. Please, ensure that the URL is correct and the server is active. Use the link below to update your record.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(48, NULL, 0, 1, NULL, 'admin_invalid_link', '', '', '', '', '', 'An unreachable {listing_type_singular} website URL has been detected.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;we have detected an invalid or misspelled {listing_type_singular} website URL.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(49, NULL, 0, 1, NULL, 'user_invalid_backlink', '', '', '', '', '', 'An invalid {listing_type_singular} backlink has been detected.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your {listing_type_singular} backlink has been flagged as invalid. For your subscription to remain active, please, ensure the backlink is posted on the listing website and the server is up and running.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(50, NULL, 0, 1, NULL, 'admin_invalid_backlink', '', '', '', '', '', 'An invalid {listing_type_singular} backlink has been detected.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;we have detected an invalid {listing_type_singular} backlink.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(51, NULL, 0, 1, NULL, 'user_order_cancelled_invalid_backlink', '', '', '', '', '', 'Your {listing_type_singular} order has been suspended.', '&lt;p&gt;Hello {first_name} {last_name},&lt;/p&gt;&lt;p&gt;your listing order has been suspended due to an invalid backlink. You can re-activate this listing by choosing a new listing product.&lt;/p&gt;&lt;p&gt;ID: {listing_id}&lt;/p&gt;&lt;p&gt;Title: {listing_title}&lt;/p&gt;&lt;p&gt;{link}&lt;/p&gt;&lt;p&gt;{signature}&lt;/p&gt;'),
(52, NULL, 0, 1, NULL, 'admin_order_cancelled', '', '', '', '', '', '{listing_type_singular} order has been cancelled.', '&lt;p&gt;Hello,&lt;/p&gt;&lt;p&gt;{listing_type_singular} order has been cancelled.&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;{link}&quot;&gt;View Listing Summary&lt;/a&gt;.&lt;/p&gt;&lt;blockquote&gt;Listing Title: {listing_title}&lt;br /&gt;Product: {listing_product}&lt;/blockquote&gt;&lt;p&gt;{signature}&lt;/p&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_event_user`
--

CREATE TABLE `phpls_event_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

--
-- Table structure for table `phpls_fieldconstraints`
--

CREATE TABLE `phpls_fieldconstraints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_fieldconstraints`
--

INSERT INTO `phpls_fieldconstraints` (`id`, `customizable`, `field_id`, `name`, `value`, `weight`) VALUES
(1, NULL, 1, 'required', '', 1),
(2, NULL, 1, 'bannedwords', NULL, 2),
(3, NULL, 2, 'required', '', 3),
(4, NULL, 2, 'bannedwords', NULL, 4),
(5, NULL, 6, 'required', '', 5),
(6, NULL, 6, 'bannedwords', NULL, 6),
(7, NULL, 8, 'required', '', 7),
(8, NULL, 8, 'number', '', 8),
(9, NULL, 8, 'isleaf', 'locations', 9),
(10, NULL, 9, 'required', '', 10),
(11, NULL, 10, 'required', '', 11),
(12, NULL, 11, 'required', '', 12),
(13, NULL, 11, 'number', '', 13),
(14, NULL, 11, 'min', '0', 14),
(15, NULL, 11, 'max', '20', 15),
(16, NULL, 3, 'required', '', 16),
(17, NULL, 3, 'unique:users,email,{id}', '', 17),
(18, NULL, 4, 'required', '', 18),
(19, NULL, 5, 'required', '', 19),
(20, NULL, 5, 'timezone', '', 20);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_fieldgroups`
--

CREATE TABLE `phpls_fieldgroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_fieldgroups`
--

INSERT INTO `phpls_fieldgroups` (`id`, `name`, `slug`) VALUES
(1, 'Users', 'users');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_fieldoptions`
--

CREATE TABLE `phpls_fieldoptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `value` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_fields`
--

CREATE TABLE `phpls_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fieldgroup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `submittable` tinyint(1) UNSIGNED DEFAULT NULL,
  `updatable` tinyint(1) UNSIGNED DEFAULT NULL,
  `queryable` tinyint(1) UNSIGNED DEFAULT NULL,
  `sortable` tinyint(1) UNSIGNED DEFAULT NULL,
  `outputable` tinyint(1) UNSIGNED DEFAULT NULL,
  `sluggable` varchar(255) DEFAULT NULL,
  `upload_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `label` text DEFAULT NULL,
  `tooltip` varchar(255) DEFAULT NULL,
  `placeholder` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_fields`
--

INSERT INTO `phpls_fields` (`id`, `fieldgroup_id`, `customizable`, `submittable`, `updatable`, `queryable`, `sortable`, `outputable`, `sluggable`, `upload_id`, `type`, `name`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(1, 1, NULL, 1, 1, NULL, NULL, NULL, NULL, 0, 'text', 'first_name', '', '{\"en\":\"First Name\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 1),
(2, 1, NULL, 1, 1, NULL, NULL, NULL, NULL, 0, 'text', 'last_name', '', '{\"en\":\"Last Name\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 2),
(3, 1, NULL, 1, 1, NULL, NULL, NULL, NULL, 0, 'email', 'email', '', '{\"en\":\"Email\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 9),
(4, 1, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, 'password', 'password', NULL, '{\"en\":\"Password\"}', NULL, NULL, NULL, 10),
(5, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 'timezone', 'timezone', '', '{\"en\":\"Timezone\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 11),
(6, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 'text', 'address', '', '{\"en\":\"Address\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 3),
(7, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 'text', 'zip', '', '{\"en\":\"Zip \\/ Postal Code\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 4),
(8, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 'locationmappicker', 'location_id', '', '{\"en\":\"Location\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 5),
(9, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 'number', 'latitude', '', '{\"en\":\"Latitude\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 6),
(10, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 'number', 'longitude', '', '{\"en\":\"Longitude\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 7),
(11, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 'hidden', 'zoom', '15', '{\"en\":\"Zoom\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 8);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_files`
--

CREATE TABLE `phpls_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uploadtype_id` bigint(20) UNSIGNED NOT NULL,
  `document_id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `version` mediumint(8) UNSIGNED NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `mime` varchar(255) NOT NULL,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `extension` text DEFAULT NULL,
  `crop_data` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `image_height` smallint(5) UNSIGNED DEFAULT NULL,
  `small_image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `small_image_height` smallint(5) UNSIGNED DEFAULT NULL,
  `medium_image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `medium_image_height` smallint(5) UNSIGNED DEFAULT NULL,
  `large_image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `large_image_height` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_filetypes`
--

CREATE TABLE `phpls_filetypes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `mime` text NOT NULL,
  `extension` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_filetypes`
--

INSERT INTO `phpls_filetypes` (`id`, `name`, `mime`, `extension`) VALUES
(1, '{\"en\":\"JPEG Image\"}', 'image/jpeg', 'jpeg,jpg,jpe'),
(2, '{\"en\":\"PNG Image\"}', 'image/png', 'png'),
(3, '{\"en\":\"GIF Image\"}', 'image/gif', 'gif'),
(4, '{\"en\":\"PDF File\"}', 'application/pdf', 'pdf'),
(5, '{\"en\":\"WebP Image\"}', 'image/webp', 'webp');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_filetype_uploadtype`
--

CREATE TABLE `phpls_filetype_uploadtype` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `filetype_id` bigint(20) UNSIGNED NOT NULL,
  `uploadtype_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_filetype_uploadtype`
--

INSERT INTO `phpls_filetype_uploadtype` (`id`, `filetype_id`, `uploadtype_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 1, 2),
(5, 2, 2),
(6, 3, 2),
(7, 1, 3),
(8, 2, 3),
(9, 3, 3),
(10, 1, 4),
(11, 2, 4),
(12, 3, 4),
(13, 1, 5),
(14, 2, 5),
(15, 3, 5),
(16, 1, 6),
(17, 2, 6),
(18, 3, 6),
(19, 1, 7),
(20, 2, 7),
(21, 3, 7),
(22, 1, 20),
(23, 2, 20),
(24, 3, 20),
(25, 1, 30),
(26, 2, 30),
(27, 3, 30),
(28, 1, 40),
(29, 2, 40),
(30, 3, 40),
(31, 5, 1),
(32, 5, 2),
(33, 5, 3),
(34, 5, 4),
(35, 5, 5),
(36, 5, 6),
(37, 5, 7),
(38, 5, 20),
(39, 5, 30),
(40, 5, 40);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_gateways`
--

CREATE TABLE `phpls_gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `offsite` tinyint(1) UNSIGNED DEFAULT NULL,
  `subscription` tinyint(1) UNSIGNED DEFAULT NULL,
  `offline` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `settings` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_gateways`
--

INSERT INTO `phpls_gateways` (`id`, `active`, `offsite`, `subscription`, `offline`, `name`, `description`, `slug`, `settings`, `weight`) VALUES
(1, 1, NULL, NULL, 1, '{\"en\":\"Offline \\/ Wire Transfer\"}', '{\"en\":\"\"}', 'offline', '{\"message\":\"&lt;p&gt;&lt;strong&gt;Offline \\/ Wire Payment Instructions. &lt;\\/strong&gt;&lt;\\/p&gt;\"}', 1),
(2, 1, 1, NULL, NULL, '{\"en\":\"PayPal Checkout API\"}', '{\"en\":\"\"}', 'paypal', '{\"currency\":\"USD\",\"clientId\":\"\",\"secret\":\"\",\"testMode\":\"1\"}', 2),
(3, 1, 1, NULL, NULL, '{\"en\":\"Authorize.net API\"}', '{\"en\":\"\"}', 'authorizenet', '{\"currency\":\"USD\",\"authName\":\"test\",\"transactionKey\":\"test\",\"signatureKey\":\"test\",\"testMode\":\"1\"}', 3),
(4, 1, 1, NULL, NULL, '{\"en\":\"Mollie API\"}', '{\"en\":\"\"}', 'mollie', '{\"currency\":\"USD\",\"apiKey\":\"test\"}', 4),
(5, 1, 1, 1, NULL, '{\"en\":\"Stripe Subscription\"}', '{\"en\":\"\"}', 'stripe', '{\"currency\":\"USD\",\"apiKey\":\"test\",\"publicKey\":\"test\",\"webhookSecret\":\"test\"}', 5);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_gateway_pricing`
--

CREATE TABLE `phpls_gateway_pricing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gateway_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_hours`
--

CREATE TABLE `phpls_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `dow` tinyint(1) UNSIGNED DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Table structure for table `phpls_invoices`
--

CREATE TABLE `phpls_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gateway_id` bigint(20) UNSIGNED DEFAULT NULL,
  `period` varchar(10) DEFAULT NULL,
  `period_count` mediumint(8) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `subtotal` decimal(15,2) UNSIGNED NOT NULL,
  `total` decimal(15,2) UNSIGNED NOT NULL,
  `balance` decimal(15,2) UNSIGNED NOT NULL,
  `tax` decimal(15,2) UNSIGNED DEFAULT NULL,
  `discount` decimal(15,2) UNSIGNED NOT NULL,
  `refund` decimal(15,2) UNSIGNED DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `due_datetime` datetime DEFAULT NULL,
  `paid_datetime` datetime DEFAULT NULL,
  `cancelled_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_languages`
--

CREATE TABLE `phpls_languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `locale` varchar(60) NOT NULL,
  `name` varchar(255) NOT NULL,
  `native` varchar(255) DEFAULT NULL,
  `direction` varchar(255) NOT NULL,
  `thousands_separator` varchar(10) DEFAULT NULL,
  `decimal_separator` varchar(10) NOT NULL,
  `date_format` varchar(200) NOT NULL,
  `time_format` varchar(200) NOT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_languages`
--

INSERT INTO `phpls_languages` (`id`, `active`, `customizable`, `locale`, `name`, `native`, `direction`, `thousands_separator`, `decimal_separator`, `date_format`, `time_format`, `weight`) VALUES
(1, 1, NULL, 'en', 'English', 'English', 'ltr', ',', '.', 'm/d/Y', 'h:i A', 1);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_linked_update`
--

CREATE TABLE `phpls_linked_update` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listingfieldconstraints`
--

CREATE TABLE `phpls_listingfieldconstraints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `listingfield_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listingfielddata`
--

CREATE TABLE `phpls_listingfielddata` (
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listingfieldgroups`
--

CREATE TABLE `phpls_listingfieldgroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_listingfieldgroups`
--

INSERT INTO `phpls_listingfieldgroups` (`id`, `name`, `slug`) VALUES
(1, '{\"en\":\"Listings\"}', 'listings'),
(2, '{\"en\":\"Reviews\"}', 'reviews'),
(3, '{\"en\":\"Messages\"}', 'messages');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listingfieldoptions`
--

CREATE TABLE `phpls_listingfieldoptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `listingfield_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `value` text DEFAULT NULL,
  `schema_itemprop` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listingfields`
--

CREATE TABLE `phpls_listingfields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `removable` tinyint(1) UNSIGNED DEFAULT NULL,
  `submittable` tinyint(1) UNSIGNED DEFAULT NULL,
  `updatable` tinyint(1) UNSIGNED DEFAULT NULL,
  `queryable` tinyint(1) UNSIGNED DEFAULT NULL,
  `sortable` tinyint(1) UNSIGNED DEFAULT NULL,
  `outputable` tinyint(1) UNSIGNED DEFAULT NULL,
  `outputable_search` tinyint(1) UNSIGNED DEFAULT NULL,
  `sluggable` varchar(255) DEFAULT NULL,
  `listingfieldgroup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) NOT NULL,
  `upload_id` bigint(20) UNSIGNED DEFAULT NULL,
  `socialprofiletype_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `label` text DEFAULT NULL,
  `search_type` varchar(255) DEFAULT NULL,
  `range_min` varchar(255) DEFAULT NULL,
  `range_max` varchar(255) DEFAULT NULL,
  `range_step` varchar(255) DEFAULT NULL,
  `tooltip` varchar(255) DEFAULT NULL,
  `placeholder` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `schema_itemprop` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listingfield_product`
--

CREATE TABLE `phpls_listingfield_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `listingfield_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listings`
--

CREATE TABLE `phpls_listings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `import_id` bigint(20) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `claimed` tinyint(1) UNSIGNED DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `rating` float(2,1) UNSIGNED DEFAULT NULL,
  `review_count` mediumint(8) UNSIGNED DEFAULT NULL,
  `impressions` bigint(20) UNSIGNED DEFAULT NULL,
  `search_impressions` bigint(20) UNSIGNED DEFAULT NULL,
  `phone_views` bigint(20) UNSIGNED DEFAULT NULL,
  `website_clicks` bigint(20) UNSIGNED DEFAULT NULL,
  `_featured` tinyint(1) UNSIGNED DEFAULT NULL,
  `_page` tinyint(1) UNSIGNED DEFAULT NULL,
  `_position` mediumint(8) UNSIGNED DEFAULT NULL,
  `_extra_categories` mediumint(8) UNSIGNED DEFAULT NULL,
  `_title_size` mediumint(8) UNSIGNED DEFAULT NULL,
  `_short_description_size` mediumint(8) UNSIGNED DEFAULT NULL,
  `_description_size` mediumint(8) UNSIGNED DEFAULT NULL,
  `_description_links_limit` mediumint(8) UNSIGNED DEFAULT NULL,
  `_gallery_size` mediumint(8) UNSIGNED DEFAULT NULL,
  `_address` tinyint(1) UNSIGNED DEFAULT NULL,
  `_map` tinyint(1) UNSIGNED DEFAULT NULL,
  `_event_dates` mediumint(8) UNSIGNED DEFAULT NULL,
  `_send_message` tinyint(1) UNSIGNED DEFAULT NULL,
  `_reviews` tinyint(1) UNSIGNED DEFAULT NULL,
  `_seo` tinyint(1) UNSIGNED DEFAULT NULL,
  `_backlink` tinyint(1) UNSIGNED DEFAULT NULL,
  `_dofollow` tinyint(1) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `event_start_datetime` datetime DEFAULT NULL,
  `event_frequency` text DEFAULT NULL,
  `event_interval` tinyint(2) UNSIGNED DEFAULT NULL,
  `event_weekdays` text DEFAULT NULL,
  `event_weeks` text DEFAULT NULL,
  `event_dates` text DEFAULT NULL,
  `event_end_datetime` datetime DEFAULT NULL,
  `event_rsvp` tinyint(1) UNSIGNED DEFAULT NULL,
  `offer_start_datetime` datetime DEFAULT NULL,
  `offer_end_datetime` datetime DEFAULT NULL,
  `offer_price` text DEFAULT NULL,
  `offer_discount_type` text DEFAULT NULL,
  `offer_discount` text DEFAULT NULL,
  `offer_count` text DEFAULT NULL,
  `offer_terms` text DEFAULT NULL,
  `offer_redeem` tinyint(1) UNSIGNED DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `zoom` tinyint(2) UNSIGNED DEFAULT NULL,
  `timezone` char(6) DEFAULT NULL,
  `sync_product` tinyint(1) UNSIGNED DEFAULT NULL,
  `deadlinkchecker_datetime` datetime DEFAULT NULL,
  `deadlinkchecker_retry` tinyint(2) UNSIGNED DEFAULT NULL,
  `deadlinkchecker_code` varchar(255) DEFAULT NULL,
  `backlinkchecker_datetime` datetime DEFAULT NULL,
  `backlinkchecker_retry` tinyint(2) UNSIGNED DEFAULT NULL,
  `backlinkchecker_code` varchar(255) DEFAULT NULL,
  `backlinkchecker_linkrelation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_listing_linked`
--

CREATE TABLE `phpls_listing_linked` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_locations`
--

CREATE TABLE `phpls_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `impressions` bigint(20) UNSIGNED DEFAULT NULL,
  `featured` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `zoom` tinyint(2) UNSIGNED DEFAULT NULL,
  `logo_id` varchar(255) DEFAULT NULL,
  `header_id` varchar(255) DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `_left` bigint(20) UNSIGNED NOT NULL,
  `_right` bigint(20) UNSIGNED NOT NULL,
  `_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `_level` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_locations`
--

INSERT INTO `phpls_locations` (`id`, `active`, `impressions`, `featured`, `name`, `slug`, `short_description`, `description`, `latitude`, `longitude`, `zoom`, `logo_id`, `header_id`, `meta_title`, `meta_keywords`, `meta_description`, `_left`, `_right`, `_parent_id`, `_level`) VALUES
(1, 1, NULL, NULL, '{\"en\":\"ROOT\"}', 'root', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 4, NULL, NULL),
(2, 1, NULL, NULL, '{\"en\":\"United States\"}', 'united-states', '{\"en\":\"\"}', '', 39.828300, -98.579500, 4, '3fc1e58b64e790a6e977ecd72dad326d', '5c11c2d6b1e43504d9a1ad8f93aa8370', '{\"en\":\"\"}', '{\"en\":\"\"}', '{\"en\":\"\"}', 2, 3, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_logs`
--

CREATE TABLE `phpls_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL,
  `added_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_messagefielddata`
--

CREATE TABLE `phpls_messagefielddata` (
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_messages`
--

CREATE TABLE `phpls_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `listing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `read_datetime` datetime DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `lastreply_datetime` datetime DEFAULT NULL,
  `title` text NOT NULL,
  `description` text DEFAULT NULL,
  `attachments_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_offer_user`
--

CREATE TABLE `phpls_offer_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_orders`
--

CREATE TABLE `phpls_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `pricing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subscription_id` varchar(255) DEFAULT NULL,
  `cancellable` tinyint(1) UNSIGNED DEFAULT NULL,
  `period` varchar(10) DEFAULT NULL,
  `period_count` mediumint(8) UNSIGNED DEFAULT NULL,
  `price` decimal(15,2) UNSIGNED DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `sync_pricing` tinyint(1) UNSIGNED DEFAULT NULL,
  `notification_1_sent` tinyint(1) UNSIGNED DEFAULT NULL,
  `notification_2_sent` tinyint(1) UNSIGNED DEFAULT NULL,
  `notification_3_sent` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_pages`
--

CREATE TABLE `phpls_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `title` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_pages`
--

INSERT INTO `phpls_pages` (`id`, `active`, `type_id`, `customizable`, `title`, `slug`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(1, 1, NULL, NULL, '{\"en\":\"404 Not Found\"}', 'error/404', NULL, NULL, NULL),
(2, 1, NULL, NULL, '{\"en\":\"405 Method Not Allowed\"}', 'error/405', NULL, NULL, NULL),
(3, 1, NULL, NULL, '{\"en\":\"Contact Us\"}', 'contact', NULL, NULL, NULL),
(4, 1, NULL, NULL, '{\"en\":\"Account Dashboard\"}', 'account', NULL, NULL, NULL),
(5, 1, NULL, NULL, '{\"en\":\"Sign In\"}', 'account/login', NULL, NULL, NULL),
(6, 1, NULL, NULL, '{\"en\":\"Sign Up\"}', 'account/create', NULL, NULL, NULL),
(7, 1, NULL, NULL, '{\"en\":\"Password Reminder\"}', 'account/password-reminder', NULL, NULL, NULL),
(8, 1, NULL, NULL, '{\"en\":\"Password Reset\"}', 'account/password-reset', NULL, NULL, NULL),
(9, 1, NULL, NULL, '{\"en\":\"Account Verification\"}', 'account/verification', NULL, NULL, NULL),
(10, 1, NULL, NULL, '{\"en\":\"Edit Profile\"}', 'account/profile', NULL, NULL, NULL),
(11, 1, NULL, NULL, '{\"en\":\"Bookmarks\"}', 'account/bookmarks', NULL, NULL, NULL),
(12, 1, NULL, NULL, '{\"en\":\"Messages\"}', 'account/messages', NULL, NULL, NULL),
(13, 1, NULL, NULL, '{\"en\":\"Message\"}', 'account/messages/view', NULL, NULL, NULL),
(14, 1, NULL, NULL, '{\"en\":\"Reviews\"}', 'account/reviews', NULL, NULL, NULL),
(15, 1, NULL, NULL, '{\"en\":\"Review\"}', 'account/reviews/view', NULL, NULL, NULL),
(16, 1, NULL, NULL, '{\"en\":\"Claims\"}', 'account/claims', NULL, NULL, NULL),
(17, 1, NULL, NULL, '{\"en\":\"Invoices\"}', 'account/invoices', NULL, NULL, NULL),
(18, 1, NULL, NULL, '{\"en\":\"Invoice\"}', 'account/invoices/view', NULL, NULL, NULL),
(19, 1, NULL, NULL, '{\"en\":\"Manage Listings\"}', 'account/manage/type', NULL, NULL, NULL),
(20, 1, NULL, NULL, '{\"en\":\"Add Listing\"}', 'account/manage/type/create', NULL, NULL, NULL),
(21, 1, NULL, NULL, '{\"en\":\"Update Listing\"}', 'account/manage/type/update', NULL, NULL, NULL),
(22, 1, NULL, NULL, '{\"en\":\"Listing Summary\"}', 'account/manage/type/summary', NULL, NULL, NULL),
(23, 1, NULL, NULL, '{\"en\":\"Listing Reviews\"}', 'account/manage/type/reviews', NULL, NULL, NULL),
(24, 1, NULL, NULL, '{\"en\":\"Checkout\"}', 'account/checkout', NULL, NULL, NULL),
(25, 1, NULL, NULL, '{\"en\":\"Checkout\"}', 'account/checkout/gateway', NULL, NULL, NULL),
(26, 1, NULL, NULL, '{\"en\":\"Maintenance Mode\"}', 'maintenance', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_page_widget`
--

CREATE TABLE `phpls_page_widget` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `widget_id` bigint(20) UNSIGNED NOT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL,
  `settings` mediumtext DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `access_level` tinyint(1) UNSIGNED DEFAULT NULL,
  `access_level_pricing_ids` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_page_widget`
--

INSERT INTO `phpls_page_widget` (`id`, `active`, `page_id`, `widget_id`, `weight`, `settings`, `access_level`) VALUES
(1, 1, 1, 1, 1, '[]', 1),
(2, 1, 1, 28, 2, '[]', 1),
(3, 1, 1, 2, 3, '[]', 1),
(4, 1, 2, 1, 4, '[]', 1),
(5, 1, 2, 29, 5, '[]', 1),
(6, 1, 2, 2, 6, '[]', 1),
(7, 1, 3, 1, 7, '[]', 1),
(8, 1, 3, 14, 9, '[]', 1),
(9, 1, 3, 2, 10, '[]', 1),
(10, 1, 4, 1, 11, '[]', 1),
(11, 1, 4, 15, 12, '[]', 1),
(12, 1, 4, 30, 13, '[]', 1),
(13, 1, 4, 2, 14, '[]', 1),
(14, 1, 5, 1, 15, '[]', 1),
(15, 1, 5, 30, 16, '[]', 1),
(16, 1, 5, 2, 17, '[]', 1),
(17, 1, 6, 1, 18, '[]', 1),
(18, 1, 6, 30, 19, '[]', 1),
(19, 1, 6, 2, 20, '[]', 1),
(20, 1, 7, 1, 21, '[]', 1),
(21, 1, 7, 30, 22, '[]', 1),
(22, 1, 7, 2, 23, '[]', 1),
(23, 1, 8, 1, 24, '[]', 1),
(24, 1, 8, 30, 25, '[]', 1),
(25, 1, 8, 2, 26, '[]', 1),
(26, 1, 9, 1, 27, '[]', 1),
(27, 1, 9, 30, 28, '[]', 1),
(28, 1, 9, 2, 29, '[]', 1),
(29, 1, 10, 1, 30, '[]', 1),
(30, 1, 10, 15, 31, '[]', 1),
(31, 1, 10, 30, 32, '[]', 1),
(32, 1, 10, 2, 33, '[]', 1),
(33, 1, 11, 1, 34, '[]', 1),
(34, 1, 11, 15, 35, '[]', 1),
(35, 1, 11, 30, 36, '[]', 1),
(36, 1, 11, 2, 37, '[]', 1),
(37, 1, 12, 1, 38, '[]', 1),
(38, 1, 12, 15, 39, '[]', 1),
(39, 1, 12, 30, 40, '[]', 1),
(40, 1, 12, 2, 41, '[]', 1),
(41, 1, 13, 1, 42, '[]', 1),
(42, 1, 13, 15, 43, '[]', 1),
(43, 1, 13, 30, 44, '[]', 1),
(44, 1, 13, 2, 45, '[]', 1),
(45, 1, 14, 1, 46, '[]', 1),
(46, 1, 14, 15, 47, '[]', 1),
(47, 1, 14, 30, 48, '[]', 1),
(48, 1, 14, 2, 49, '[]', 1),
(49, 1, 15, 1, 50, '[]', 1),
(50, 1, 15, 15, 51, '[]', 1),
(51, 1, 15, 30, 52, '[]', 1),
(52, 1, 15, 2, 53, '[]', 1),
(53, 1, 16, 1, 54, '[]', 1),
(54, 1, 16, 15, 55, '[]', 1),
(55, 1, 16, 30, 56, '[]', 1),
(56, 1, 16, 2, 57, '[]', 1),
(57, 1, 17, 1, 58, '[]', 1),
(58, 1, 17, 15, 59, '[]', 1),
(59, 1, 17, 30, 60, '[]', 1),
(60, 1, 17, 2, 61, '[]', 1),
(61, 1, 18, 1, 62, '[]', 1),
(62, 1, 18, 15, 63, '[]', 1),
(63, 1, 18, 30, 64, '[]', 1),
(64, 1, 18, 2, 65, '[]', 1),
(65, 1, 19, 1, 66, '[]', 1),
(66, 1, 19, 15, 67, '[]', 1),
(67, 1, 19, 30, 68, '[]', 1),
(68, 1, 19, 2, 69, '[]', 1),
(69, 1, 20, 1, 70, '[]', 1),
(70, 1, 20, 15, 71, '[]', 1),
(71, 1, 20, 30, 72, '[]', 1),
(72, 1, 20, 2, 73, '[]', 1),
(73, 1, 21, 1, 74, '[]', 1),
(74, 1, 21, 15, 75, '[]', 1),
(75, 1, 21, 30, 76, '[]', 1),
(76, 1, 21, 2, 77, '[]', 1),
(77, 1, 22, 1, 78, '[]', 1),
(78, 1, 22, 15, 79, '[]', 1),
(79, 1, 22, 30, 80, '[]', 1),
(80, 1, 22, 2, 81, '[]', 1),
(81, 1, 23, 1, 82, '[]', 1),
(82, 1, 23, 15, 83, '[]', 1),
(83, 1, 23, 30, 84, '[]', 1),
(84, 1, 23, 2, 85, '[]', 1),
(85, 1, 24, 1, 86, '[]', 1),
(86, 1, 24, 15, 87, '[]', 1),
(87, 1, 24, 30, 88, '[]', 1),
(88, 1, 24, 2, 89, '[]', 1),
(89, 1, 25, 1, 90, '[]', 1),
(90, 1, 25, 15, 91, '[]', 1),
(91, 1, 25, 30, 92, '[]', 1),
(92, 1, 25, 2, 93, '[]', 1),
(93, 1, 26, 1, 94, '[]', 1),
(94, 1, 26, 33, 95, '[]', 1),
(95, 1, 26, 2, 96, '[]', 1);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_pricings`
--

CREATE TABLE `phpls_pricings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hidden` tinyint(1) UNSIGNED DEFAULT NULL,
  `autoapprovable` tinyint(1) UNSIGNED DEFAULT NULL,
  `claimable` tinyint(1) UNSIGNED DEFAULT NULL,
  `cancellable` tinyint(1) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `period` varchar(10) NOT NULL,
  `period_count` mediumint(8) UNSIGNED NOT NULL,
  `price` decimal(15,2) UNSIGNED NOT NULL,
  `user_limit` mediumint(8) UNSIGNED NOT NULL,
  `peruser_limit` mediumint(8) UNSIGNED NOT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_pricing_required`
--

CREATE TABLE `phpls_pricing_required` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED NOT NULL,
  `required_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_pricing_upgrade`
--

CREATE TABLE `phpls_pricing_upgrade` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pricing_id` bigint(20) UNSIGNED NOT NULL,
  `upgrade_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_products`
--

CREATE TABLE `phpls_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hidden` tinyint(1) UNSIGNED DEFAULT NULL,
  `featured` tinyint(1) UNSIGNED DEFAULT NULL,
  `type_id` mediumint(8) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `_featured` tinyint(1) UNSIGNED DEFAULT NULL,
  `_page` tinyint(1) UNSIGNED DEFAULT NULL,
  `_position` mediumint(8) UNSIGNED NOT NULL,
  `_extra_categories` mediumint(8) UNSIGNED NOT NULL,
  `_title_size` mediumint(8) UNSIGNED DEFAULT NULL,
  `_short_description_size` mediumint(8) UNSIGNED NOT NULL,
  `_description_size` mediumint(8) UNSIGNED NOT NULL,
  `_description_links_limit` mediumint(8) UNSIGNED DEFAULT NULL,
  `_gallery_size` mediumint(8) UNSIGNED DEFAULT NULL,
  `_address` tinyint(1) UNSIGNED DEFAULT NULL,
  `_map` tinyint(1) UNSIGNED DEFAULT NULL,
  `_event_dates` mediumint(8) UNSIGNED DEFAULT NULL,
  `_send_message` tinyint(1) UNSIGNED DEFAULT NULL,
  `_reviews` tinyint(1) UNSIGNED DEFAULT NULL,
  `_seo` tinyint(1) UNSIGNED DEFAULT NULL,
  `_backlink` tinyint(1) UNSIGNED DEFAULT NULL,
  `_dofollow` tinyint(1) UNSIGNED DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_ratings`
--

CREATE TABLE `phpls_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_ratings`
--

INSERT INTO `phpls_ratings` (`id`, `name`, `weight`) VALUES
(1, '{\"en\":\"Customer Services\"}', 1),
(2, '{\"en\":\"Professionalism\"}', 2),
(3, '{\"en\":\"Products Quality\"}', 3);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_rating_type`
--

CREATE TABLE `phpls_rating_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rating_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_rawstats`
--

CREATE TABLE `phpls_rawstats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL,
  `added_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_reminders`
--

CREATE TABLE `phpls_reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_replies`
--

CREATE TABLE `phpls_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `read_datetime` datetime DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachments_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_reviewfielddata`
--

CREATE TABLE `phpls_reviewfielddata` (
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_reviews`
--

CREATE TABLE `phpls_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `rating` float(2,1) UNSIGNED DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `title` text NOT NULL,
  `description` text DEFAULT NULL,
  `attachments_id` varchar(255) DEFAULT NULL,
  `rating_1` float(3,1) UNSIGNED DEFAULT NULL,
  `rating_2` float(3,1) UNSIGNED DEFAULT NULL,
  `rating_3` float(3,1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_sessions`
--

CREATE TABLE `phpls_sessions` (
  `sid` varchar(128) NOT NULL,
  `sdata` blob DEFAULT NULL,
  `slifetime` mediumint(9) NOT NULL,
  `stimestamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_settingfields`
--

CREATE TABLE `phpls_settingfields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `settinggroup_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `upload_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `options_type` varchar(255) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `constraints` text DEFAULT NULL,
  `value` text DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `tooltip` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_settingfields`
--

INSERT INTO `phpls_settingfields` (`id`, `settinggroup_id`, `type`, `upload_id`, `name`, `options_type`, `options`, `constraints`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(1, 1, 'separator', NULL, 'separator_general', NULL, NULL, NULL, NULL, 'general_general', NULL, NULL, NULL, 1),
(2, 1, 'text', NULL, 'site_name', NULL, NULL, NULL, NULL, 'site_name', NULL, NULL, NULL, 2),
(3, 1, 'timezone', NULL, 'timezone', NULL, NULL, 'required', NULL, 'timezone', NULL, NULL, NULL, 3),
(4, 1, 'textarea', NULL, 'address_format', NULL, NULL, 'required|string', NULL, 'address_format', NULL, NULL, NULL, 4),
(5, 1, 'separator', NULL, 'separator_custom_js', NULL, NULL, NULL, NULL, 'general_custom_js', NULL, NULL, NULL, 5),
(6, 1, 'textarea', NULL, 'custom_js', NULL, NULL, NULL, NULL, 'custom_js', NULL, NULL, NULL, 6),
(7, 1, 'separator', NULL, 'separator_captcha', NULL, NULL, NULL, NULL, 'general_captcha', NULL, NULL, NULL, 7),
(8, 1, 'text', NULL, 'captcha_site_key', NULL, NULL, NULL, NULL, 'captcha_site_key', NULL, NULL, NULL, 8),
(9, 1, 'text', NULL, 'captcha_secret_key', NULL, NULL, NULL, NULL, 'captcha_secret_key', NULL, NULL, NULL, 9),
(10, 2, 'separator', NULL, 'separator_general', NULL, NULL, NULL, NULL, 'account_general', NULL, NULL, NULL, 10),
(11, 2, 'toggle', NULL, 'approval', NULL, NULL, NULL, NULL, 'account_approval', NULL, NULL, NULL, 11),
(12, 2, 'toggle', NULL, 'verification', NULL, NULL, NULL, NULL, 'account_verification', NULL, NULL, NULL, 12),
(13, 2, 'select', NULL, 'default_group', 'eval', 'return (new \\App\\Models\\UserGroup)->orderBy(\'id\')->get([\'id\', \'name\'])->pluck(\'name\', \'id\')->all();', NULL, NULL, 'account_group', NULL, NULL, NULL, 13),
(14, 2, 'select', NULL, 'default_provider', NULL, 'Native|native', NULL, NULL, 'default_provider', NULL, NULL, NULL, 14),
(15, 2, 'separator', NULL, 'separator_facebook', NULL, NULL, NULL, NULL, 'account_facebook', NULL, NULL, NULL, 15),
(16, 2, 'text', NULL, 'facebook_app_id', NULL, NULL, NULL, NULL, 'facebook_app_id', NULL, NULL, NULL, 16),
(17, 2, 'text', NULL, 'facebook_secret', NULL, NULL, NULL, NULL, 'facebook_secret', NULL, NULL, NULL, 17),
(18, 3, 'separator', NULL, 'separator_general', NULL, NULL, NULL, NULL, 'billing_general', NULL, NULL, NULL, 18),
(19, 3, 'text', NULL, 'currency_code', NULL, NULL, 'required', NULL, 'currency_code', NULL, NULL, NULL, 19),
(20, 3, 'text', NULL, 'currency_sign', NULL, NULL, 'required', NULL, 'currency_sign', NULL, NULL, NULL, 20),
(21, 3, 'select', NULL, 'currency_sign_position', NULL, 'append|append\r\nprepend|prepend', NULL, NULL, 'currency_sign_position', NULL, NULL, NULL, 21),
(22, 3, 'select', NULL, 'tax', NULL, 'inclusive|inclusive\r\nexclusive|exclusive', NULL, NULL, 'tax', NULL, NULL, NULL, 22),
(23, 3, 'separator', NULL, 'separator_invoice', NULL, NULL, NULL, NULL, 'billing_invoice', NULL, NULL, NULL, 23),
(24, 3, 'textarea', NULL, 'invoice_company_details', NULL, NULL, '', NULL, 'invoice_company_details', NULL, NULL, NULL, 24),
(25, 3, 'text', NULL, 'invoice_product_name', NULL, NULL, NULL, NULL, 'invoice_product_name', NULL, NULL, NULL, 25),
(26, 3, 'separator', NULL, 'separator_advanced', NULL, NULL, NULL, NULL, 'billing_advanced', NULL, NULL, NULL, 26),
(27, 3, 'number', NULL, 'invoice_creation_days', NULL, NULL, 'required|min:1', NULL, 'invoice_creation_days', NULL, NULL, NULL, 27),
(28, 3, 'number', NULL, 'overdue_suspend_days', NULL, NULL, 'required|min:1', NULL, 'overdue_suspend_days', NULL, NULL, NULL, 28),
(29, 3, 'number', NULL, 'invoice_reminder_1_days', NULL, NULL, 'required|min:1', NULL, 'invoice_reminder_1_days', NULL, NULL, NULL, 29),
(30, 3, 'number', NULL, 'invoice_reminder_2_days', NULL, NULL, 'required|min:1', NULL, 'invoice_reminder_2_days', NULL, NULL, NULL, 30),
(31, 3, 'number', NULL, 'invoice_reminder_3_days', NULL, NULL, 'required|min:1', NULL, 'invoice_reminder_3_days', NULL, NULL, NULL, 31),
(32, 4, 'separator', NULL, 'separator_general', NULL, NULL, NULL, NULL, 'email_general', NULL, NULL, NULL, 32),
(33, 4, 'email', NULL, 'from_email', NULL, '', 'required', NULL, 'from_email', NULL, NULL, NULL, 33),
(34, 4, 'text', NULL, 'from_name', NULL, '', '', NULL, 'from_name', NULL, NULL, NULL, 34),
(35, 4, 'textarea', NULL, 'signature', NULL, NULL, NULL, NULL, 'signature', NULL, NULL, NULL, 35),
(36, 4, 'separator', NULL, 'separator_transport', NULL, NULL, NULL, NULL, 'email_transport', NULL, NULL, NULL, 36),
(37, 4, 'select', NULL, 'transport', NULL, 'smtp|smtp\r\nsendmail|sendmail', '', NULL, 'email_transport', NULL, NULL, NULL, 37),
(38, 4, 'text', NULL, 'smtp_host', NULL, '', '', NULL, 'smtp_host', NULL, NULL, NULL, 38),
(39, 4, 'text', NULL, 'smtp_port', NULL, '', '', NULL, 'smtp_port', NULL, NULL, NULL, 39),
(40, 4, 'select', NULL, 'smtp_encryption', NULL, '|none\r\nssl|ssl\r\ntls|tls', '', NULL, 'smtp_encryption', NULL, NULL, NULL, 40),
(41, 4, 'text', NULL, 'smtp_user', NULL, '', '', NULL, 'smtp_user', NULL, NULL, NULL, 41),
(42, 4, 'text', NULL, 'smtp_password', NULL, '', '', NULL, 'smtp_password', NULL, NULL, NULL, 42),
(43, 4, 'text', NULL, 'sendmail_command', NULL, '', '', NULL, 'sendmail_command', NULL, NULL, NULL, 43),
(44, 4, 'custom', NULL, 'email_test', NULL, NULL, NULL, '', 'email_test', NULL, NULL, NULL, 44),
(45, 4, 'number', NULL, 'queue_rate', NULL, NULL, 'required', NULL, 'queue_rate', NULL, NULL, NULL, 45),
(46, 5, 'separator', NULL, 'separator_general', NULL, NULL, NULL, NULL, 'map_general', NULL, NULL, NULL, 46),
(47, 5, 'select', NULL, 'provider', NULL, 'osm|osm\r\nmapbox|mapbox', '', NULL, 'provider', NULL, NULL, NULL, 47),
(48, 5, 'text', NULL, 'access_token', NULL, '', '', NULL, 'access_token', NULL, NULL, NULL, 48),
(49, 5, 'separator', NULL, 'separator_coordinates', NULL, NULL, NULL, NULL, 'map_coordinates', NULL, NULL, NULL, 49),
(50, 5, 'mappicker', NULL, 'mappicker', NULL, NULL, NULL, NULL, 'mappicker', NULL, NULL, NULL, 50),
(51, 5, 'number', NULL, 'latitude', NULL, '', 'required', NULL, 'latitude', NULL, NULL, NULL, 51),
(52, 5, 'number', NULL, 'longitude', NULL, '', 'required', NULL, 'longitude', NULL, NULL, NULL, 52),
(53, 5, 'number', NULL, 'zoom', NULL, '', 'required|min:1|max:20', NULL, 'zoom', NULL, NULL, NULL, 53),
(54, 6, 'separator', NULL, 'separator_keys', NULL, NULL, NULL, NULL, 'security_keys', NULL, NULL, NULL, 54),
(55, 6, 'hash', NULL, 'encryption_key', NULL, NULL, 'required', NULL, 'encryption_key', NULL, NULL, NULL, 55),
(56, 6, 'hash', NULL, 'authentication_key', NULL, NULL, 'required', NULL, 'authentication_key', NULL, NULL, NULL, 56),
(57, 7, 'separator', NULL, 'separator_bans', NULL, NULL, NULL, NULL, 'other_bans', NULL, NULL, NULL, 57),
(58, 7, 'textarea', NULL, 'banned_words', NULL, NULL, '', NULL, 'banned_words', NULL, NULL, NULL, 58),
(59, 7, 'textarea', NULL, 'banned_ips', NULL, NULL, '', NULL, 'banned_ips', NULL, NULL, NULL, 59),
(60, 2, 'separator', NULL, 'separator_google', NULL, NULL, NULL, NULL, 'account_google', NULL, NULL, NULL, 60),
(61, 2, 'text', NULL, 'google_client_id', NULL, NULL, NULL, NULL, 'google_client_id', NULL, NULL, NULL, 61),
(62, 2, 'text', NULL, 'google_client_secret', NULL, NULL, NULL, NULL, 'google_client_secret', NULL, NULL, NULL, 62),
(63, 1, 'separator', NULL, 'separator_maintenance', NULL, NULL, NULL, NULL, 'general_maintenance', NULL, NULL, NULL, 63),
(64, 1, 'toggle', NULL, 'maintenance', NULL, NULL, NULL, NULL, 'maintenance', NULL, NULL, NULL, 64),
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
(85, 7, 'toggle', NULL, 'backlinkchecker_cancel_failed_listing_refund', NULL, NULL, '', NULL, 'backlinkchecker_cancel_failed_listing_refund', NULL, NULL, NULL, 85),
(86, 7, 'separator', NULL, 'separator_seo', NULL, NULL, NULL, NULL, 'seo_robots_txt', NULL, NULL, NULL, 86),
(87, 7, 'textarea', NULL, 'robots_txt', NULL, NULL, NULL, NULL, 'robots_txt', NULL, NULL, NULL, 87),
(88, 7, 'separator', NULL, 'separator_openai', NULL, NULL, NULL, NULL, 'openai', NULL, NULL, NULL, 88),
(89, 7, 'text', NULL, 'openai_api_key', NULL, NULL, NULL, NULL, 'openai_api_key', NULL, NULL, NULL, 89),
(90, 7, 'number', NULL, 'openai_daily_limit', NULL, NULL, NULL, NULL, 'openai_daily_limit', NULL, NULL, NULL, 90);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_settinggroups`
--

CREATE TABLE `phpls_settinggroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_settinggroups`
--

INSERT INTO `phpls_settinggroups` (`id`, `slug`) VALUES
(1, 'general'),
(2, 'account'),
(3, 'billing'),
(4, 'email'),
(5, 'map'),
(6, 'security'),
(7, 'other');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_settings`
--

CREATE TABLE `phpls_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `settinggroup_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_settings`
--

INSERT INTO `phpls_settings` (`id`, `settinggroup_id`, `name`, `value`) VALUES
(1, 1, 'site_name', 'Business Directory'),
(2, 1, 'timezone', '+0000'),
(3, 1, 'address_format', '{address} {location_3}, {location_2} {zip} {location_1}'),
(4, 1, 'custom_js', NULL),
(5, 1, 'captcha_site_key', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'),
(6, 1, 'captcha_secret_key', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'),
(7, 2, 'approval', '1'),
(8, 2, 'verification', '1'),
(9, 2, 'default_group', '2'),
(10, 2, 'default_provider', 'Native'),
(11, 2, 'facebook_app_id', NULL),
(12, 2, 'facebook_secret', NULL),
(13, 3, 'currency_code', 'USD'),
(14, 3, 'currency_sign', '$'),
(15, 3, 'currency_sign_position', 'prepend'),
(16, 3, 'tax', 'exclusive'),
(17, 3, 'invoice_company_details', 'phpListings.com\r\nCompany Address\r\nCity, Zip State, Country\r\n(123) 456-7890'),
(18, 3, 'invoice_product_name', 'Listing Membership'),
(19, 3, 'invoice_creation_days', '3'),
(20, 3, 'overdue_suspend_days', '5'),
(21, 3, 'invoice_reminder_1_days', '2'),
(22, 3, 'invoice_reminder_2_days', '3'),
(23, 3, 'invoice_reminder_3_days', '8'),
(24, 4, 'from_email', 'admin@phplistings.com'),
(25, 4, 'from_name', 'Business Directory'),
(26, 4, 'signature', 'Thank you.'),
(27, 4, 'transport', 'smtp'),
(28, 4, 'smtp_host', 'phplistings.com'),
(29, 4, 'smtp_port', '587'),
(30, 4, 'smtp_encryption', 'tls'),
(31, 4, 'smtp_user', 'user'),
(32, 4, 'smtp_password', 'password'),
(33, 4, 'sendmail_command', '/usr/sbin/sendmail'),
(34, 4, 'queue_rate', '10'),
(35, 5, 'provider', 'osm'),
(36, 5, 'access_token', 'pk.eyJ1IjoicGhwbGlzdGluZ3MiLCJhIjoiY2s1OGZpN2k3MGFxaTNvb2E2a3p5ZXBhMSJ9.l4HTlcVDtS2ngWLZygqSpg'),
(37, 5, 'latitude', '39.8283'),
(38, 5, 'longitude', '-98.5795'),
(39, 5, 'zoom', '4'),
(40, 6, 'encryption_key', '6e2b1361c8ce850ba2dc29f68586e533'),
(41, 6, 'authentication_key', 'da7f4f4ffb4d7d0f73654e8000a87720'),
(42, 7, 'banned_words', ''),
(43, 7, 'banned_ips', ''),
(44, 2, 'google_client_id', NULL),
(45, 2, 'google_client_secret', NULL),
(46, 1, 'maintenance', NULL),
(47, 7, 'banned_email_domains', ''),
(48, 7, 'deadlinkchecker', NULL),
(49, 7, 'deadlinkchecker_interval', 7),
(50, 7, 'deadlinkchecker_retry_interval', 6),
(51, 7, 'deadlinkchecker_max_retry_count', 8),
(52, 7, 'deadlinkchecker_client_notification_retry', ''),
(53, 7, 'deadlinkchecker_admin_notification_retry', ''),
(54, 7, 'deadlinkchecker_autoremove_failed_link', NULL),
(55, 7, 'backlinkchecker', NULL),
(56, 7, 'backlinkchecker_url', ''),
(57, 7, 'backlinkchecker_url_template', '&lt;a href=&quot;{link}&quot;&gt;{site_name}&lt;/a&gt;'),
(58, 7, 'backlinkchecker_follow_only', NULL),
(59, 7, 'backlinkchecker_interval', 7),
(60, 7, 'backlinkchecker_retry_interval', 6),
(61, 7, 'backlinkchecker_max_retry_count', 8),
(62, 7, 'backlinkchecker_client_notification_retry', ''),
(63, 7, 'backlinkchecker_admin_notification_retry', ''),
(64, 7, 'backlinkchecker_cancel_failed_listing', NULL),
(65, 7, 'backlinkchecker_cancel_failed_listing_refund', 1),
(66, 7, 'robots_txt', 'Sitemap: {sitemap}\r\nUser-agent: *\r\nAllow: /'),
(67, 7, 'openai_api_key', ''),
(68, 7, 'openai_daily_limit', 10);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_socialprofiletypes`
--

CREATE TABLE `phpls_socialprofiletypes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `icon_filename` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_socialprofiletypes`
--

INSERT INTO `phpls_socialprofiletypes` (`id`, `name`, `icon_filename`) VALUES
(1, 'Facebook', 'facebook.png'),
(2, 'X', 'x.png'),
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
(15, 'Yelp', 'yelp.png'),
(16, 'Alignable', 'alignable.png'),
(17, 'Indeed', 'indeed.png'),
(18, 'Nextdoor', 'nextdoor.png'),
(19, 'Threads', 'threads.png'),
(20, 'TripAdvisor', 'tripadvisor.png'),
(21, 'Bluesky', 'bluesky.png');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_stats`
--

CREATE TABLE `phpls_stats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `count` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_taxes`
--

CREATE TABLE `phpls_taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `compound` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `value` decimal(5,2) UNSIGNED DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_themes`
--

CREATE TABLE `phpls_themes` (
  `id` bigint UNSIGNED NOT NULL,
  `customizable` tinyint UNSIGNED DEFAULT NULL,
  `version` mediumint UNSIGNED DEFAULT NULL,
  `name` text,
  `slug` varchar(255) NOT NULL,
  `settings` mediumtext CHARACTER SET utf8mb4,
  `weight` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_themes`
--

INSERT INTO `phpls_themes` (`id`, `customizable`, `version`, `name`, `slug`, `settings`, `weight`) VALUES
(1, NULL, 1, 'Default', 'default', '[]', 1);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_transactions`
--

CREATE TABLE `phpls_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gateway_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `amount` float(15,2) UNSIGNED DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `error` text,
  `reference` text DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `paid_datetime` datetime DEFAULT NULL,
  `cancelled_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_types`
--

CREATE TABLE `phpls_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `deleted` tinyint(1) UNSIGNED DEFAULT NULL,
  `approvable` tinyint(1) UNSIGNED DEFAULT NULL,
  `approvable_updates` tinyint(1) UNSIGNED DEFAULT NULL,
  `approvable_reviews` tinyint(1) UNSIGNED DEFAULT NULL,
  `approvable_comments` tinyint(1) UNSIGNED DEFAULT NULL,
  `approvable_messages` tinyint(1) UNSIGNED DEFAULT NULL,
  `approvable_replies` tinyint(1) UNSIGNED DEFAULT NULL,
  `localizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `reviewable` tinyint(1) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `name_singular` text DEFAULT NULL,
  `name_plural` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `peruser_limit` mediumint(8) UNSIGNED DEFAULT NULL,
  `address_format` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_type_linked`
--

CREATE TABLE `phpls_type_linked` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_updatefielddata`
--

CREATE TABLE `phpls_updatefielddata` (
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `update_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_updates`
--

CREATE TABLE `phpls_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `event_start_datetime` datetime DEFAULT NULL,
  `event_frequency` text DEFAULT NULL,
  `event_interval` tinyint(2) UNSIGNED DEFAULT NULL,
  `event_weekdays` text DEFAULT NULL,
  `event_weeks` text DEFAULT NULL,
  `event_dates` text DEFAULT NULL,
  `event_end_datetime` datetime DEFAULT NULL,
  `event_rsvp` tinyint(1) UNSIGNED DEFAULT NULL,
  `offer_start_datetime` datetime DEFAULT NULL,
  `offer_end_datetime` datetime DEFAULT NULL,
  `offer_price` text DEFAULT NULL,
  `offer_discount_type` text DEFAULT NULL,
  `offer_discount` text DEFAULT NULL,
  `offer_count` text DEFAULT NULL,
  `offer_terms` text DEFAULT NULL,
  `offer_redeem` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `zoom` tinyint(2) UNSIGNED DEFAULT NULL,
  `timezone` char(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_uploadtypes`
--

CREATE TABLE `phpls_uploadtypes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `public` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `max_files` mediumint(8) UNSIGNED DEFAULT NULL,
  `max_size` varchar(255) DEFAULT NULL,
  `small_image_resize_type` tinyint(1) UNSIGNED DEFAULT NULL,
  `small_image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `small_image_height` smallint(5) UNSIGNED DEFAULT NULL,
  `small_image_quality` tinyint(3) UNSIGNED DEFAULT NULL,
  `medium_image_resize_type` tinyint(1) UNSIGNED DEFAULT NULL,
  `medium_image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `medium_image_height` smallint(5) UNSIGNED DEFAULT NULL,
  `medium_image_quality` tinyint(3) UNSIGNED DEFAULT NULL,
  `large_image_resize_type` tinyint(1) UNSIGNED DEFAULT NULL,
  `large_image_width` smallint(5) UNSIGNED DEFAULT NULL,
  `large_image_height` smallint(5) UNSIGNED DEFAULT NULL,
  `large_image_quality` tinyint(3) UNSIGNED DEFAULT NULL,
  `watermark_file_path` text DEFAULT NULL,
  `watermark_position_vertical` varchar(255) DEFAULT NULL,
  `watermark_position_horizontal` varchar(255) DEFAULT NULL,
  `watermark_transparency` tinyint(3) UNSIGNED DEFAULT NULL,
  `cropbox_width` smallint(5) UNSIGNED DEFAULT NULL,
  `cropbox_height` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_uploadtypes`
--

INSERT INTO `phpls_uploadtypes` (`id`, `customizable`, `public`, `name`, `max_files`, `max_size`, `small_image_resize_type`, `small_image_width`, `small_image_height`, `small_image_quality`, `medium_image_resize_type`, `medium_image_width`, `medium_image_height`, `medium_image_quality`, `large_image_resize_type`, `large_image_width`, `large_image_height`, `large_image_quality`, `watermark_file_path`, `watermark_position_vertical`, `watermark_position_horizontal`, `watermark_transparency`, `cropbox_width`, `cropbox_height`) VALUES
(1, NULL, 1, '{\"en\":\"Listing Logo\"}', 1, 10, 1, 300, 225, 95, 1, 400, 300, 95, 1, 1024, 768, 95, '', 'bottom', 'right', 50, 300, 225),
(2, NULL, 1, '{\"en\":\"Listing Gallery\"}', 10, 10, 1, 300, 225, 90, 1, 400, 300, 90, 1, 1024, 768, 95, '', 'top', 'left', 50, 300, 225),
(3, NULL, NULL, '{\"en\":\"Category Logo\"}', 1, 10, 1, 300, 225, 95, 1, 400, 300, 95, 1, 1024, 768, 95, '', 'top', 'left', 50, 300, 225),
(4, NULL, NULL, '{\"en\":\"Location Logo\"}', 1, 10, 1, 250, 350, 90, 1, 500, 700, 90, 1, 500, 700, 90, '', 'top', 'left', 50, 500, 700),
(5, NULL, NULL, '{\"en\":\"Category \\/ Location Header\"}', 1, 10, 1, 250, 50, 90, 1, 500, 100, 90, 1, 1000, 200, 95, '', 'top', 'left', 50, 500, 100),
(6, NULL, NULL, '{\"en\":\"Search Box Slider\"}', 10, 10, 1, 300, 100, 90, 1, 300, 100, 90, 1, 1280, 426, 90, '', 'top', 'left', 50, 600, 200),
(7, NULL, NULL, '{\"en\":\"Website Header Logo\"}', 1, 10, 1, 200, 60, 90, 1, 200, 60, 90, 1, 400, 120, 90, '', 'top', 'left', 50, 200, 60),
(20, NULL, NULL, '{\"en\":\"Two-column Teaser Image\"}', 1, 10, 1, 300, 225, 95, 1, 400, 300, 95, 1, 1024, 768, 95, '', 'top', 'left', 50, 300, 225),
(30, NULL, NULL, '{\"en\":\"Quad-box Teaser Image\"}', 1, 10, 1, 320, 240, 95, 1, 640, 480, 95, 1, 1024, 768, 95, '', 'top', 'left', 50, 320, 240),
(40, NULL, NULL, '{\"en\":\"Listing Badge\"}', 1, 10, 1, 200, 200, 95, 1, 300, 300, 95, 1, 400, 400, 95, '', 'top', 'left', 50, 300, 300);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_userfielddata`
--

CREATE TABLE `phpls_userfielddata` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_usergroups`
--

CREATE TABLE `phpls_usergroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_usergroups`
--

INSERT INTO `phpls_usergroups` (`id`, `customizable`, `name`) VALUES
(1, NULL, '{\"en\":\"Administrator\"}'),
(2, NULL, '{\"en\":\"Registered User\"}');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_usergroup_userrole`
--

CREATE TABLE `phpls_usergroup_userrole` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usergroup_id` bigint(20) UNSIGNED NOT NULL,
  `userrole_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_usergroup_userrole`
--

INSERT INTO `phpls_usergroup_userrole` (`id`, `usergroup_id`, `userrole_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(17, 1, 17),
(18, 1, 18),
(19, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_userroles`
--

CREATE TABLE `phpls_userroles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_userroles`
--

INSERT INTO `phpls_userroles` (`id`, `name`) VALUES
(1, 'admin_login'),
(2, 'user_login'),
(3, 'admin_content'),
(4, 'admin_listings'),
(5, 'admin_reviews'),
(6, 'admin_messages'),
(7, 'admin_claims'),
(8, 'admin_categories'),
(9, 'admin_products'),
(10, 'admin_fields'),
(11, 'admin_users'),
(12, 'admin_files'),
(13, 'admin_locations'),
(14, 'admin_emails'),
(15, 'admin_appearance'),
(16, 'admin_settings'),
(17, 'admin_import'),
(18, 'admin_export');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_users`
--

CREATE TABLE `phpls_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `import_id` bigint(20) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `email_verified` tinyint(1) UNSIGNED DEFAULT NULL,
  `banned` tinyint(1) UNSIGNED DEFAULT NULL,
  `taxable` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` text DEFAULT NULL,
  `last_name` text DEFAULT NULL,
  `email` text NOT NULL,
  `timezone` char(6) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `zoom` tinyint(2) UNSIGNED DEFAULT NULL,
  `added_datetime` datetime DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_users`
--

INSERT INTO `phpls_users` (`id`, `active`, `email_verified`, `banned`, `taxable`, `first_name`, `last_name`, `email`, `timezone`, `token`, `verification_code`, `address`, `zip`, `location_id`, `latitude`, `longitude`, `zoom`, `added_datetime`, `updated_datetime`) VALUES
(1, 1, 1, NULL, 1, 'Website', 'Administrator', 'admin@phplistings.com', '-0700', '6ec3b180a3c214e05a6aa4c183d82267', '96ea994aa029a16687837b5fa69c6047', 'address', '12345', '2', '34.14363482031264', '-118.37872638512164', '17', '2020-06-01 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgetfieldconstraints`
--

CREATE TABLE `phpls_widgetfieldconstraints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `widgetfield_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_widgetfieldconstraints`
--

INSERT INTO `phpls_widgetfieldconstraints` (`id`, `widgetfield_id`, `name`, `value`, `weight`) VALUES
(1, 1, 'required', '', 1),
(2, 2, 'required', '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgetfieldgroups`
--

CREATE TABLE `phpls_widgetfieldgroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_widgetfieldgroups`
--

INSERT INTO `phpls_widgetfieldgroups` (`id`, `customizable`, `name`, `slug`) VALUES
(1, NULL, 'Contact Form', 'contactform');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgetfieldoptions`
--

CREATE TABLE `phpls_widgetfieldoptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `widgetfield_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `value` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgetfields`
--

CREATE TABLE `phpls_widgetfields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `widgetfieldgroup_id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `upload_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `label` text DEFAULT NULL,
  `tooltip` varchar(255) DEFAULT NULL,
  `placeholder` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_widgetfields`
--

INSERT INTO `phpls_widgetfields` (`id`, `widgetfieldgroup_id`, `customizable`, `upload_id`, `type`, `name`, `value`, `label`, `tooltip`, `placeholder`, `description`, `weight`) VALUES
(1, 1, NULL, NULL, 'text', 'name', '', '{\"en\":\"Your Name\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 1),
(2, 1, NULL, NULL, 'email', 'email', '', '{\"en\":\"Your Email\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 2),
(3, 1, NULL, NULL, 'textarea', 'comment', '', '{\"en\":\"Your Question\"}', NULL, '{\"en\":\"\"}', '{\"en\":\"\"}', 3),
(4, 1, NULL, NULL, 'captcha', 'captcha', NULL, NULL, NULL, NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgetmenugroups`
--

CREATE TABLE `phpls_widgetmenugroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customizable` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_widgetmenugroups`
--

INSERT INTO `phpls_widgetmenugroups` (`id`, `customizable`, `name`) VALUES
(1, NULL, 'Header Menu'),
(2, NULL, 'Footer Menu');

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgetmenuitems`
--

CREATE TABLE `phpls_widgetmenuitems` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `widgetmenugroup_id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `public` tinyint(1) UNSIGNED DEFAULT NULL,
  `highlighted` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `nofollow` tinyint(1) UNSIGNED DEFAULT NULL,
  `_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weight` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_widgetmenuitems`
--

INSERT INTO `phpls_widgetmenuitems` (`id`, `widgetmenugroup_id`, `active`, `public`, `name`, `route`, `link`, `target`, `nofollow`, `_parent_id`, `weight`) VALUES
(1, 1, 1, 1, '{\"en\":\"Contact Us\"}', 'contact', '', '_self', NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `phpls_widgets`
--

CREATE TABLE `phpls_widgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `phpls_widgets`
--

INSERT INTO `phpls_widgets` (`id`, `name`, `slug`) VALUES
(1, '{\"en\":\"Header\"}', 'header'),
(2, '{\"en\":\"Footer\"}', 'footer'),
(3, '{\"en\":\"Custom\"}', 'custom'),
(4, '{\"en\":\"Search Box\"}', 'searchbox'),
(5, '{\"en\":\"Search Bar\"}', 'searchbar'),
(6, '{\"en\":\"Map\"}', 'map'),
(7, '{\"en\":\"3-column Teaser\"}', 'threecolumnteaser'),
(8, '{\"en\":\"Categories\"}', 'categories'),
(9, '{\"en\":\"Listings\"}', 'listings'),
(10, '{\"en\":\"Newsletter Subscribe\"}', 'newsletter'),
(11, '{\"en\":\"Locations\"}', 'locations'),
(12, '{\"en\":\"Create Account Teaser\"}', 'addaccountteaser'),
(13, '{\"en\":\"Pricing\"}', 'pricing'),
(14, '{\"en\":\"Contact Form\"}', 'contactform'),
(15, '{\"en\":\"Account Header\"}', 'accountheader'),
(16, '{\"en\":\"Listing Search Results\"}', 'listingsearchresults'),
(17, '{\"en\":\"Listing Search Results Header\"}', 'listingsearchresultsheader'),
(18, '{\"en\":\"Listing Gallery Slider\"}', 'listinggalleryslider'),
(19, '{\"en\":\"Listing\"}', 'listing'),
(20, '{\"en\":\"Listing Header\"}', 'listingheader'),
(21, '{\"en\":\"Listing Reviews\"}', 'listingreviews'),
(22, '{\"en\":\"Listing Send Message Form\"}', 'listingsendmessageform'),
(23, '{\"en\":\"Listing Add Review Form\"}', 'listingaddreviewform'),
(24, '{\"en\":\"Listing Claim Form\"}', 'listingclaimform'),
(25, '{\"en\":\"Image\"}', 'image'),
(26, '{\"en\":\"Slider\"}', 'slider'),
(27, '{\"en\":\"Banner\"}', 'banner'),
(28, '{\"en\":\"404\"}', 'error404'),
(29, '{\"en\":\"405\"}', 'error405'),
(30, '{\"en\":\"Data Wrapper\"}', 'datawrapper'),
(31, '{\"en\":\"2-column Teaser\"}', 'twocolumnteaser'),
(32, '{\"en\":\"User\"}', 'user'),
(33, '{\"en\":\"Maintenance\"}', 'maintenance'),
(34, '{\"en\":\"YouTube Video\"}', 'youtube'),
(35, '{\"en\":\"Reviews\"}', 'reviews'),
(36, '{\"en\":\"Quad-box Teaser\"}', 'quadboxteaser'),
(37, '{\"en\":\"Popup\"}', 'popup');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `phpls_accounts`
--
ALTER TABLE `phpls_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`user_id`) USING BTREE;

--
-- Indexes for table `phpls_badges`
--
ALTER TABLE `phpls_badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_badge_listing`
--
ALTER TABLE `phpls_badge_listing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_badge_product`
--
ALTER TABLE `phpls_badge_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_bookmarks`
--
ALTER TABLE `phpls_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`user_id`);

--
-- Indexes for table `phpls_cache`
--
ALTER TABLE `phpls_cache`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `phpls_categories`
--
ALTER TABLE `phpls_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slugIdx` (`slug`) USING BTREE,
  ADD KEY `parentIdx` (`_parent_id`) USING BTREE;

--
-- Indexes for table `phpls_category_export`
--
ALTER TABLE `phpls_category_export`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_category_listing`
--
ALTER TABLE `phpls_category_listing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lookupIdx` (`category_id`,`listing_id`);

--
-- Indexes for table `phpls_category_listingfield`
--
ALTER TABLE `phpls_category_listingfield`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`category_id`, `listingfield_id`);
--
-- Indexes for table `phpls_category_product`
--
ALTER TABLE `phpls_category_product`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`category_id`, `product_id`);
--
-- Indexes for table `phpls_category_update`
--
ALTER TABLE `phpls_category_update`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`category_id`, `update_id`);
--
-- Indexes for table `phpls_claims`
--
ALTER TABLE `phpls_claims`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_comments`
--
ALTER TABLE `phpls_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`user_id`) USING BTREE;

--
-- Indexes for table `phpls_cronjobs`
--
ALTER TABLE `phpls_cronjobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_dates`
--
ALTER TABLE `phpls_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listingIdx` (`listing_id`),
  ADD KEY `dateIdx` (`event_date`);

--
-- Indexes for table `phpls_discounts`
--
ALTER TABLE `phpls_discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_discount_pricing`
--
ALTER TABLE `phpls_discount_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_discount_required`
--
ALTER TABLE `phpls_discount_required`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_emails`
--
ALTER TABLE `phpls_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `statusIdx` (`status`,`schedule_datetime`);

--
-- Indexes for table `phpls_emailtemplates`
--
ALTER TABLE `phpls_emailtemplates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nameIdx` (`name`);

--
-- Indexes for table `phpls_event_user`
--
ALTER TABLE `phpls_event_user`
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
-- Indexes for table `phpls_fieldconstraints`
--
ALTER TABLE `phpls_fieldconstraints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userfieldIdx` (`field_id`);

--
-- Indexes for table `phpls_fieldgroups`
--
ALTER TABLE `phpls_fieldgroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_fieldoptions`
--
ALTER TABLE `phpls_fieldoptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_fields`
--
ALTER TABLE `phpls_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_files`
--
ALTER TABLE `phpls_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documentIdx` (`document_id`);

--
-- Indexes for table `phpls_filetypes`
--
ALTER TABLE `phpls_filetypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_filetype_uploadtype`
--
ALTER TABLE `phpls_filetype_uploadtype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_gateways`
--
ALTER TABLE `phpls_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_gateway_pricing`
--
ALTER TABLE `phpls_gateway_pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_hours`
--
ALTER TABLE `phpls_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lookupIdx` (`hash`,`start_time`);

--
-- Indexes for table `phpls_imports`
--
ALTER TABLE `phpls_imports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_invoices`
--
ALTER TABLE `phpls_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderIdx` (`order_id`);

--
-- Indexes for table `phpls_languages`
--
ALTER TABLE `phpls_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_linked_update`
--
ALTER TABLE `phpls_linked_update`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_listingfieldconstraints`
--
ALTER TABLE `phpls_listingfieldconstraints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listingfieldIdx` (`listingfield_id`);

--
-- Indexes for table `phpls_listingfielddata`
--
ALTER TABLE `phpls_listingfielddata`
  ADD PRIMARY KEY (`listing_id`,`field_name`);

--
-- Indexes for table `phpls_listingfieldgroups`
--
ALTER TABLE `phpls_listingfieldgroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_listingfieldoptions`
--
ALTER TABLE `phpls_listingfieldoptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listingfieldIdx` (`listingfield_id`);

--
-- Indexes for table `phpls_listingfields`
--
ALTER TABLE `phpls_listingfields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `typeIdx` (`type_id`);

--
-- Indexes for table `phpls_listingfield_product`
--
ALTER TABLE `phpls_listingfield_product`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`listingfield_id`, `product_id`);

--
-- Indexes for table `phpls_listings`
--
ALTER TABLE `phpls_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lookupIdx` (`active`,`status`,`type_id`,`category_id`,`location_id`),
  ADD KEY `locationIdx` (`location_id`),
  ADD KEY `categoryIdx` (`category_id`);
ALTER TABLE `phpls_listings` ADD FULLTEXT KEY `fulltextIdx` (`title`,`short_description`,`description`);

--
-- Indexes for table `phpls_listing_linked`
--
ALTER TABLE `phpls_listing_linked`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`child_id`, `parent_id`);
--
-- Indexes for table `phpls_locations`
--
ALTER TABLE `phpls_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slugIdx` (`slug`) USING BTREE,
  ADD KEY `parentIdx` (`_parent_id`);

--
-- Indexes for table `phpls_logs`
--
ALTER TABLE `phpls_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `typeIdx` (`type`);

--
-- Indexes for table `phpls_messagefielddata`
--
ALTER TABLE `phpls_messagefielddata`
  ADD PRIMARY KEY (`message_id`,`field_name`) USING BTREE;

--
-- Indexes for table `phpls_messages`
--
ALTER TABLE `phpls_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`sender_id`) USING BTREE;

--
-- Indexes for table `phpls_offer_user`
--
ALTER TABLE `phpls_offer_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_orders`
--
ALTER TABLE `phpls_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `listingIdx` (`listing_id`);

--
-- Indexes for table `phpls_pages`
--
ALTER TABLE `phpls_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lookupIdx` (`slug`);

--
-- Indexes for table `phpls_page_widget`
--
ALTER TABLE `phpls_page_widget`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`page_id`);

--
-- Indexes for table `phpls_pricings`
--
ALTER TABLE `phpls_pricings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productIdx` (`product_id`);

--
-- Indexes for table `phpls_pricing_required`
--
ALTER TABLE `phpls_pricing_required`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_pricing_upgrade`
--
ALTER TABLE `phpls_pricing_upgrade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_products`
--
ALTER TABLE `phpls_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `typeIdx` (`type_id`);

--
-- Indexes for table `phpls_ratings`
--
ALTER TABLE `phpls_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_rating_type`
--
ALTER TABLE `phpls_rating_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_rawstats`
--
ALTER TABLE `phpls_rawstats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_reminders`
--
ALTER TABLE `phpls_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`user_id`) USING BTREE;

--
-- Indexes for table `phpls_replies`
--
ALTER TABLE `phpls_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`user_id`) USING BTREE;

--
-- Indexes for table `phpls_reviewfielddata`
--
ALTER TABLE `phpls_reviewfielddata`
  ADD PRIMARY KEY (`review_id`,`field_name`) USING BTREE;

--
-- Indexes for table `phpls_reviews`
--
ALTER TABLE `phpls_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userIdx` (`user_id`) USING BTREE;

--
-- Indexes for table `phpls_sessions`
--
ALTER TABLE `phpls_sessions`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `phpls_settingfields`
--
ALTER TABLE `phpls_settingfields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupIdx` (`settinggroup_id`);

--
-- Indexes for table `phpls_settinggroups`
--
ALTER TABLE `phpls_settinggroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_settings`
--
ALTER TABLE `phpls_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settinggroupIdx` (`settinggroup_id`);

--
-- Indexes for table `phpls_socialprofiletypes`
--
ALTER TABLE `phpls_socialprofiletypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_stats`
--
ALTER TABLE `phpls_stats`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`type`, `type_id`, `date`);

--
-- Indexes for table `phpls_taxes`
--
ALTER TABLE `phpls_taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_themes`
--
ALTER TABLE `phpls_themes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_transactions`
--
ALTER TABLE `phpls_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_types`
--
ALTER TABLE `phpls_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_type_linked`
--
ALTER TABLE `phpls_type_linked`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_updatefielddata`
--
ALTER TABLE `phpls_updatefielddata`
  ADD PRIMARY KEY (`update_id`,`field_name`) USING BTREE;

--
-- Indexes for table `phpls_updates`
--
ALTER TABLE `phpls_updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_uploadtypes`
--
ALTER TABLE `phpls_uploadtypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_userfielddata`
--
ALTER TABLE `phpls_userfielddata`
  ADD PRIMARY KEY (`user_id`,`field_name`) USING BTREE;

--
-- Indexes for table `phpls_usergroups`
--
ALTER TABLE `phpls_usergroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_usergroup_userrole`
--
ALTER TABLE `phpls_usergroup_userrole`
  ADD PRIMARY KEY (`id`),
  ADD INDEX `lookupIdx` (`usergroup_id`, `userrole_id`);

--
-- Indexes for table `phpls_userroles`
--
ALTER TABLE `phpls_userroles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_users`
--
ALTER TABLE `phpls_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locationIdx` (`location_id`);
ALTER TABLE `phpls_users` ADD FULLTEXT KEY `fulltextIdx` (`first_name`,`last_name`);

--
-- Indexes for table `phpls_widgetfieldconstraints`
--
ALTER TABLE `phpls_widgetfieldconstraints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `widgetfieldIdx` (`widgetfield_id`);

--
-- Indexes for table `phpls_widgetfieldgroups`
--
ALTER TABLE `phpls_widgetfieldgroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_widgetfieldoptions`
--
ALTER TABLE `phpls_widgetfieldoptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `widgetfieldIdx` (`widgetfield_id`);

--
-- Indexes for table `phpls_widgetfields`
--
ALTER TABLE `phpls_widgetfields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `widgetfieldgroupIdx` (`widgetfieldgroup_id`);

--
-- Indexes for table `phpls_widgetmenugroups`
--
ALTER TABLE `phpls_widgetmenugroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phpls_widgetmenuitems`
--
ALTER TABLE `phpls_widgetmenuitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `widgetmenugroupIdx` (`widgetmenugroup_id`);

--
-- Indexes for table `phpls_widgets`
--
ALTER TABLE `phpls_widgets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `phpls_accounts`
--
ALTER TABLE `phpls_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_badges`
--
ALTER TABLE `phpls_badges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_badge_listing`
--
ALTER TABLE `phpls_badge_listing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_badge_product`
--
ALTER TABLE `phpls_badge_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_bookmarks`
--
ALTER TABLE `phpls_bookmarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_categories`
--
ALTER TABLE `phpls_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_category_export`
--
ALTER TABLE `phpls_category_export`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_category_listing`
--
ALTER TABLE `phpls_category_listing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_category_listingfield`
--
ALTER TABLE `phpls_category_listingfield`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_category_product`
--
ALTER TABLE `phpls_category_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_category_update`
--
ALTER TABLE `phpls_category_update`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_claims`
--
ALTER TABLE `phpls_claims`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_comments`
--
ALTER TABLE `phpls_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_cronjobs`
--
ALTER TABLE `phpls_cronjobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `phpls_dates`
--
ALTER TABLE `phpls_dates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_discounts`
--
ALTER TABLE `phpls_discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_discount_pricing`
--
ALTER TABLE `phpls_discount_pricing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_discount_required`
--
ALTER TABLE `phpls_discount_required`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_emails`
--
ALTER TABLE `phpls_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_emailtemplates`
--
ALTER TABLE `phpls_emailtemplates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `phpls_event_user`
--
ALTER TABLE `phpls_event_user`
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
-- AUTO_INCREMENT for table `phpls_fieldconstraints`
--
ALTER TABLE `phpls_fieldconstraints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `phpls_fieldgroups`
--
ALTER TABLE `phpls_fieldgroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_fieldoptions`
--
ALTER TABLE `phpls_fieldoptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_fields`
--
ALTER TABLE `phpls_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `phpls_files`
--
ALTER TABLE `phpls_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_filetypes`
--
ALTER TABLE `phpls_filetypes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `phpls_filetype_uploadtype`
--
ALTER TABLE `phpls_filetype_uploadtype`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `phpls_gateways`
--
ALTER TABLE `phpls_gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `phpls_gateway_pricing`
--
ALTER TABLE `phpls_gateway_pricing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_hours`
--
ALTER TABLE `phpls_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_imports`
--
ALTER TABLE `phpls_imports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_invoices`
--
ALTER TABLE `phpls_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_languages`
--
ALTER TABLE `phpls_languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_linked_update`
--
ALTER TABLE `phpls_linked_update`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_listingfieldconstraints`
--
ALTER TABLE `phpls_listingfieldconstraints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_listingfieldgroups`
--
ALTER TABLE `phpls_listingfieldgroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `phpls_listingfieldoptions`
--
ALTER TABLE `phpls_listingfieldoptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_listingfields`
--
ALTER TABLE `phpls_listingfields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_listingfield_product`
--
ALTER TABLE `phpls_listingfield_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_listings`
--
ALTER TABLE `phpls_listings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_listing_linked`
--
ALTER TABLE `phpls_listing_linked`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_locations`
--
ALTER TABLE `phpls_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phpls_locations`
--
ALTER TABLE `phpls_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_messages`
--
ALTER TABLE `phpls_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_offer_user`
--
ALTER TABLE `phpls_offer_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_orders`
--
ALTER TABLE `phpls_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_pages`
--
ALTER TABLE `phpls_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `phpls_page_widget`
--
ALTER TABLE `phpls_page_widget`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `phpls_pricings`
--
ALTER TABLE `phpls_pricings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_pricing_required`
--
ALTER TABLE `phpls_pricing_required`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_pricing_upgrade`
--
ALTER TABLE `phpls_pricing_upgrade`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_products`
--
ALTER TABLE `phpls_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_ratings`
--
ALTER TABLE `phpls_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `phpls_rating_type`
--
ALTER TABLE `phpls_rating_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_rawstats`
--
ALTER TABLE `phpls_rawstats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_reminders`
--
ALTER TABLE `phpls_reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_replies`
--
ALTER TABLE `phpls_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_reviews`
--
ALTER TABLE `phpls_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_settingfields`
--
ALTER TABLE `phpls_settingfields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `phpls_settinggroups`
--
ALTER TABLE `phpls_settinggroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `phpls_settings`
--
ALTER TABLE `phpls_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `phpls_socialprofiletypes`
--
ALTER TABLE `phpls_socialprofiletypes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `phpls_stats`
--
ALTER TABLE `phpls_stats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_taxes`
--
ALTER TABLE `phpls_taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_themes`
--
ALTER TABLE `phpls_themes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_transactions`
--
ALTER TABLE `phpls_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_types`
--
ALTER TABLE `phpls_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_type_linked`
--
ALTER TABLE `phpls_type_linked`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_updates`
--
ALTER TABLE `phpls_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_uploadtypes`
--
ALTER TABLE `phpls_uploadtypes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `phpls_usergroups`
--
ALTER TABLE `phpls_usergroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phpls_usergroup_userrole`
--
ALTER TABLE `phpls_usergroup_userrole`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `phpls_userroles`
--
ALTER TABLE `phpls_userroles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `phpls_users`
--
ALTER TABLE `phpls_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_widgetfieldconstraints`
--
ALTER TABLE `phpls_widgetfieldconstraints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phpls_widgetfieldgroups`
--
ALTER TABLE `phpls_widgetfieldgroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_widgetfieldoptions`
--
ALTER TABLE `phpls_widgetfieldoptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phpls_widgetfields`
--
ALTER TABLE `phpls_widgetfields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `phpls_widgetmenugroups`
--
ALTER TABLE `phpls_widgetmenugroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phpls_widgetmenuitems`
--
ALTER TABLE `phpls_widgetmenuitems`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phpls_widgets`
--
ALTER TABLE `phpls_widgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

COMMIT;
