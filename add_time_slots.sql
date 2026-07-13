-- QuickWorks: Add booking_time_slots table
-- Run this migration on an existing database

USE `quickworksdb`;

-- 9. booking_time_slots table
-- Tracks hourly time slot bookings for workers (6 AM to 6 PM)
CREATE TABLE IF NOT EXISTS `booking_time_slots` (
  `slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `slot_date` date NOT NULL,
  `slot_hour` tinyint(2) NOT NULL COMMENT '6=6AM, 7=7AM, ..., 17=5PM (6AM-6PM range)',
  `status` enum('PENDING','BOOKED','RELEASED','COMPLETED') DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`slot_id`),
  KEY `idx_worker_date` (`worker_id`, `slot_date`, `slot_hour`),
  KEY `idx_job` (`job_id`),
  FOREIGN KEY (`job_id`) REFERENCES `job_requests`(`job_id`) ON DELETE CASCADE,
  FOREIGN KEY (`worker_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
