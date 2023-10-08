-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 07-10-2023 a las 21:57:30
-- Versión del servidor: 5.7.23-23
-- Versión de PHP: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ceylinks_PruebaH3`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assign_forms_roles`
--

CREATE TABLE `assign_forms_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assign_forms_users`
--

CREATE TABLE `assign_forms_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `images`, `short_description`, `description`, `category_id`, `slug`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Prueba 1 Blog', 'blogs/QBlrKarwwVD5moxEoNeGThKYDQ4STCKbBVLVro1i.png', 'Lorem Ipsum&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'Lorem Ipsum&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\nLorem Ipsum&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 1, 'prueba-1-blog', '1', '2023-10-08 06:37:13', '2023-10-08 06:37:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Noticias', 1, '2023-10-08 06:35:44', '2023-10-08 06:35:44'),
(2, 'Tortugas', 1, '2023-10-08 06:35:52', '2023-10-08 06:35:52'),
(3, 'Actividades', 1, '2023-10-08 06:36:08', '2023-10-08 06:36:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_website` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_slots` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `json` longtext COLLATE utf8mb4_unicode_ci,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) DEFAULT NULL,
  `currency_symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `booking_values`
--

CREATE TABLE `booking_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `json` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `currency_symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_slots_date` date DEFAULT NULL,
  `booking_slots` text COLLATE utf8mb4_unicode_ci,
  `booking_seats_date` date DEFAULT NULL,
  `booking_seats_session` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_seats` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `booking_status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ch_favorites`
--

CREATE TABLE `ch_favorites` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `favorite_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ch_messages`
--

CREATE TABLE `ch_messages` (
  `id` bigint(20) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_id` bigint(20) NOT NULL,
  `to_id` bigint(20) NOT NULL,
  `body` varchar(5000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `poll_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments_replies`
--

CREATE TABLE `comments_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_id` bigint(20) NOT NULL,
  `poll_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dashboard_widgets`
--

CREATE TABLE `dashboard_widgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` double(8,2) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poll_id` bigint(20) UNSIGNED DEFAULT NULL,
  `chart_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_genrators`
--

CREATE TABLE `document_genrators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `change_log_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `change_log_json` longtext COLLATE utf8mb4_unicode_ci,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_menus`
--

CREATE TABLE `document_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `json` text COLLATE utf8mb4_unicode_ci,
  `html` longtext COLLATE utf8mb4_unicode_ci,
  `document_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `questions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `order` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `faqs`
--

INSERT INTO `faqs` (`id`, `questions`, `answer`, `order`, `created_at`, `updated_at`) VALUES
(1, 'What is Prime Laravel Form Builder?', 'Prime Laravel Form Builder is a powerful and user-friendly form-building solution specifically designed for Laravel, a popular PHP framework. It provides developers with a comprehensive set of tools and components to effortlessly create and manage forms within their Laravel applications.', '0', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'What are the key features of Prime Laravel Form Builder?', 'Prime Laravel Form Builder offers an array of features to simplify the form-building process. Some key features include: 1. Drag-and-drop form builder interface for intuitive form creation. 2. Wide range of pre-built form elements such as text fields, checkboxes, radio buttons, dropdowns, and more. 3. Flexible customization options to tailor forms to specific requirements. 4. Form validation rules and error handling for data integrity. 5. Seamless integration with Laravel\'s form handling and processing capabilities. 6. Ability to generate clean and semantic HTML code for optimal performance. 7. Built-in support for form themes and templates for consistent styling across applications. 8. Extensive documentation and dedicated customer support for assistance and troubleshooting.', '1', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'Is Prime Laravel Form Builder compatible with Laravel versions?', 'Yes, Prime Laravel Form Builder is designed to seamlessly integrate with different versions of Laravel, ensuring compatibility and smooth functioning. It is regularly updated to align with the latest Laravel releases, ensuring developers can leverage its features without compatibility concerns.', '2', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'Can Prime Laravel Form Builder handle complex form requirements?', 'Absolutely. Prime Laravel Form Builder is built to handle a wide range of form complexities. Whether you need multi-step forms, conditional logic, form validation rules, or dynamic form elements, it provides a robust framework to handle even the most intricate form requirements efficiently.', '3', '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `footer_settings`
--

CREATE TABLE `footer_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `footer_settings`
--

INSERT INTO `footer_settings` (`id`, `menu`, `slug`, `parent_id`, `page_id`, `created_at`, `updated_at`) VALUES
(1, 'Company', 'company', 0, NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'Product', 'product', 0, NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'Download', 'download', 0, NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'Support', 'support', 0, NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(5, 'About Us', 'about-us', 1, 1, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(6, 'Our Team', 'our-team', 1, 2, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(7, 'Products', 'products', 1, 3, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(8, 'Contact', 'contact', 1, 4, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(9, 'Feature', 'feature', 2, 5, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(10, 'Pricing', 'pricing', 2, 6, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(11, 'Credit', 'Credit', 2, 7, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(12, 'News', 'news', 2, 8, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(13, 'iOS', 'ios', 3, 9, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(14, 'Android', 'android', 3, 10, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(15, 'Microsoft', 'microsoft', 3, 11, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(16, 'Desktop', 'desktop', 3, 12, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(17, 'Help', 'help', 4, 13, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(18, 'Terms', 'terms', 4, 14, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(19, 'FAQ', 'fAQ', 4, 15, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(20, 'Privacy', 'privacy', 4, 16, '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forms`
--

CREATE TABLE `forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `bccemail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ccemail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `success_msg` text COLLATE utf8mb4_unicode_ci,
  `thanks_msg` text COLLATE utf8mb4_unicode_ci,
  `amount` double(8,2) DEFAULT NULL,
  `currency_symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `allow_share_section` bigint(20) DEFAULT NULL,
  `allow_comments` bigint(20) DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assign_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `json` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'theme1',
  `theme_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'theme-2',
  `theme_background_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'form-themes/theme3/form-background.png',
  `set_end_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `set_end_date_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `form_comments`
--

CREATE TABLE `form_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `form_comments_controllers`
--

CREATE TABLE `form_comments_controllers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `form_comments_replies`
--

CREATE TABLE `form_comments_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `form_integration_settings`
--

CREATE TABLE `form_integration_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `field_json` longtext COLLATE utf8mb4_unicode_ci,
  `json` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `form_values`
--

CREATE TABLE `form_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `json` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `currency_symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `header_settings`
--

CREATE TABLE `header_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `image_polls`
--

CREATE TABLE `image_polls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vote` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poll_id` bigint(20) NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_securities`
--

CREATE TABLE `login_securities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `google2fa_enable` tinyint(1) NOT NULL DEFAULT '0',
  `google2fa_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mail_templates`
--

CREATE TABLE `mail_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `mailable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `html_template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_template` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mail_templates`
--

INSERT INTO `mail_templates` (`id`, `mailable`, `subject`, `html_template`, `text_template`, `created_at`, `updated_at`) VALUES
(1, 'App\\Mail\\TestMail', 'Mail send for testing purpose', '<p><strong>This Mail For Testing</strong></p>\n            <p><strong>Thanks</strong></p>', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'App\\Mail\\Thanksmail', 'New survey Submited - {{ title }}', '<div class=\"section-body\">\n            <div class=\"mx-0 row\">\n            <div class=\"mx-auto col-6\">\n            <div class=\"card\">\n            <div class=\"card-header\">\n            <h4 class=\"text-center w-100\">{{ title }}</h4>\n            </div>\n            <div class=\"card-body\">\n            <div class=\"text-center\">\n            <img src=\"{{image}}\" id=\"app-dark-logo\" class=\"my-5 text-center img img-responsive w-30 justify-content-center\"/>\n            </div>\n            <h2 class=\"text-center w-100\">{{ thanks_msg }}</h2>\n            </div>\n            </div>\n            </div>\n            </div>\n            </div>', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'App\\Mail\\BookingThanksmail', 'New booking Submited - {{ title }}', '<div class=\"section-body\">\n            <div class=\"mx-0 row\">\n            <div class=\"mx-auto col-6\">\n            <div class=\"card\">\n            <div class=\"card-header\">\n            <h4 class=\"text-center w-100\">{{ title }}</h4>\n            </div>\n            <div class=\"card-body\">\n            <div class=\"text-center\">\n            <img src=\"{{image}}\" id=\"app-dark-logo\" class=\"my-5 text-center img img-responsive w-30 justify-content-center\"/>\n            </div>\n            <h2 class=\"text-center w-100\">{{ thanks_msg }}</h2>\n            <h3 class=\"text-center w-100\">Click to view your booking details: <a target=\"_blank\" href=\"{{ link }}\">Click Here</a></h3>\n            </div>\n            </div>\n            </div>\n            </div>\n            </div>', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'App\\Mail\\PasswordReset', 'Reset Password Notification', '<p><strong>Hello!</strong></p><p>You are receiving this email because we received a password reset request for your account. To proceed with the password reset please click on the link below:</p><p><a href=\"{{url}}\">Reset Password</a></p><p>This password reset link will expire in 60 minutes.&nbsp;<br>If you did not request a password reset, no further action is required.</p>', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(5, 'App\\Mail\\ConatctMail', 'New Enquiry Details.', '<p><strong>Name : {{name}}</strong></p>\n\n            <p><strong>Email : </strong><strong>{{email}}</strong></p>\n\n            <p><strong>Contact No : {{ contact_no }}&nbsp;</strong></p>\n\n            <p><strong>Message :&nbsp;</strong><strong>{{ message }}</strong></p>', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `meeting_polls`
--

CREATE TABLE `meeting_polls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vote` datetime DEFAULT NULL,
  `poll_id` bigint(20) NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2017_08_24_000000_create_settings_table', 1),
(4, '2018_10_10_000000_create_mail_templates_table', 1),
(5, '2019_08_19_000000_create_failed_jobs_table', 1),
(6, '2019_09_22_192348_create_messages_table', 1),
(7, '2019_10_16_211433_create_favorites_table', 1),
(8, '2019_10_18_223259_add_avatar_to_users', 1),
(9, '2019_10_20_211056_add_extra_fields_to_users', 1),
(10, '2019_10_20_211056_add_messenger_color_to_users', 1),
(11, '2019_10_22_000539_add_dark_mode_to_users', 1),
(12, '2019_10_25_214038_add_active_status_to_users', 1),
(13, '2020_08_22_121757_create_forms_table', 1),
(14, '2020_08_22_121758_create_form_values_table', 1),
(15, '2021_03_05_112733_create_modules_table', 1),
(16, '2021_03_10_032138_add_coloumn_module_table', 1),
(17, '2021_06_16_083454_create_login_securities_table', 1),
(18, '2021_06_16_115243_create_permission_tables', 1),
(19, '2021_08_10_060033_create_user_form_table', 1),
(20, '2021_08_25_050952_add_lang_field_in_users_table', 1),
(21, '2021_08_8_032138_add_coloumn_form_table', 1),
(22, '2021_09_21_060524_add_client_msg__to_forms__table', 1),
(23, '2021_10_14_085757_amount_to_forms_table', 1),
(24, '2021_10_14_085944_amount_to_form_values_table', 1),
(25, '2021_10_19_041655_add_payment_status_to_forms_table', 1),
(26, '2022_02_07_070446_add_payment_to_forms_table', 1),
(27, '2022_02_07_114611_add_payment_type_to_form_values_table', 1),
(28, '2022_02_21_032724_create_social_logins_table', 1),
(29, '2022_05_19_043539_social_type', 1),
(30, '2022_08_26_120030_add_status_to_form_values_table', 1),
(31, '2022_09_14_044629_create_polls_table', 1),
(32, '2022_09_23_065225_create_multiple_choices_table', 1),
(33, '2022_09_23_065251_create_meeting_polls_table', 1),
(34, '2022_09_23_065324_create_image_polls_table', 1),
(35, '2022_09_29_055159_add_forms_cc', 1),
(36, '2022_10_04_063224_create_comments_table', 1),
(37, '2022_10_04_063242_create_comments_replies_table', 1),
(38, '2022_10_13_102234_create_form_comments_controllers_table', 1),
(39, '2022_10_13_121737_create_form_comments_replies_table', 1),
(40, '2022_10_13_121754_create_form_comments_table', 1),
(41, '2022_10_14_051557_allow_section', 1),
(42, '2022_11_29_065355_create_dashboard_widgets_table', 1),
(43, '2023_01_17_072809_create_faqs_table', 1),
(44, '2023_01_17_103524_add_assign_type_to_forms_table', 1),
(45, '2023_02_06_115445_create_sms_templates_table', 1),
(46, '2023_02_06_115716_create_user_codes_table', 1),
(47, '2023_02_06_120602_add_country_code_to_users_table', 1),
(48, '2023_02_24_054509_create_assign_forms_users_table', 1),
(49, '2023_02_24_061824_create_assign_forms_roles_table', 1),
(50, '2023_03_20_065028_create_document_genrators_table', 1),
(51, '2023_03_20_095301_create_document_menus_table', 1),
(52, '2023_05_13_041437_create_events_table', 1),
(53, '2023_05_17_113006_create_testimonials_table', 1),
(54, '2023_05_18_104654_add_designation_testimonials_table', 1),
(55, '2023_05_22_131647_create_notifications_settings_table', 1),
(56, '2023_05_23_092628_create_notifications_table', 1),
(57, '2023_06_09_054119_add_set_end_date_to_forms_table', 1),
(58, '2023_06_22_084811_add_created_by_users_table', 1),
(59, '2023_07_17_061316_create_form_integration_settings_table', 1),
(60, '2023_07_27_050119_add_theme_to_forms_table', 1),
(61, '2023_08_09_065314_add_dark_to_users_table', 1),
(62, '2023_08_11_100159_create_bookings_table', 1),
(63, '2023_08_14_090154_create_time_wise_bookings_table', 1),
(64, '2023_08_14_104801_create_seat_wise_bookings_table', 1),
(65, '2023_08_17_093556_create_booking_values_table', 1),
(66, '2023_08_24_035759_create_blog_categories_table', 1),
(67, '2023_08_24_062508_create_blogs_table', 1),
(68, '2023_08_24_110040_add_description_to_forms_table', 1),
(69, '2023_08_29_040231_add_created_by_to_blogs_table', 1),
(70, '2023_09_01_063412_create_footer_settings_table', 1),
(71, '2023_09_01_064016_create_page_settings_table', 1),
(72, '2023_09_04_044855_create_header_settings_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modules`
--

CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modules`
--

INSERT INTO `modules` (`id`, `name`, `permission`, `created_at`, `updated_at`) VALUES
(1, 'dashboardwidget', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'user', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'role', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'form', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(5, 'submitted-form', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(6, 'booking', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(7, 'booking-calendar', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(8, 'submitted-booking', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(9, 'poll', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(10, 'document', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(11, 'blog', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(12, 'category', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(13, 'event', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(14, 'mailtemplate', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(15, 'sms-template', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(16, 'language', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(17, 'setting', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(18, 'chat', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(19, 'landing-page', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(20, 'testimonial', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(21, 'faqs', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(22, 'page-setting', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `multiple_choices`
--

CREATE TABLE `multiple_choices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vote` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poll_id` bigint(20) NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications_settings`
--

CREATE TABLE `notifications_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_notification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '1-On 0-Off',
  `sms_notification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '1-On 0-Off',
  `notify` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '1-On 0-Off',
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notifications_settings`
--

INSERT INTO `notifications_settings` (`id`, `title`, `email_notification`, `sms_notification`, `notify`, `status`, `created_at`, `updated_at`) VALUES
(1, 'testing purpose', '1', '0', '1', 2, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'new survey details', '2', '2', '1', 2, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'From Create', '2', '2', '0', 1, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'New Enquiry Details', '1', '2', '1', 1, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(5, 'Register mail', '1', '2', '1', 1, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(6, 'new booking survey details', '1', '2', '1', 1, '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `page_settings`
--

CREATE TABLE `page_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `friendly_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `page_settings`
--

INSERT INTO `page_settings` (`id`, `title`, `type`, `url_type`, `page_url`, `friendly_url`, `description`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'desc', NULL, NULL, NULL, 'At Prime Laravel Form Builder, we understand the importance of data privacy and security. That&#39;s why we offer robust privacy settings to ensure the protection of your sensitive information. Here&#39;s how our privacy settings work:\\r\\n\\r\\n\\r\\n\\r\\nData Encryption: We employ industry-standard encryption protocols to safeguard your data during transit and storage. Your form submissions and user information are encrypted, making it extremely difficult for unauthorized parties to access or tamper with the data.\\r\\n\\r\\n\\r\\nUser Consent Management: Our privacy settings include options for managing user consents. You can configure your forms to include consent checkboxes for users to agree to your data handling practices. This helps you ensure compliance with privacy regulations and builds trust with your users.\\r\\n\\r\\n\\r\\nData Retention Controls: Take control of how long you retain user data with our data retention settings. Define retention periods that align with your business needs or regulatory requirements. Once the specified retention period expires, the data is automatically and permanently deleted from our servers.\\r\\n\\r\\n\\r\\nAccess Controls: Grant specific access permissions to team members or clients based on their roles and responsibilities. With our access control settings, you can limit who can view, edit, or export form data, ensuring that only authorized individuals can access sensitive information.\\r\\n\\r\\n\\r\\nThird-Party Integrations: If you integrate third-party services or applications with Prime Laravel Form Builder, our privacy settings enable you to manage the data shared with these services. You have the flexibility to control which data is shared, providing an extra layer of privacy and control.', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'Our Team', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'Products', 'link', 'internal link', 'https://codecanyon.net/user/quebix-technology', 'https://codecanyon.net/user/quebix-technology', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'Contact', 'link', 'internal link', 'https://v1.ceysshn.com/contact/us', 'https://v1.ceysshn.com/contact/us', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(5, 'Feature', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(6, 'Pricing', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(7, 'Credit', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(8, 'News', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(9, 'iOS', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(10, 'Android', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(11, 'Microsoft', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(12, 'Desktop', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(13, 'Help', 'link', 'internal link', '#', '#', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(14, 'Terms', 'desc', NULL, NULL, NULL, 'Prime Laravel Form builder Terms and Conditions\n\n\n\n                Acceptance of Terms By accessing and using [Prime Laravel Form builder ] (the &quot;Service&quot;), you agree to be bound by these terms and conditions. If you do not agree with any part of these terms, please refrain from using the Service.\n\n\n                Intellectual Property Rights All content and materials provided on the Service are the property of [Prime Laravel Form builder - Saas]&nbsp;and protected by applicable intellectual property laws. You may not use, reproduce, distribute, or modify any content from the Service without prior written consent from [Prime Laravel Form builder ].\n\n\n                User Responsibilities a. You are solely responsible for any content you submit or upload on the Service. You agree not to post, transmit, or share any material that is unlawful, harmful, defamatory, obscene, or infringes upon the rights of others. b. You agree not to interfere with or disrupt the Service or its associated servers and networks. c. You are responsible for maintaining the confidentiality of your account information and agree to notify [Prime Laravel Form builder - Saas] immediately of any unauthorized use of your account.\n\n\n                Disclaimer of Warranties The Service is provided on an &quot;as-is&quot; and &quot;as available&quot; basis. [Prime Laravel Form builder ] makes no warranties, expressed or implied, regarding the accuracy, reliability, or availability of the Service. Your use of the Service is at your own risk.\n\n\n                Limitation of Liability In no event shall [Prime Laravel Form builder ] be liable for any direct, indirect, incidental, consequential, or punitive damages arising out of or in connection with the use of the Service. This includes but is not limited to any errors or omissions in the content, loss of data, or any other loss or damage.\n\n\n                Indemnification You agree to indemnify and hold&nbsp; harmless from any claims, damages, liabilities, or expenses arising out of your use of the Service, your violation of these terms and conditions, or your infringement of any rights of a third party.\n\n\n                Modification and Termination [Prime Laravel Form builder - Saas] reserves the right to modify or terminate the Service at any time, without prior notice. We also reserve the right to update these terms and conditions from time to time. It is your responsibility to review the most current version regularly.\n\n\n                Governing Law These terms and conditions shall be governed by and construed in accordance with the laws of India. Any disputes arising out of these terms shall be subject to the exclusive jurisdiction of the courts located in india.\n\n            ', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(15, 'FAQ', 'link', 'internal link', 'https://v1.ceysshn.com/all/faqs', 'https://v1.ceysshn.com/all/faqs', NULL, '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(16, 'Privacy', 'desc', NULL, NULL, NULL, '\n\n                Acceptance of Terms By accessing and using [Prime Laravel Form builder ] (the &quot;Service&quot;), you agree to be bound by these terms and conditions. If you do not agree with any part of these terms, please refrain from using the Service.\n\n\n                Intellectual Property Rights All content and materials provided on the Service are the property of [Prime Laravel Form builder - Saas]&nbsp;and protected by applicable intellectual property laws. You may not use, reproduce, distribute, or modify any content from the Service without prior written consent from [Prime Laravel Form builder ].\n\n\n                User Responsibilities a. You are solely responsible for any content you submit or upload on the Service. You agree not to post, transmit, or share any material that is unlawful, harmful, defamatory, obscene, or infringes upon the rights of others. b. You agree not to interfere with or disrupt the Service or its associated servers and networks. c. You are responsible for maintaining the confidentiality of your account information and agree to notify [Prime Laravel Form builder - Saas] immediately of any unauthorized use of your account.\n\n\n                Disclaimer of Warranties The Service is provided on an &quot;as-is&quot; and &quot;as available&quot; basis. [Prime Laravel Form builder ] makes no warranties, expressed or implied, regarding the accuracy, reliability, or availability of the Service. Your use of the Service is at your own risk.\n\n\n                Limitation of Liability In no event shall [Prime Laravel Form builder ] be liable for any direct, indirect, incidental, consequential, or punitive damages arising out of or in connection with the use of the Service. This includes but is not limited to any errors or omissions in the content, loss of data, or any other loss or damage.\n\n\n                Indemnification You agree to indemnify and hold&nbsp; harmless from any claims, damages, liabilities, or expenses arising out of your use of the Service, your violation of these terms and conditions, or your infringement of any rights of a third party.\n\n\n                Modification and Termination [Prime Laravel Form builder - Saas] reserves the right to modify or terminate the Service at any time, without prior notice. We also reserve the right to update these terms and conditions from time to time. It is your responsibility to review the most current version regularly.\n\n\n                Governing Law These terms and conditions shall be governed by and construed in accordance with the laws of India. Any disputes arising out of these terms shall be subject to the exclusive jurisdiction of the courts located in india.\n\n            ', '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage-dashboardwidget', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'create-dashboardwidget', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'edit-dashboardwidget', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'delete-dashboardwidget', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(5, 'export-dashboardwidget', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(6, 'manage-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(7, 'create-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(8, 'edit-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(9, 'delete-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(10, 'export-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(11, 'impersonate-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(12, 'phoneverified-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(13, 'emailverified-user', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(14, 'manage-role', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(15, 'create-role', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(16, 'edit-role', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(17, 'delete-role', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(18, 'export-role', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(19, 'manage-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(20, 'create-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(21, 'edit-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(22, 'delete-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(23, 'design-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(24, 'fill-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(25, 'duplicate-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(26, 'theme-setting-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(27, 'integration-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(28, 'payment-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(29, 'export-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(30, 'dashboard-qrcode-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(31, 'manage-submitted-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(32, 'edit-submitted-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(33, 'delete-submitted-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(34, 'download-submitted-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(35, 'export-submitted-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(36, 'show-submitted-form', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(37, 'manage-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(38, 'create-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(39, 'edit-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(40, 'delete-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(41, 'design-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(42, 'export-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(43, 'payment-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(44, 'fill-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(45, 'manage-booking-calendar', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(46, 'show-submitted-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(47, 'manage-submitted-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(48, 'edit-submitted-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(49, 'delete-submitted-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(50, 'export-submitted-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(51, 'copyurl-submitted-booking', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(52, 'manage-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(53, 'create-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(54, 'edit-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(55, 'delete-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(56, 'vote-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(57, 'result-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(58, 'export-poll', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(59, 'manage-document', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(60, 'create-document', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(61, 'edit-document', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(62, 'delete-document', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(63, 'document-generate-document', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(64, 'manage-blog', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(65, 'create-blog', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(66, 'edit-blog', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(67, 'delete-blog', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(68, 'show-blog', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(69, 'export-blog', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(70, 'manage-category', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(71, 'create-category', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(72, 'edit-category', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(73, 'delete-category', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(74, 'show-category', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(75, 'export-category', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(76, 'manage-event', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(77, 'create-event', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(78, 'edit-event', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(79, 'delete-event', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(80, 'manage-mailtemplate', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(81, 'edit-mailtemplate', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(82, 'export-mailtemplate', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(83, 'manage-sms-template', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(84, 'edit-sms-template', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(85, 'export-sms-template', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(86, 'create-language', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(87, 'manage-language', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(88, 'delete-language', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(89, 'manage-setting', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(90, 'manage-chat', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(91, 'manage-landing-page', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(92, 'manage-testimonial', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(93, 'create-testimonial', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(94, 'edit-testimonial', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(95, 'delete-testimonial', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(96, 'export-testimonial', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(97, 'manage-faqs', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(98, 'create-faqs', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(99, 'edit-faqs', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(100, 'delete-faqs', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(101, 'export-faqs', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(102, 'manage-page-setting', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(103, 'create-page-setting', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(104, 'edit-page-setting', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(105, 'delete-page-setting', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(106, 'export-page-setting', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polls`
--

CREATE TABLE `polls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voting_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multiple_answer_options` text COLLATE utf8mb4_unicode_ci,
  `require_participants_names` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voting_restrictions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `set_end_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hide_participants_from_each_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `set_end_date_time` datetime DEFAULT NULL,
  `allow_comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `results_visibility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_answer_options` text COLLATE utf8mb4_unicode_ci,
  `image_require_participants_names` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_voting_restrictions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_set_end_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_set_end_date_time` datetime DEFAULT NULL,
  `image_allow_comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_hide_participants_from_each_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_results_visibility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_answer_options` text COLLATE utf8mb4_unicode_ci,
  `meeting_fixed_time_zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meetings_fixed_time_zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limit_selection_to_one_option_only` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_set_end_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_set_end_date_time` datetime DEFAULT NULL,
  `meeting_allow_comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_hide_participants_from_each_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'User', 'web', '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seat_wise_bookings`
--

CREATE TABLE `seat_wise_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `seat_booking_json` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `services` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `week_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_time_status` tinyint(1) NOT NULL DEFAULT '0',
  `session_time_json` longtext COLLATE utf8mb4_unicode_ci,
  `limit_time_status` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `rolling_days_status` tinyint(1) NOT NULL DEFAULT '0',
  `rolling_days` bigint(20) DEFAULT NULL,
  `maximum_limit_status` tinyint(1) NOT NULL DEFAULT '0',
  `maximum_limit` bigint(20) DEFAULT NULL,
  `multiple_booking` tinyint(1) NOT NULL DEFAULT '0',
  `email_notification` tinyint(1) NOT NULL DEFAULT '0',
  `time_zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enable_note` tinyint(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(1, 'app_name', 'H3 Honduras Heroes'),
(2, 'app_logo', 'appLogo/app-logo.png'),
(3, 'app_small_logo', 'appLogo/app-small-logo.png'),
(4, 'favicon_logo', 'appLogo/app-favicon-logo.png'),
(5, 'default_language', 'es'),
(6, 'color', 'theme-6'),
(7, 'app_dark_logo', 'appLogo/app-dark-logo.png'),
(8, 'storage_type', 'local'),
(9, 'date_format', 'd-M-y'),
(10, 'time_format', 'g:i A'),
(11, 'roles', 'User'),
(12, 'google_calendar_enable', 'off'),
(13, 'captcha_enable', 'off'),
(14, 'transparent_layout', 'on'),
(15, 'dark_mode', NULL),
(16, 'meta_image', 'seo_image/meta_image.jpg'),
(17, 'document_theme1', 'document_theme/Stisla.png'),
(18, 'document_theme2', 'document_theme/Editor.png'),
(19, 'app_setting_status', 'on'),
(20, 'menu_setting_status', 'on'),
(21, 'feature_setting_status', 'on'),
(22, 'faq_setting_status', 'on'),
(23, 'testimonial_setting_status', 'on'),
(24, 'sidefeature_setting_status', 'on'),
(25, 'landing_page', '0'),
(26, 'apps_setting_enable', 'on'),
(27, 'apps_name', 'Prime Laravel'),
(28, 'apps_bold_name', 'Form Builder'),
(29, 'app_detail', 'Prime Laravel Form Builder is software for creating automated systems, you can create your own forms without writing a line of code. you have only to use the Drag & Drop to build your form and start using it.'),
(30, 'apps_image', 'DummyData/app.png'),
(31, 'apps_multiple_image_setting', '[{\"apps_multiple_image\":\"DummyData/1.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/2.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/3.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/4.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/5.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/6.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/7.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/8.png\"},\n                                                                  {\"apps_multiple_image\":\"DummyData/9.png\"}]'),
(32, 'feature_setting_enable', 'on'),
(33, 'feature_name', 'Prime Laravel Form Builder'),
(34, 'feature_bold_name', 'Features'),
(35, 'feature_detail', 'Prime Laravel Form Builder The features of Prime make it one of the most flexible systems for optimal inventory management. Features such as godown management, multiple stock valuation, manufacturing, batch and expiry date, job costing, etc., and powerful inventory reports make inventory management a cakewalk.'),
(36, 'feature_setting', '[\n                                                        {\"feature_image\":\"DummyData/active.svg\",\"feature_name\":\"Email Notification\",\"feature_bold_name\":\"On From Submit\",\"feature_detail\":\"You can send a notification email to someone in your organization when a contact submits a form. You can use this type of form processing step so that...\"},\n                                                        {\"feature_image\":\"DummyData/security.svg\",\"feature_name\":\"Two Factor\",\"feature_bold_name\":\"Authentication\",\"feature_detail\":\"Security is our priority. With our robust two-factor authentication (2FA) feature, you can add an extra layer of protection to your Prime Laravel Form\"},\n                                                        {\"feature_image\":\"DummyData/secretary.svg\",\"feature_name\":\"Multi Users With\",\"feature_bold_name\":\"Role & permission\",\"feature_detail\":\"Assign roles and permissions to different users based on their responsibilities and requirements. Admins can manage user accounts, define access level\"},\n                                                        {\"feature_image\":\"DummyData/documents.svg\",\"feature_name\":\"Document builder\",\"feature_bold_name\":\"Easy and fast\",\"feature_detail\":\"Template Library: Offer a selection of pre-designed templates for various document types (e.g., contracts, reports).Template Creation: Allow users to create custom templates with placeholders for dynamic content.\\r\\n\\r\\nTemplate Library: Offer a selection of pre-designed templates for various document types (e.g., contracts, reports).Template Creation: Allow users to create custom templates with placeholders for dynamic content.\"}]'),
(37, 'menu_setting_section1_enable', 'on'),
(38, 'menu_image_section1', 'DummyData/menusection1.png'),
(39, 'menu_name_section1', 'Form Builder'),
(40, 'menu_bold_name_section1', 'With Drag & Drop Dashboard Widgets'),
(41, 'menu_detail_section1', 'Creating beautiful dashboards has never been easier. Our drag-and-drop interface lets you effortlessly arrange and resize widgets, allowing you to design dynamic and interactive dashboards without any coding.'),
(42, 'menu_setting_section2_enable', 'on'),
(43, 'menu_image_section2', 'DummyData/menusection12.png'),
(44, 'menu_name_section2', 'Multi builders'),
(45, 'menu_bold_name_section2', 'Poll Management & Document Generator & Booking System'),
(46, 'menu_detail_section2', 'you can create customized surveys with ease. From multiple choice questionss to rating scales, our drag-and-drop builder lets you construct your polls in minutes, saving you valuable time and effort.'),
(47, 'menu_setting_section3_enable', 'on'),
(48, 'menu_image_section3', 'DummyData/setting.png'),
(49, 'menu_name_section3', 'Setting Features With'),
(50, 'menu_bold_name_section3', 'Multiple Section settings'),
(51, 'menu_detail_section3', 'A settings page is a crucial component of many digital products, allowing users to customize their experience according to their preferences. Designing a settings page with dynamic data enhances user satisfaction and engagement. Here s a guide on how to create such a page.'),
(52, 'business_growth_setting_enable', 'on'),
(53, 'business_growth_front_image', 'DummyData/10.png'),
(54, 'business_growth_video', 'DummyData/Dashboard _ Prime Laravel Form Builder.mp4'),
(55, 'business_growth_name', 'Makes Quick'),
(56, 'business_growth_bold_name', 'Business Growth'),
(57, 'business_growth_detail', 'Offer unique products, services, or solutions that stand out in the market. Innovation and differentiation can attract customers and give you a competitive edge.'),
(58, 'business_growth_view_setting', '[{\"business_growth_view_name\":\"Positive Reviews\",\"business_growth_view_amount\":\"20 k+\"},{\"business_growth_view_name\":\"Total Sales\",\"business_growth_view_amount\":\"300 +\"},{\"business_growth_view_name\":\"Happy Users\",\"business_growth_view_amount\":\"100 k+\"}]'),
(59, 'business_growth_setting', '[{\"business_growth_title\":\"User Friendly\"},{\"business_growth_title\":\"Products Analytic\"},{\"business_growth_title\":\"Manufacturers\"},{\"business_growth_title\":\"Order Status Tracking\"},{\"business_growth_title\":\"Supply Chain\"},{\"business_growth_title\":\"Chatting Features\"},{\"business_growth_title\":\"Workflows\"},{\"business_growth_title\":\"Transformation\"},{\"business_growth_title\":\"Easy Payout\"},{\"business_growth_title\":\"Data Adjustment\"},{\"business_growth_title\":\"Order Status Tracking\"},{\"business_growth_title\":\"Store Swap Link\"},{\"business_growth_title\":\"Manufacturers\"},{\"business_growth_title\":\"Order Status Tracking\"}]'),
(60, 'form_setting_enable', 'on'),
(61, 'form_name', 'Survey Form'),
(62, 'form_detail', 'Prime Laravel Form Builder is software for creating automated systems, you can create your own forms without writing a line of code. you have only to use the Drag & Drop to build your form and start using it.'),
(63, 'faq_setting_enable', 'on'),
(64, 'faq_name', 'Frequently asked questionss'),
(65, 'blog_setting_enable', 'on'),
(66, 'blog_name', 'BLOGS'),
(67, 'blog_detail', 'Optimize your manufacturing business with Quebix, offering a seamless user interface for streamlined operations, one convenient platform.'),
(68, 'start_view_setting_enable', 'on'),
(69, 'start_view_name', 'Prime Laravel Form Builder'),
(70, 'start_view_detail', 'Prime Laravel Form Builder is software for creating automated systems, you can create your own forms without writing a line of code. you have only to use the Drag & Drop to build your form and start using it.'),
(71, 'start_view_link_name', 'Contact US'),
(72, 'start_view_link', 'https://quebixtechnology.com/contact_us'),
(73, 'start_view_image', 'DummyData//11.png'),
(74, 'footer_setting_enable', 'on'),
(75, 'footer_description', 'in this product, we provide you, with form builder & poll management, Multi users & permissions, email notification, event calender with ( google Calendar) And Document Generator.'),
(76, 'rtl', '0'),
(77, '2fa', '0'),
(78, 'register', '0'),
(79, 'gtag', ''),
(80, 'email_verification', '0'),
(81, 'sms_verification', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_templates`
--

CREATE TABLE `sms_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` text COLLATE utf8mb4_unicode_ci,
  `variables` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sms_templates`
--

INSERT INTO `sms_templates` (`id`, `event`, `template`, `variables`, `created_at`, `updated_at`) VALUES
(1, 'verification code sms', 'Hello :name, Your verification code is :code', 'name,code', '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `social_logins`
--

CREATE TABLE `social_logins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `social_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` double(10,1) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `title`, `desc`, `designation`, `image`, `rating`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Fex', 'Customer Support Specialist', 'As a Customer Support Specialist for Prime-Laravel-Form-Builder, I have had the incredible opportunity to assist our valued customers in their journey of utilizing this revolutionary form-building solution.', 'Support Specialist', 'DummyData/13.png', 5.0, '1', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(2, 'Johnsi', 'A Journey of Growth and Transformation', 'As the Lead Developer for Prime-Laravel-Form-Builder, I have had the privilege of being at the forefront of developing a cutting-edge product that revolutionizes form-building.', 'Lead Developer', 'DummyData/14.png', 5.0, '1', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(3, 'Fex Ilizania', 'Customer Support Specialist', 'As a Customer Support Specialist for Prime-Laravel-Form-Builder, I have had the incredible opportunity to assist our valued customers in their journey of utilizing this revolutionary form-building solution.', 'Support Specialist', 'DummyData/15.png', 5.0, '1', '2023-10-08 06:25:58', '2023-10-08 06:25:58'),
(4, 'John', 'A Remarkable Journey of Collaboration and Success', 'As a Project Manager, my primary responsibility has been to ensure that projects are delivered on time, within budget. I have had the opportunity to work closely with cross-functional teams, marketers, and stakeholders, initiation to completion.', 'Project Manager', 'DummyData/16.png', 5.0, '1', '2023-10-08 06:25:58', '2023-10-08 06:25:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `time_wise_bookings`
--

CREATE TABLE `time_wise_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `slot_duration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slot_duration_minutes` bigint(20) NOT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `services` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `week_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intervals_time_status` tinyint(1) NOT NULL DEFAULT '0',
  `intervals_time_json` longtext COLLATE utf8mb4_unicode_ci,
  `limit_time_status` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `rolling_days_status` tinyint(1) NOT NULL DEFAULT '0',
  `rolling_days` bigint(20) DEFAULT NULL,
  `maximum_limit_status` tinyint(1) NOT NULL DEFAULT '0',
  `maximum_limit` bigint(20) DEFAULT NULL,
  `multiple_booking` tinyint(1) NOT NULL DEFAULT '0',
  `email_notification` tinyint(1) NOT NULL DEFAULT '0',
  `time_zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enable_note` tinyint(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'avatar/avatar.png',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'India',
  `messenger_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2180f3',
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `dark_layout` tinyint(1) NOT NULL DEFAULT '0',
  `rtl_layout` tinyint(1) NOT NULL DEFAULT '0',
  `transprent_layout` tinyint(1) NOT NULL DEFAULT '1',
  `theme_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'theme-2',
  `users_grid_view` tinyint(1) NOT NULL DEFAULT '0',
  `forms_grid_view` tinyint(1) NOT NULL DEFAULT '0',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dark_mode` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `type`, `email_verified_at`, `password`, `remember_token`, `avatar`, `address`, `country`, `messenger_color`, `active_status`, `country_code`, `phone_verified_at`, `dark_layout`, `rtl_layout`, `transprent_layout`, `theme_color`, `users_grid_view`, `forms_grid_view`, `phone`, `dark_mode`, `lang`, `social_type`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'team@ceysshn.com', 'Admin', '2023-10-08 06:25:58', '$2y$10$2lUe8C.WKSPnf.ueiQAhQ.lt7KMSmvAjdtQT6P6YkbcdbZ4RozJN6', NULL, 'avatar/avatar.png', NULL, 'India', '#2180f3', 1, NULL, '2023-10-08 06:25:58', 0, 0, 1, 'theme-6', 0, 0, NULL, 0, 'es', NULL, NULL, '2023-10-08 06:25:58', '2023-10-08 06:51:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_codes`
--

CREATE TABLE `user_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_forms`
--

CREATE TABLE `user_forms` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `assign_forms_roles`
--
ALTER TABLE `assign_forms_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assign_forms_roles_form_id_index` (`form_id`),
  ADD KEY `assign_forms_roles_role_id_index` (`role_id`);

--
-- Indices de la tabla `assign_forms_users`
--
ALTER TABLE `assign_forms_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assign_forms_users_form_id_index` (`form_id`),
  ADD KEY `assign_forms_users_user_id_index` (`user_id`);

--
-- Indices de la tabla `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `booking_values`
--
ALTER TABLE `booking_values`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ch_favorites`
--
ALTER TABLE `ch_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ch_messages`
--
ALTER TABLE `ch_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comments_replies`
--
ALTER TABLE `comments_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dashboard_widgets`
--
ALTER TABLE `dashboard_widgets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `document_genrators`
--
ALTER TABLE `document_genrators`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `document_menus`
--
ALTER TABLE `document_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `footer_settings`
--
ALTER TABLE `footer_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `form_comments`
--
ALTER TABLE `form_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `form_comments_controllers`
--
ALTER TABLE `form_comments_controllers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `form_comments_replies`
--
ALTER TABLE `form_comments_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `form_integration_settings`
--
ALTER TABLE `form_integration_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `form_values`
--
ALTER TABLE `form_values`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `header_settings`
--
ALTER TABLE `header_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `image_polls`
--
ALTER TABLE `image_polls`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_securities`
--
ALTER TABLE `login_securities`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mail_templates`
--
ALTER TABLE `mail_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `meeting_polls`
--
ALTER TABLE `meeting_polls`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `multiple_choices`
--
ALTER TABLE `multiple_choices`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indices de la tabla `notifications_settings`
--
ALTER TABLE `notifications_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `page_settings`
--
ALTER TABLE `page_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `seat_wise_bookings`
--
ALTER TABLE `seat_wise_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_key_index` (`key`);

--
-- Indices de la tabla `sms_templates`
--
ALTER TABLE `sms_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `social_logins`
--
ALTER TABLE `social_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `time_wise_bookings`
--
ALTER TABLE `time_wise_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `user_codes`
--
ALTER TABLE `user_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user_forms`
--
ALTER TABLE `user_forms`
  ADD KEY `user_forms_role_id_index` (`role_id`),
  ADD KEY `user_forms_form_id_index` (`form_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `assign_forms_roles`
--
ALTER TABLE `assign_forms_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `assign_forms_users`
--
ALTER TABLE `assign_forms_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `booking_values`
--
ALTER TABLE `booking_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comments_replies`
--
ALTER TABLE `comments_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dashboard_widgets`
--
ALTER TABLE `dashboard_widgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `document_genrators`
--
ALTER TABLE `document_genrators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `document_menus`
--
ALTER TABLE `document_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `footer_settings`
--
ALTER TABLE `footer_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `form_comments`
--
ALTER TABLE `form_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `form_comments_controllers`
--
ALTER TABLE `form_comments_controllers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `form_comments_replies`
--
ALTER TABLE `form_comments_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `form_integration_settings`
--
ALTER TABLE `form_integration_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `form_values`
--
ALTER TABLE `form_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `header_settings`
--
ALTER TABLE `header_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `image_polls`
--
ALTER TABLE `image_polls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `login_securities`
--
ALTER TABLE `login_securities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mail_templates`
--
ALTER TABLE `mail_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `meeting_polls`
--
ALTER TABLE `meeting_polls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `multiple_choices`
--
ALTER TABLE `multiple_choices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications_settings`
--
ALTER TABLE `notifications_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `page_settings`
--
ALTER TABLE `page_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de la tabla `polls`
--
ALTER TABLE `polls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `seat_wise_bookings`
--
ALTER TABLE `seat_wise_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `sms_templates`
--
ALTER TABLE `sms_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `social_logins`
--
ALTER TABLE `social_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `time_wise_bookings`
--
ALTER TABLE `time_wise_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user_codes`
--
ALTER TABLE `user_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
