-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 14, 2025 at 04:45 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_chatbot_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_details`
--

DROP TABLE IF EXISTS `business_details`;
CREATE TABLE IF NOT EXISTS `business_details` (
  `id` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `restaurant_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_details`
--

INSERT INTO `business_details` (`id`, `restaurant_name`, `address`, `phone`, `email`, `open_time`, `close_time`, `created_at`, `updated_at`) VALUES
(1, 'Fork Restaurant', 'Garden Colony, Ward No. 1, Vehari, P.O. Box Same, Tehsil Mailsi, District Vehari', '03222221847', 'Hafsa@outlook.com', '09:00:00', '22:00:00', '2025-06-07 03:04:35', '2025-06-07 03:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_logs`
--

DROP TABLE IF EXISTS `chatbot_logs`;
CREATE TABLE IF NOT EXISTS `chatbot_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `intent_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `user_input` text COLLATE utf8mb4_general_ci NOT NULL,
  `bot_response` text COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_chatbot_logs_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_logs`
--

INSERT INTO `chatbot_logs` (`id`, `user_id`, `session_id`, `intent_name`, `user_input`, `bot_response`, `timestamp`) VALUES
(1, NULL, 'dfMessenger-16833105', 'CancelReservation', 'make reservation', 'No reservation found with code: \"ABC123\"', '2025-06-09 04:58:17'),
(2, NULL, 'dfMessenger-61198870', 'MakeReservation', '03004212333', 'Reservation #806af130 confirmed for 5 guest(s) on Oct 6, 2025 at 12:00 PM. We\'ll contact you at 03004212333 if needed.', '2025-06-09 05:44:18'),
(3, NULL, 'dfMessenger-61198870', 'ModifyReservation', 'my reservation code is 806af130', 'No reservation found with code: \"123xyz\". Please check your code and try again.', '2025-06-09 05:45:44'),
(4, NULL, 'dfMessenger-61198870', 'CancelReservation', 'can you cancel my reservation please?', 'No reservation found with code: \"ABC123\"', '2025-06-09 05:50:50'),
(5, 1, '', '', '', 'Sorry, I didn\'t understand that intent.', '2025-06-10 01:02:43'),
(6, 1, '', '', '', 'Sorry, I didn\'t understand that intent.', '2025-06-10 01:03:01'),
(7, 1, '', '', '', 'Sorry, I didn\'t understand that intent.', '2025-06-10 01:03:04'),
(8, 1, '', '', '', 'Sorry, I didn\'t understand that intent.', '2025-06-10 01:03:14'),
(9, 1, '', '', '', 'Sorry, I didn\'t understand that intent.', '2025-06-10 01:03:26'),
(10, 1, '', '', '', 'Sorry, I didn\'t understand that intent.', '2025-06-10 01:04:02'),
(11, NULL, '', '', '', 'Sorry, I am unable to process the intent: \'\'.', '2025-06-10 04:16:45'),
(12, NULL, '', '', '', 'Sorry, I am unable to process the intent: \'\'.', '2025-06-10 04:17:27'),
(13, NULL, '', '', '', 'Sorry, I am unable to process the intent: \'\'.', '2025-06-10 04:17:29'),
(14, 1, 'user_1_i6cm0zh3k3s', 'MakeReservation', '03001111222', '✅ Reservation Confirmed! Your code is: res_68476d.\nWe have a table for 12 guest(s) on Jun 13, 2025 at 10:00 AM. We\'ll contact you at 03001111222 if needed.', '2025-06-10 04:27:39'),
(16, 1, 'user_1_2tuywb0q6e2', 'MakeReservation', '03004477888', '✅ Reservation Confirmed! Your code is: res_68476e.\nWe have a table for 7 guest(s) on Jun 18, 2025 at 11:00 AM. We\'ll contact you at 03004477888 if needed.', '2025-06-10 04:32:10'),
(17, 1, 'user_1_h94v9yl4lgk', 'CancelOrder', 'can you place an order?', 'No order found with ID #\"45\" or \"ORD123\" for your account.', '2025-06-10 04:35:22'),
(18, 1, 'user_1_h94v9yl4lgk', 'CancelOrder', 'track my order', 'No order found with ID #\"45\" or \"ORD123\" for your account.', '2025-06-10 04:35:38'),
(19, 1, 'user_1_1gfylclap1x', 'MakeReservation', '03003333333', '✅ Reservation #853F67 confirmed for 16 guest(s) on Jun 16, 2025 at 12:00 PM. We\'ll contact you at 03003333333 if needed.', '2025-06-10 04:37:12'),
(20, 1, 'user_1_1gfylclap1x', 'CancelOrder', 'place order', 'No order found with ID #\"45\" or \"ORD123\" for your account.', '2025-06-10 04:37:54'),
(21, NULL, '8bd09c24-da22-1bb5-820c-7250a7b96bfa', 'PlaceOrder', 'place order', 'Please log in to place an order.', '2025-06-10 04:38:36'),
(22, 1, 'user_1_1gfylclap1x', 'PlaceOrder', 'Place Order', 'Sorry, we don\'t have \'\"Margherita Pizza\"\' on our menu.', '2025-06-10 04:38:54'),
(23, 1, 'user_1_gownl4h4dtv', 'PlaceOrder', 'place order', 'Sorry, we don\'t have \'\"Margherita Pizza\"\' on our menu.', '2025-06-10 04:40:05'),
(24, NULL, '8bd09c24-da22-1bb5-820c-7250a7b96bfa', 'PlaceOrder', 'place order', 'Please log in to place an order.', '2025-06-10 04:42:19'),
(25, 1, 'user_1_7694db4g5wr', 'PlaceOrder', 'Crunchy Pizza', 'How many would you like to order?', '2025-06-10 04:56:01'),
(26, 1, 'user_1_5dpqubxnh2v', 'Order.Start', 'place order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:04:30'),
(27, 1, 'user_1_5dpqubxnh2v', 'Order.SelectCategory', 'pizza', 'I\'m sorry, which category did you want to see?', '2025-06-10 05:04:33'),
(28, 1, 'user_1_5dpqubxnh2v', 'Order.SelectCategory', 'Pizza', 'I\'m sorry, which category did you want to see?', '2025-06-10 05:04:50'),
(29, 1, 'user_1_5dpqubxnh2v', 'Order.SelectCategory', 'Pizzas', 'I\'m sorry, which category did you want to see?', '2025-06-10 05:04:54'),
(30, 1, 'user_1_5dpqubxnh2v', 'Order.SelectCategory', 'Crunchy Pizza', 'I\'m sorry, which category did you want to see?', '2025-06-10 05:05:23'),
(31, 1, 'user_1_9b4jo0s8hb9', 'Order.Start', 'place order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:18:57'),
(32, 1, 'user_1_9b4jo0s8hb9', 'Order.SelectCategory', 'Pizzas', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:19:09'),
(33, 1, 'user_1_dxu3hqubj7g', 'Order.Start', 'place order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:20:35'),
(34, 1, 'user_1_dxu3hqubj7g', 'Order.SelectCategory', 'Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:20:41'),
(35, 1, 'user_1_dxu3hqubj7g', 'Order.SelectCategory', 'Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:20:49'),
(36, 1, 'user_1_n4khcoayeq', 'Order.Start', 'place order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:26:44'),
(37, 1, 'user_1_n4khcoayeq', 'Order.SelectCategory', 'Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:26:47'),
(38, 1, 'user_1_n4khcoayeq', 'Order.SelectCategory', 'Crunchy Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:26:58'),
(39, 1, 'user_1_n4khcoayeq', 'Order.SelectCategory', 'Crunchy Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:27:11'),
(40, 1, 'user_1_a8d2d2zxjub', 'Order.Start', 'place order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:30:11'),
(41, 1, 'user_1_a8d2d2zxjub', 'Order.SelectCategory', 'Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:30:14'),
(42, 1, 'user_1_vo5z3ivjzka', 'Order.Start', 'place order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:37:57'),
(43, 1, 'user_1_vo5z3ivjzka', 'Order.SelectCategory', 'Pizza', 'Excellent choice! In our Pizza category, we have: Crunchy Pizza. What would you like?', '2025-06-10 05:38:00'),
(44, 1, 'user_1_vo5z3ivjzka', 'Order.SelectItem', '1 Crunchy Pizza', '✅ Order #4 placed: 1 x Crunchy Pizza. Your total is ₹1500. It\'ll be ready soon!', '2025-06-10 05:38:28'),
(45, 1, 'user_1_vo5z3ivjzka', 'Order.Start', 'track my order', 'Great! What would you like to order from? You can choose from: Pizza.', '2025-06-10 05:38:50'),
(46, 1, 'user_1_5kluzlyliju', 'RequestSupport', 'i need help', '✅ Support ticket #3 has been created. A member of our team will review it shortly. You can check its status by saying \'check ticket 3\'.', '2025-06-10 05:51:40'),
(47, 1, 'user_1_cr8gk3zjgbt', 'RequestSupport', 'My food was cold when I recieved it. it was not that okay to eat.', '✅ Support ticket #4 has been created. A member of our team will review it shortly. You can check its status by saying \'check ticket 4\'.', '2025-06-10 06:01:44'),
(48, 1, 'user_1_j53j6mvie1r', 'CheckSupportStatus', '5', '--- Ticket #5 ---\n\nYou (Jun 10, 6:07 am):\nWe are sorry to hear that. We have got noticed, and we assure you this will never happen again. thanks for contacting us.\n\n------------------------\nStatus: Closed', '2025-06-10 06:18:14'),
(49, 1, 'user_1_j53j6mvie1r', 'CheckSupportStatus', '5', '--- Ticket #5 ---\n\n\n\nYou (Jun 10, 6:07 am):\nWe are sorry to hear that. We have got noticed, and we assure you this will never happen again. thanks for contacting us.\n\n------------------------\nStatus: Closed', '2025-06-10 06:19:07'),
(50, 1, 'user_1_wyu5gzwelv', 'AskFAQ', 'are prices inclusive of tax', 'Here is what I found for \'Are your prices inclusive of tax?\':\n\nYes, all menu prices are inclusive of GST and applicable taxes.', '2025-06-12 05:29:44'),
(51, 1, 'user_1_wyu5gzwelv', 'AskFAQ', 'list, tell me if prices inclusive of tax', 'Here is what I found for \'Are your prices inclusive of tax?\':\n\nYes, all menu prices are inclusive of GST and applicable taxes.', '2025-06-12 05:30:03'),
(52, 1, 'user_1_wyu5gzwelv', 'AskFAQ', 'I have a question about delivery', 'Here is what I found for \'Do you offer home delivery in Pakistan?\':\n\nYes, we offer home delivery in most major cities across Pakistan including Lahore, Karachi, Islamabad, and Rawalpindi.', '2025-06-12 05:30:29'),
(53, 1, 'user_1_k4l0cm2tlb9', 'FAQ.ListAll', 'faq', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:42:02'),
(54, 1, 'user_1_c1lyj1uanl5', 'FAQ.ListAll', 'faq', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:46:06'),
(55, 1, 'user_1_r8k176sadrq', 'FAQ.ListAll', 'faq', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:47:19'),
(56, 1, 'user_1_v4qwcllm469', 'FAQ.ListAll', 'faq', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:48:08'),
(57, 1, 'user_1_v4qwcllm469', 'RequestSupport', 'help', '✅ Support ticket #6 has been created. A member of our team will review it shortly. You can check its status by saying \'check ticket 6\'.', '2025-06-12 05:50:30'),
(58, 1, 'user_1_v4qwcllm469', 'FAQ.ListAll', 'i need some information', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:50:45'),
(59, 1, 'user_1_1vgfzerh2ap', 'FAQ.ListAll', 'Can I ask a question', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:53:08'),
(60, 1, 'user_1_t6c5qkmxqp', 'FAQ.ListAll', 'I need some information', 'Here are some frequently asked questions. Please select one:', '2025-06-12 05:57:32'),
(61, 1, 'user_1_96z9aayqpe5', 'FAQ.ListAll', 'i have a question', 'Sorry, I am unable to process the intent: \'FAQ.ListAll\'.', '2025-06-12 06:00:42'),
(62, 1, 'user_1_b4ual9qlhxu', 'AskFAQ', 'what is your return policy', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:02:22'),
(63, 1, 'user_1_b4ual9qlhxu', 'Order.Start', 'Is your food halal certified?', 'No text response', '2025-06-12 06:02:49'),
(64, 1, 'user_1_b4ual9qlhxu', 'AskFAQ', 'Is your food halal certified?', 'Here is what I found for \'Is your food halal certified?\':\n\nAbsolutely. All our ingredients are 100% halal certified, sourced from trusted suppliers in Pakistan.', '2025-06-12 06:03:37'),
(65, 1, 'user_1_b4ual9qlhxu', 'AskFAQ', 'Is your food halal certified?', 'Absolutely. All our ingredients are 100% halal certified, sourced from trusted suppliers in Pakistan.', '2025-06-12 06:04:20'),
(66, 1, 'user_1_skj0eggm95s', 'AskFAQ', 'delivery charges?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:07:35'),
(67, 1, 'user_1_skj0eggm95s', 'AskFAQ', 'delivery charges', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:07:51'),
(68, 1, 'user_1_n4sogwm7f9r', 'AskFAQ', 'How do I track my order?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:08:02'),
(69, 1, 'user_1_n4sogwm7f9r', 'AskFAQ', 'Which cities do you deliver to?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:08:13'),
(70, 1, 'user_1_n4sogwm7f9r', 'AskFAQ', 'Is your food halal certified?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:08:37'),
(71, 1, 'user_1_k3x3svafae9', 'AskFAQ', 'Is your food halal certified?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:08:45'),
(72, 1, 'user_1_aq0utvsm47g', 'AskFAQ', 'Is your food halal certified?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:09:13'),
(73, 1, 'user_1_7urk2yidm8l', 'Order.Start', 'a minimum order?', 'No text response', '2025-06-12 06:09:41'),
(74, 1, 'user_1_4ww9oqvpvqm', 'AskFAQ', 'How do I track my order?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:11:11'),
(75, 1, 'user_1_wjiu3upikg', 'AskFAQ', 'How do I track my order?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:11:26'),
(76, 1, 'user_1_3vrqb4sd8v', 'CheckBusinessHours', 'opening hours?', 'No text response', '2025-06-12 06:11:46'),
(77, 1, 'user_1_qtt24d7vx2', 'AskFAQ', 'Do you have a mobile app?', 'I\'m sorry, I couldn\'t find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?', '2025-06-12 06:13:31'),
(78, 1, 'user_1_d1erqo4y36j', 'AskFAQ', 'Is your food halal certified?', 'Here is what I found for \'Is your food halal certified?\':\n\nAbsolutely. All our ingredients are 100% halal certified, sourced from trusted suppliers in Pakistan.', '2025-06-12 06:17:46'),
(79, 1, 'user_1_d1erqo4y36j', 'AskFAQ', 'Can I place an order via WhatsApp?', 'Yes, you can place orders via our official WhatsApp number: +92-3XX-XXXXXXX.', '2025-06-12 06:18:14'),
(80, 1, 'user_1_d1erqo4y36j', 'AskFAQ', 'Can I place an order via WhatsApp?', 'Yes, you can place orders via our official WhatsApp number: +92-300-1212333.', '2025-06-12 06:18:40'),
(81, 1, 'user_1_d1erqo4y36j', 'AskFAQ', 'whatsapp?', 'Yes, you can place orders via our official WhatsApp number: +92-300-1212333.', '2025-06-12 06:18:45'),
(82, 1, 'user_1_d1erqo4y36j', 'Order.Start', 'What should I do if I face an issue with my order?', 'No text response', '2025-06-12 06:18:59'),
(83, 1, 'user_1_d1erqo4y36j', 'Order.Start', 'What should I do if I face an issue with my order?', 'No text response', '2025-06-12 06:19:52'),
(84, 1, 'user_1_d1erqo4y36j', 'AskFAQ', 'whatsapp?', 'Yes, you can place orders via our official WhatsApp number: +92-300-1212333.', '2025-06-12 06:19:59'),
(85, NULL, 'guest_8fj9yjq4h4', 'AskFAQ', 'table reservation', 'Yes, you can call our helpline or use our website/app to reserve a table.', '2025-06-12 06:27:12'),
(86, NULL, 'guest_8fj9yjq4h4', 'AskFAQ', 'please make the table reservation for me', 'Yes, you can call our helpline or use our website/app to reserve a table.', '2025-06-12 06:27:30'),
(87, NULL, 'guest_8fj9yjq4h4', 'AskFAQ', 'do reserve table please', 'Yes, you can call our helpline or use our website/app to reserve a table.', '2025-06-12 06:27:43'),
(88, NULL, 'guest_kn6xnnvreu', 'AskFAQ', 'table reservation', 'Yes, you can call our helpline or use our website/app to reserve a table.', '2025-06-12 06:29:30'),
(89, NULL, 'guest_5t3ucbwfn4d', 'MakeReservation', 'book table please', 'I can certainly help with that, but you\'ll need to be logged in first. Please log in to your account, and I\'ll be ready to assist!', '2025-06-12 06:48:00'),
(90, 1, 'user_1_oy0vid54exf', 'MakeReservation', 'book table please', 'Of course! What date would you like to book?', '2025-06-12 06:48:32'),
(91, 1, 'user_1_zyh8cv6hbv', 'MakeReservation', 'book table', 'Of course! What date would you like to book?', '2025-06-12 06:59:19'),
(92, 1, 'user_1_czmmh98m6u', 'CancelReservation', 'reserve table', 'Your reservation has been cancelled.', '2025-06-12 07:03:17'),
(93, 1, 'user_1_55s9lhsiunv', 'CancelReservation', 'book table', 'Your reservation has been cancelled.', '2025-06-12 07:09:57'),
(94, 1, 'user_1_55s9lhsiunv', 'CancelReservation', 'make reservation', 'Your reservation has been cancelled.', '2025-06-12 07:10:07'),
(95, 1, 'user_1_0v2xd2juff4a', 'CancelReservation', 'please book a table', 'Your reservation has been cancelled.', '2025-06-12 07:10:57'),
(96, 1, 'user_1_w70sbc2crzo', 'CancelReservation', 'please book a table', 'Your reservation has been cancelled.', '2025-06-12 07:11:11'),
(97, 1, 'user_1_ww998u74gxe', 'CancelReservation', 'reservation', 'Your reservation has been cancelled.', '2025-06-12 07:11:51'),
(98, 1, 'user_1_qd6fazrslg', 'CancelReservation', 'please make a table reservation', 'Your reservation has been cancelled.', '2025-06-12 07:12:29'),
(99, 1, 'user_1_mydcsj6b3s', 'CancelReservation', 'book a table', 'Your reservation has been cancelled.', '2025-06-12 07:13:58'),
(100, 1, 'user_1_hs68ka538e', 'CancelReservation', 'I\'d like to book a table for tomorrow', 'I could not find an active reservation with the code \'Array\' on your account. Please double-check the code.', '2025-06-12 07:26:15'),
(101, 1, 'user_1_hs68ka538e', 'CancelReservation', 'book table please', 'I could not find an active reservation with the code \'Array\' on your account. Please double-check the code.', '2025-06-12 07:26:21'),
(102, 1, 'user_1_r1p1gggo8c', 'CancelReservation', 'book table please', 'Your reservation has been cancelled.', '2025-06-12 07:26:43'),
(103, 1, 'user_1_r1p1gggo8c', 'CancelReservation', 'book table please', 'Your reservation has been cancelled.', '2025-06-12 07:26:53'),
(104, 1, 'user_1_r1p1gggo8c', 'CancelReservation', 'book table please', 'No text response', '2025-06-12 07:27:01'),
(105, 1, 'user_1_ehfv7u08hb8', 'MakeReservation', '03001111111', '✅ All set! Your reservation for 6 guests on June 15, 2025 at 2:00 PM is confirmed. Your reservation code is c83b43a8b1add7f9a5942476b2e87fe1. We look forward to seeing you!', '2025-06-12 07:38:34'),
(106, NULL, 'guest_unm3p9ptuh', 'MakeReservation', '03001122333', 'For this action, you need to be logged in. Please log in to your account, and I\'ll be happy to assist you further.', '2025-06-12 07:52:38'),
(107, NULL, 'guest_t0fyfve967d', 'MakeReservation.Start', 'book a table', 'To make a reservation, you need to be logged in first. Please log in to your account and I can help you book a table.', '2025-06-12 08:03:16'),
(108, 1, 'user_1_f3989jta834', 'MakeReservation.Start', 'book a table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-12 08:04:31'),
(109, NULL, 'guest_09q6qodbncw3', 'MakeReservation.Start', 'book a table', 'To make a reservation, you need to be logged in first. Please log in to your account and I can help you book a table.', '2025-06-12 08:05:31'),
(110, NULL, 'guest_kyt9ap0iz7', 'MakeReservation.Start', 'book a table', 'To make a reservation, you need to be logged in first. Please log in to your account and I can help you book a table.', '2025-06-12 08:15:04'),
(111, 1, 'user_1_cmklzw7hymk', 'MakeReservation.Start', 'book a table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-12 08:15:26'),
(112, 1, 'user_1_igkchbns6ar', 'MakeReservation.Start', 'book a table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-12 08:18:40'),
(113, 1, 'user_1_qz0veoh6l9s', 'MakeReservation.Start', 'book a table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-12 08:31:06'),
(114, NULL, 'guest_gt90uzj4a8', 'MakeReservation.Start', 'please reserve a table', 'To make a reservation, you need to be logged in first. Please log in to your account and I can help you book a table.', '2025-06-13 02:15:02'),
(115, 1, 'user_1_jp5mtwkui0c', 'MakeReservation.Start', 'please book a table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 02:15:41'),
(116, 1, 'user_1_4g8idw8n6ut', 'MakeReservation.Start', 'please make reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 02:21:50'),
(117, 1, 'user_1_n5ygh0w84g', 'MakeReservation.Start', 'please make reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 02:27:56'),
(118, 1, 'user_1_n5ygh0w84g', 'MakeReservation.CollectDetails', '18 june', 'Got it. And what time would you like to book for?', '2025-06-13 02:28:02'),
(119, 1, 'user_1_n5ygh0w84g', 'MakeReservation.CollectDetails', '2pm', 'Got it. And what time would you like to book for?', '2025-06-13 02:28:13'),
(120, 1, 'user_1_n5ygh0w84g', 'MakeReservation.CollectDetails', '8pm', 'Got it. And what time would you like to book for?', '2025-06-13 02:28:22'),
(121, 1, 'user_1_43ama28j33e', 'MakeReservation.Start', 'make table reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 02:30:48'),
(122, 1, 'user_1_43ama28j33e', 'MakeReservation.CollectDetails', '18 june 2025', 'Got it. And what time would you like to book for?', '2025-06-13 02:30:56'),
(123, 1, 'user_1_43ama28j33e', 'MakeReservation.CollectDetails', '4 pm', 'Got it. And what time would you like to book for?', '2025-06-13 02:31:07'),
(124, 1, 'user_1_28xdxtmwrbg', 'MakeReservation.Start', 'please reserve a table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 02:35:01'),
(125, 1, 'user_1_28xdxtmwrbg', 'MakeReservation.Start', 'reserve table', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 02:35:06'),
(126, 1, 'user_1_5c8xlh3jed5', 'MakeReservation.Start', 'please make table reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 03:02:17'),
(127, 1, 'user_1_k2t3amfq4js', 'MakeReservation.Start', 'table reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 03:02:30'),
(128, 1, 'user_1_k2t3amfq4js', 'MakeReservation.CollectDetails', '18 june', 'Got it. And what time would you like to book for?', '2025-06-13 03:02:39'),
(129, 1, 'user_1_k2t3amfq4js', 'MakeReservation.CollectDetails', '04:00 pm', 'Got it. And what time would you like to book for?', '2025-06-13 03:02:49'),
(130, 1, 'user_1_dr9u6yy4mxu', 'MakeReservation.Start', 'table reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 03:04:07'),
(131, 1, 'user_1_dr9u6yy4mxu', 'MakeReservation.CollectDetails', '14 june', 'Got it. And what time would you like to book for?', '2025-06-13 03:04:19'),
(132, 1, 'user_1_dr9u6yy4mxu', 'MakeReservation.CollectDetails', '4pm', 'Perfect. How many guests will be in your party?', '2025-06-13 03:04:23'),
(133, 1, 'user_1_dr9u6yy4mxu', 'MakeReservation.CollectDetails', '3', 'And what is the occasion? (e.g., birthday, casual, anniversary)', '2025-06-13 03:04:31'),
(134, 1, 'user_1_dr9u6yy4mxu', 'MakeReservation.CollectDetails', 'birthday', 'Almost done! What is the best contact phone number for the reservation?', '2025-06-13 03:04:36'),
(135, 1, 'user_1_dr9u6yy4mxu', 'MakeReservation.CollectDetails', '03005588888', '✅ Excellent! Your reservation is confirmed. Your reservation code is **BD889019**. We look forward to seeing you on June 14th at 11:00 AM.', '2025-06-13 03:04:47'),
(136, NULL, 'guest_8qwwjf74tb', 'MakeReservation.Start', 'reserve a table please', 'To make a reservation, you need to be logged in first. Please log in to your account and I can help you book a table.', '2025-06-13 03:05:41'),
(137, NULL, 'guest_8qwwjf74tb', 'CheckLoginStatus', 'do it anyway without login please', 'Sorry, I am unable to process the intent: \'CheckLoginStatus\'.', '2025-06-13 03:05:59'),
(138, NULL, 'guest_8qwwjf74tb', 'RequestSupport', 'table reservation', 'For this action, you need to be logged in. Please log in to your account, and I\'ll be happy to assist you further.', '2025-06-13 03:06:23'),
(139, NULL, 'guest_8qwwjf74tb', 'AskFAQ', 'home delivery?', 'Yes, we offer home delivery in most major cities across Pakistan including Lahore, Karachi, Islamabad, and Rawalpindi.', '2025-06-13 03:06:34'),
(140, NULL, 'guest_8qwwjf74tb', 'AskFAQ', 'order at whatsapp?', 'Yes, you can place orders via our official WhatsApp number: +92-300-1212333.', '2025-06-13 03:06:50'),
(141, 2, 'user_2_94mgaad9ezl', 'Order.Start', 'check order status', 'No text response', '2025-06-13 03:11:23'),
(142, 2, 'user_2_7m7icws0zq8', 'Order.Start', 'place order', 'No text response', '2025-06-13 03:22:53'),
(143, 2, 'user_2_7m7icws0zq8', 'Order.Start', 'please place an order', 'No text response', '2025-06-13 03:23:02'),
(144, 2, 'user_2_t4oqm0b38ni', 'Order.Start', 'place order', 'I can help with that! What would you like to order from? Our categories are: Pizza.', '2025-06-13 04:01:15'),
(145, 2, 'user_2_t4oqm0b38ni', 'Order.SelectCategory', 'Pizza', 'In the Pizza category, we have: Crunchy Pizza ($1,500.00). What would you like?', '2025-06-13 04:01:21'),
(146, 2, 'user_2_jo8ef7y1cvt', 'MakeReservation.Start', 'table reservation', 'Great, I can help with that. For what date would you like to make a reservation?', '2025-06-13 04:02:02'),
(147, 2, 'user_2_jo8ef7y1cvt', 'MakeReservation.CollectDetails', '16 june', 'Got it. And what time would you like to book for?', '2025-06-13 04:02:07'),
(148, 2, 'user_2_jo8ef7y1cvt', 'MakeReservation.CollectDetails', '12pm', 'Perfect. How many guests will be in your party?', '2025-06-13 04:02:13'),
(149, 2, 'user_2_jo8ef7y1cvt', 'MakeReservation.CollectDetails', '3', 'And what is the occasion? (e.g., birthday, casual, anniversary)', '2025-06-13 04:02:18'),
(150, 2, 'user_2_jo8ef7y1cvt', 'MakeReservation.CollectDetails', 'Anniversary', 'Almost done! What is the best contact phone number for the reservation?', '2025-06-13 04:02:24'),
(151, 2, 'user_2_jo8ef7y1cvt', 'MakeReservation.CollectDetails', '03004477888', '✅ Excellent! Your reservation is confirmed. Your reservation code is **72FC9296**. We look forward to seeing you on June 16th at 7:00 AM.', '2025-06-13 04:02:29'),
(152, 2, 'user_2_1wl5v6a4jp8', 'Order.Start', 'place order', 'I can help with that! What would you like to order from? Our categories are: Pizza.', '2025-06-13 04:19:42'),
(153, 2, 'user_2_1wl5v6a4jp8', 'Order.SelectCategory', 'Pizza', 'In the Pizza category, we have: Crunchy Pizza ($1,500.00). What would you like?', '2025-06-13 04:19:47'),
(154, 2, 'user_2_1wl5v6a4jp8', 'Order.SelectItem', '2', '✅ Order placed! I\'ve added 2 x Crunchy Pizza to your order. Your order number is **ORD-5**. You can say \'cancel order ORD-5\' if you need to.', '2025-06-13 04:20:02'),
(155, 2, 'user_2_1wl5v6a4jp8', 'CancelOrder', 'cancel order', 'I couldn\'t find an active order with that number associated with your account. Please double-check the order number.', '2025-06-13 04:20:23'),
(156, 2, 'user_2_1wl5v6a4jp8', 'CancelOrder', 'cancel order 5', 'I couldn\'t find an active order with that number associated with your account. Please double-check the order number.', '2025-06-13 04:20:40'),
(157, 2, 'user_2_97cabder5a8', 'CancelOrder', '5', '✅ Your order **5** has been successfully cancelled.', '2025-06-13 04:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `answer` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'Do you offer home delivery in Pakistan?', 'Yes, we offer home delivery in most major cities across Pakistan including Lahore, Karachi, Islamabad, and Rawalpindi.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(2, 'Is your food halal certified?', 'Absolutely. All our ingredients are 100% halal certified, sourced from trusted suppliers in Pakistan.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(3, 'What are your restaurant’s opening hours?', 'We are open every day from 12:00 PM to 12:00 AM, including weekends and public holidays.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(4, 'Do you offer dine-in and takeaway services?', 'Yes, we provide both dine-in and takeaway services at all of our branches.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(5, 'Which cities do you deliver to?', 'We currently deliver to Lahore, Karachi, Islamabad, Faisalabad, Gujranwala, Multan, and Peshawar.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(6, 'What payment methods do you accept?', 'We accept Cash on Delivery (COD), JazzCash, Easypaisa, and credit/debit cards.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(7, 'Can I place an order via WhatsApp?', 'Yes, you can place orders via our official WhatsApp number: +92-300-1212333.', '2025-06-07 02:48:56', '2025-06-12 06:18:36'),
(8, 'Is there a minimum order amount for delivery?', 'Yes, the minimum order for delivery is Rs. 500.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(9, 'Do you have a mobile app?', 'Yes, our Android and iOS apps are available. Search for our restaurant name on Google Play or Apple App Store.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(10, 'Are there any delivery charges?', 'Delivery charges may vary based on location. Typically, it ranges from Rs. 100 to Rs. 200.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(11, 'How do I track my order?', 'Once your order is placed, you will receive an SMS with a tracking link. You can also track it via our app.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(12, 'Can I reserve a table in advance?', 'Yes, you can call our helpline or use our website/app to reserve a table.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(13, 'Do you offer catering for events?', 'Yes, we offer full-service catering for weddings, corporate events, and private parties.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(14, 'Is your staff trained in hygiene protocols?', 'Yes, all staff undergo regular training as per food safety standards set by the Punjab Food Authority.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(15, 'Are there any deals or discounts available?', 'We regularly run deals and discounts. Please check our website or social media for the latest offers.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(16, 'Do you provide vegetarian options?', 'Yes, we offer a variety of vegetarian dishes including salads, pastas, and desi items.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(17, 'Is smoking allowed in the restaurant?', 'Smoking is not allowed inside the restaurant, but there are designated smoking areas.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(18, 'Can I customize my order?', 'Yes, you can request customizations (e.g., less spicy, no onions) when placing the order.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(19, 'Are your prices inclusive of tax?', 'Yes, all menu prices are inclusive of GST and applicable taxes.', '2025-06-07 02:48:56', '2025-06-07 02:48:56'),
(20, 'What should I do if I face an issue with my order?', 'You can call our support team at 042-1234567 or email support@restaurant.pk for assistance.', '2025-06-07 02:48:56', '2025-06-07 02:48:56');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

DROP TABLE IF EXISTS `menu_categories`;
CREATE TABLE IF NOT EXISTS `menu_categories` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Pizza', 'We create delicious pizza and deliver it with speed in your area. Feel free to order from our pizza menu or take home', '2025-06-07 02:02:56', '2025-06-07 02:02:56');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_menu_items_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'Crunchy Pizza', 'Pizza Online Arifwala – Enjoy fresh, hot, and delicious pizzas delivered to your doorstep in Arifwala. Order now for quick and easy online delivery!', 1500.00, 'item_68435a57c83152.29573205.webp', '2025-06-07 02:15:03', '2025-06-07 02:15:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','confirmed','preparing','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_orders_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `status`, `total_amount`, `created_at`, `updated_at`) VALUES
(3, 1, '2025-06-06 23:20:02', 'completed', 3000.00, '2025-06-07 04:20:02', '2025-06-07 05:34:24'),
(4, 1, '2025-06-10 05:38:28', 'pending', 1500.00, '2025-06-10 05:38:28', '2025-06-10 05:38:28'),
(5, 2, '2025-06-13 04:20:02', 'cancelled', 3000.00, '2025-06-13 04:20:02', '2025-06-13 04:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `menu_item_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `fk_order_items_menu_item` (`menu_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `unit_price`, `total_price`) VALUES
(3, 3, 1, 2, 1500.00, 3000.00),
(4, 4, 1, 1, 1500.00, 1500.00),
(5, 5, 1, 2, 1500.00, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pwdresets_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `number_of_guests` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `occasion` enum('casual','birthday','anniversary','business','holiday','family_reunion','engagement','proposal','graduation','date_night','corporate_event','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'casual',
  `special_requests` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reservation_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','confirmed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_code` (`reservation_code`),
  KEY `fk_reservations_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `reservation_date`, `reservation_time`, `number_of_guests`, `occasion`, `special_requests`, `contact_phone`, `reservation_code`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, '2025-06-10', '20:00:00', 2, 'holiday', 'Please confirm if it is available', '03004411222', '7f254af76a5047a3', 'pending', '2025-06-08 04:27:47', '2025-06-08 05:01:59'),
(7, 1, '2025-06-13', '10:00:00', 12, 'birthday', NULL, '03001111222', 'res_68476d', 'pending', '2025-06-10 04:27:39', '2025-06-10 04:27:39'),
(8, 1, '2025-06-18', '11:00:00', 7, 'birthday', NULL, '03004477888', 'res_68476e', 'confirmed', '2025-06-10 04:32:10', '2025-06-12 04:15:15'),
(9, 1, '2025-06-16', '12:00:00', 16, 'birthday', NULL, '03003333333', '853F67', 'confirmed', '2025-06-10 04:37:12', '2025-06-13 04:26:53'),
(10, 1, '2025-06-15', '14:00:00', 6, 'birthday', '', '03001111111', 'c83b43a8b1add7f9a5942476b2e87fe1', 'confirmed', '2025-06-12 07:38:34', '2025-06-12 07:38:34'),
(11, 1, '2025-06-14', '11:00:00', 3, 'birthday', '', '03005588888', 'BD889019', 'confirmed', '2025-06-13 03:04:47', '2025-06-13 03:04:47'),
(12, 2, '2025-06-16', '07:00:00', 3, 'anniversary', '', '03004477888', '72FC9296', 'confirmed', '2025-06-13 04:02:29', '2025-06-13 04:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_settings`
--

DROP TABLE IF EXISTS `reservation_settings`;
CREATE TABLE IF NOT EXISTS `reservation_settings` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `max_tables` int UNSIGNED NOT NULL DEFAULT '20',
  `slot_duration_mins` int UNSIGNED NOT NULL DEFAULT '60',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_settings`
--

INSERT INTO `reservation_settings` (`id`, `max_tables`, `slot_duration_mins`, `created_at`, `updated_at`) VALUES
(1, 180, 60, '2025-06-07 05:53:28', '2025-06-07 06:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
CREATE TABLE IF NOT EXISTS `support_messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'Author of the message (customer)',
  `parent_id` bigint UNSIGNED DEFAULT NULL COMMENT 'NULL for a new support request; otherwise points to the original request',
  `responder_id` bigint UNSIGNED DEFAULT NULL COMMENT 'If reply by staff, their user_id; NULL if customer follow-up',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('open','in_progress','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'open' COMMENT 'Only relevant on top-level requests',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_responder` (`responder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_messages`
--

INSERT INTO `support_messages` (`id`, `user_id`, `parent_id`, `responder_id`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 'Hi, i need help in reservation of a table for tomorrow. can you please guide me?', 'open', '2025-06-08 06:05:35', '2025-06-08 06:05:35'),
(2, 1, 1, 2, 'Sure, please visit he FAQs page for more answers.', 'open', '2025-06-08 06:08:15', '2025-06-08 06:08:15'),
(4, 1, NULL, NULL, 'My food was cold when I recieved it. it was not that okay to eat.', 'open', '2025-06-10 06:01:44', '2025-06-10 06:01:44'),
(5, 1, 4, 2, 'We are sorry to hear that. We have got noticed, and we assure you this will never happen again. thanks for contacting us.', 'open', '2025-06-10 06:07:14', '2025-06-10 06:07:14'),
(6, 1, NULL, NULL, 'help', 'open', '2025-06-12 05:50:30', '2025-06-12 05:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('customer','admin') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad Bilal', 'bilal@gmail.com', '$2y$10$/u/cBiFbVX5Ud.nC07mUyuIB5E9KkfpIDk9xHhkSbQRo09HrSCI7O', '03004411222', 'customer', '2025-06-06 05:33:15', '2025-06-06 05:33:15'),
(2, 'Hafsa Noreen', 'hafsa@gmail.com', '$2y$10$2k5yUbhcyun8vyO1TYuNAuWfn6VXXZgIaNsLeJsm87P2.cO710/Va', '03004422336', 'admin', '2025-06-06 05:49:56', '2025-06-14 21:45:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs` ADD FULLTEXT KEY `ft_question_answer` (`question`,`answer`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD CONSTRAINT `fk_chatbot_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_menu_items_category` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_menu_item` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pwdresets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `fk_messages_parent` FOREIGN KEY (`parent_id`) REFERENCES `support_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_responder` FOREIGN KEY (`responder_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
