-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 25, 2024 at 09:02 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hoteldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `date`, `read_status`) VALUES
(13, 'Nimesha Perera', 'nimesha@example.com', 'Inquiry about Room Availability', 'I would like to inquire about the availability of a double room for the first week of next month.\\r\\n\\r\\nThank You.', '2024-09-22 13:52:36', 1),
(14, 'Dilshan Fernando', 'dilshan@example.com', 'Complaint Regarding Check-In Delay', 'I faced a delay during check-in last week, which was quite inconvenient. I hope this can be addressed.', '2024-09-22 13:53:23', 1),
(15, 'Sanduni Wijesekara', 'sanduni@example.com', 'Appreciation for Great Service', 'I want to express my gratitude for the wonderful service during my recent stay. The staff was incredibly helpful!', '2024-09-22 13:54:04', 1),
(16, 'Prasad Kumar', 'prasad@example.com', 'Inquiry about Restaurant Options', 'Can you please provide information about the dining options available at Surf Bay?', '2024-09-22 13:55:44', 1),
(17, 'Chamal Senanayake', 'chamal@example.com', 'Appreciation for the Scenic View', 'I thoroughly enjoyed the breathtaking view from my room. Thank you for such a fantastic experience!', '2024-09-22 14:03:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`id`, `name`, `price`, `description`, `category`, `image`) VALUES
(13, 'Toast Bread', '1000.00', 'Crispy golden-brown slices of bread, lightly buttered and toasted to perfection, offering a warm and crunchy texture, perfect for breakfast or a quick snack.', 'All Day Brekkie', '../uploads/1725785212_break23.jpg'),
(14, 'Stack Burger', '1250.00', 'A towering creation of juicy, flame-grilled beef patty nestled between soft, toasted buns, layered with melted cheddar cheese, crisp lettuce, fresh tomatoes, and a zesty homemade sauce.', 'All Day Brekkie', '../uploads/1725785324_burger2.png'),
(15, 'Banana Boost Smoothie', '2100.00', 'A creamy blend of fresh bananas, chilled milk, and a hint of honey for natural sweetness, with a splash of vanilla for that extra kick. This smoothie is the perfect pick-me-up, packed with nutrients and a smooth, refreshing taste that\'s perfect for any time of day.', 'All Day Brekkie', '../uploads/1725785430_break2.jpg'),
(17, 'Chicken Burrito', '1500.00', 'The Chicken Burrito is a flavorful wrap filled with tender chicken, rice, beans, cheese, and fresh veggies, all wrapped in a soft tortilla. Perfectly satisfying!', 'LunchTime Mains', '../uploads/6653d1c20974d7c6508ac9a11925870f.jpg'),
(18, 'Chicken Salad', '1100.00', 'A dish made with tender chicken, crisp lettuce, fresh veggies, and a light dressing. A healthy and flavorful choice for any meal!', 'LunchTime Mains', '../uploads/1654a6566d27a6bb5c7111eb928cc58a.jpg'),
(19, 'Tomato Pasta', '800.00', 'A classic dish featuring al dente pasta tossed in a rich, tangy tomato sauce, garnished with herbs and a sprinkle of cheese. Simple yet delicious!', 'LunchTime Mains', '../uploads/682a0323938de0b68d56558aef954b72.jpg'),
(20, 'Ramen Noodles', '950.00', 'A comforting bowl of tender noodles served in a savory broth, topped with vegetables, soft-boiled eggs, and flavorful seasonings. A warm and satisfying meal!', 'LunchTime Mains', '../uploads/743c309acf6c8f860b40a41e930f8d9d.jpg'),
(21, 'Big Mac Tacos', '1050.00', 'Fun twist on the classic burger, featuring seasoned beef, shredded lettuce, cheese, pickles, and special sauce, all wrapped in a soft taco shell. A flavorful fusion!', 'Burger & Tacos', '../uploads/2e58d05770ec1044088db1c094075b28.jpg'),
(22, 'Smash Tacos', '1450.00', 'Packed with smashed beef patties, melted cheese, fresh veggies, and zesty sauces, all served in a crispy taco shell. A bold and savory bite!', 'Burger & Tacos', '../uploads/46f4bf877b29069ecec8fc60d878b6c0.jpg'),
(23, 'Cheeseburger Tacos', '1390.00', 'Combine the best of both worlds, featuring juicy beef, melted cheese, lettuce, tomatoes, and pickles, all wrapped in a soft taco shell. A delicious fusion of flavors!', 'Burger & Tacos', '../uploads/662c4ce1374ca17e9b999f6f5e2ca6fe.jpg'),
(24, 'NY Cheesecake', '400.00', 'A rich and creamy dessert with a smooth, velvety texture, set on a buttery graham cracker crust. A classic New York indulgence that melts in your mouth!', 'Desserts', '../uploads/53ffe2ce6d416ba5dd9492580c4e8251.jpg'),
(25, 'Chewy Brownies', '350.00', 'Decadently rich and fudgy with a delightfully soft, gooey texture. Perfectly baked for a satisfying, chocolatey treat with every bite!', 'Desserts', '../uploads/5782fa7e5b3371e689c11ef2c112974d.jpg'),
(27, 'Churros', '400.00', 'Crispy, golden-brown pastries rolled in cinnamon sugar, with a light and airy inside. Perfectly sweet and crunchy, they’re a delightful treat with a dusting of sweetness!', 'Desserts', '../uploads/23cc176e7e967058152d2dcb51a5f7fc.jpg'),
(28, 'Lava Cake', '550.00', 'Chocolate dessert with a molten, gooey center that flows when you cut into it. Served warm with a scoop of vanilla ice cream, it’s the ultimate indulgence!', 'Desserts', '../uploads/9c87a114745c2d522c4f3577f60f2a74.jpg'),
(29, 'Oat Meal', '990.00', 'Oatmeal is a hearty and nutritious food made from oats that have been either rolled, steel-cut, or ground. It\'s commonly eaten as a hot breakfast porridge, often mixed with water or milk. It\'s also a good source of essential vitamins, minerals, and antioxidants.', 'All Day Brekkie', '../uploads/oatmeal.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_session` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_id` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_session`, `order_date`, `order_id`, `total_amount`, `status`) VALUES
(21, 'Test User', '2024-09-23 13:19:03', 'ORD_66f16ac717533', '3500.00', 'Pending'),
(22, 'Test User', '2024-09-23 13:33:09', 'ORD_66f16e1579e02', '3550.00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `item_price`, `qty`, `total_price`) VALUES
(31, '21', 'Stack Burger', '1250.00', 2, '2500.00'),
(32, '21', 'Toast Bread', '1000.00', 1, '1000.00'),
(33, '22', 'Banana Boost Smoothie', '2100.00', 1, '2100.00'),
(34, '22', 'Smash Tacos', '1450.00', 1, '1450.00');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `price_per_night`, `room_type`, `description`, `image_url`) VALUES
(25, 'Deluxe Double Room', '18000.00', 'Double', 'A spacious room with a king-size bed, ensuite bathroom, and a city view. Perfect for couples.', '../uploads/1727011630_1c38e12db94c42573fb57cb183d663b1.jpg'),
(27, 'Family Suite', '39000.00', 'Suite', 'A spacious family suite with big bed, a living area, and kid-friendly amenities. Ideal for families or groups.', '../uploads/1725979103_56e110385425107c441ccb7b3f53a792.jpg'),
(28, 'Executive Suite', '38000.00', 'Suite', 'A luxurious suite with a separate living area, private balcony, and top-notch amenities for the ultimate stay.', '../uploads/1725979143_7e49fc31a57b6cdb55028c7a44125669.jpg'),
(29, 'Budget Single Room', '6700.00', 'Single', 'A small but comfortable single room with basic amenities and a great value for budget-conscious guests.', '../uploads/1725979171_0b411651aa94a72fa686ff3fe2197528.jpg'),
(30, 'Standard Single Room', '10000.00', 'Single', 'Cozy single room with a comfortable bed, modern amenities, and an elegant decor. Ideal for solo travelers.', '../uploads/1727011728_33e6da6d1ed0ef149c97efe7e982ead3.jpg'),
(31, 'Superior Double Room', '22000.00', 'Double', 'Enjoy a premium double room featuring a beautiful view, modern furnishings, and all the comfort you need.', '../uploads/1727011849_81aefbd684fae6dfce499553d1f64c26.jpg'),
(32, 'Presidential Suite', '9919.00', 'Suite', 'Experience luxury at its finest in our grand Presidential Suite, complete with a private lounge, dining area, and a stunning view.', '../uploads/1727012358_0f44626bd2f465a68a8ef87a8a9991d2.jpg'),
(33, 'Budget Double Room', '18500.00', 'Double', 'A Budget double room with a king-sized bed, elegant decor, and an en-suite bathroom. Perfect for couples seeking comfort.', '../uploads/1727012685_fe62cd5fc6fe847d3a2522ed4972d300.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `arrival_date` date NOT NULL,
  `departure_date` date NOT NULL,
  `guest_count` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `room_id`, `arrival_date`, `departure_date`, `guest_count`, `total_price`, `created_at`, `status`, `user_id`, `user_name`, `user_email`) VALUES
(3, 25, '2024-09-22', '2024-09-24', '2', '0.00', '2024-09-22 16:27:39', 'Completed', 4, 'Test User', 'testuser@surfbay.mirissa'),
(4, 27, '2024-09-22', '2024-09-23', '2', '0.00', '2024-09-22 16:30:06', 'Cancelled', 5, 'Test User2', 'testuser2@surfbay.mirissa'),
(5, 25, '2024-09-27', '2024-09-30', '2', '0.00', '2024-09-23 02:28:43', 'pending', 4, 'Test User', 'testuser@surfbay.mirissa');

-- --------------------------------------------------------

--
-- Table structure for table `userdetails`
--

CREATE TABLE `userdetails` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_type` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userdetails`
--

INSERT INTO `userdetails` (`id`, `username`, `email`, `password`, `image`, `created_at`, `acc_type`) VALUES
(3, 'Admin', 'admin123@gmail.com', '$2y$10$smBLhgXkrbwlRqeX98vFs.y1AO3Cfae8q63HxxH3FHJfOk2ivtKGy', '66f0d3d7522e1.jpg', '2024-09-20 14:44:21', 'admin'),
(4, 'Test User', 'testuser@surfbay.mirissa', '$2y$10$WU4V0slbT.YFegn6ldO8YOJTZLGe7AD4128018Kpo9WlXSYtpArU6', '1726882226_icons8-user-96.png', '2024-09-21 01:30:26', 'user'),
(5, 'Test User2', 'testuser2@surfbay.mirissa', '$2y$10$km2DkMbmvZuERF2osP3xtuedynaTK7R/9ICmaz4f3k7poQ5/bRNTa', '1727022537_icons8-heart-96.png', '2024-09-22 16:28:58', 'user'),
(6, 'Test User3', 'testuser3@surfbay.mirissa', '$2y$10$Fr4pQbBjtaD4BIyO.H.pHOuJZPgLduuIgU16GbpFcfHLcP0vjDNBq', '1727141747_icons8-dashboard-100.png', '2024-09-24 01:35:47', 'user'),
(7, 'Test User4', 'testuser4@surfbay.mirissa', '$2y$10$t1t/nvIFDghQHL0Q5tfrpuVFjcGpTzoa3tvfJ1i3.HLdktBJBQqx.', '1727142400_icons8-save-100.png', '2024-09-24 01:46:40', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userdetails`
--
ALTER TABLE `userdetails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `userdetails`
--
ALTER TABLE `userdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
