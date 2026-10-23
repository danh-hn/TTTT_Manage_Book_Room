-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 23, 2026 lúc 06:08 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanlyphonghop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `participant_count` int(11) NOT NULL DEFAULT 1,
  `extra_services` varchar(100) DEFAULT 'Không',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('pending','approved','cancelled') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `expires_at`, `title`, `participant_count`, `extra_services`, `start_time`, `end_time`, `status`, `created_at`) VALUES
(20, 5, 5, NULL, 's', 3, NULL, '2026-10-18 00:00:00', '2026-10-18 01:00:00', 'pending', '2026-10-17 16:01:14'),
(23, 6, 6, NULL, 'Họp ABC', 8, NULL, '2026-10-23 21:00:00', '2026-10-23 23:00:00', 'cancelled', '2026-10-23 13:30:15'),
(24, 7, 6, NULL, 's', 2, NULL, '2026-10-23 21:00:00', '2026-10-23 22:00:00', 'approved', '2026-10-23 13:32:08'),
(25, 6, 3, NULL, 'abvcc', 1, NULL, '2026-10-23 21:00:00', '2026-10-23 22:00:00', 'approved', '2026-10-23 13:48:25'),
(26, 7, 3, NULL, 'scđx', 1, NULL, '2026-10-23 21:00:00', '2026-10-23 22:00:00', 'cancelled', '2026-10-23 13:49:09'),
(27, 6, 12, NULL, 's', 3, NULL, '2026-10-23 21:00:00', '2026-10-23 22:00:00', 'approved', '2026-10-23 13:58:02'),
(28, 7, 12, NULL, 's', 3, NULL, '2026-10-23 21:00:00', '2026-10-23 22:00:00', 'approved', '2026-10-23 13:58:18'),
(47, 6, 3, '2026-10-23 22:49:02', 'Đang giữ chỗ tạm thời', 1, '', '2026-10-23 23:00:00', '2026-10-24 00:00:00', '', '2026-10-23 15:44:02'),
(48, 6, 6, '2026-10-23 22:52:53', 'Đang giữ chỗ tạm thời', 1, '', '2026-10-23 23:00:00', '2026-10-24 00:00:00', '', '2026-10-23 15:47:53'),
(49, 6, 6, '2026-10-23 22:53:40', 'Đang giữ chỗ tạm thời', 1, '', '2026-10-23 23:00:00', '2026-10-24 00:00:00', '', '2026-10-23 15:48:40'),
(50, 6, 1, '2026-10-23 23:05:51', 'Đang giữ chỗ tạm thời', 1, '', '2026-10-24 00:00:00', '2026-10-24 01:00:00', '', '2026-10-23 16:00:51'),
(51, 6, 6, '2026-10-23 23:11:29', 'Đang giữ chỗ tạm thời', 1, '', '2026-10-24 00:00:00', '2026-10-24 01:00:00', '', '2026-10-23 16:06:29');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
