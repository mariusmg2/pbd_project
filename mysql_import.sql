
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(60) NOT NULL,
  `product_category` varchar(60) NOT NULL,
  `product_name` varchar(60) NOT NULL,
  `product_desc` tinytext NOT NULL,
  `product_img_name` varchar(60) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_code` (`product_code`)
) AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_name` (`category_name`)
) AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `credentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) AUTO_INCREMENT=1 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_category`, `product_name`, `product_desc`, `product_img_name`, `price`) VALUES
(1, 'PD1001', 'telefoane', 'Telefon Android', 'Telefon...', 'android-phone.jpg', 200.50),
(2, 'PD1002', 'televizoare', 'Television Samsung', 'Televizor...', 'lcd-tv.jpg', 500.85),
(3, 'PD1003', 'telefoane', 'Telefon WP', 'Alt telefon...', 'external-hard-disk.jpg', 100.00),
(4, 'PD1004', 'televizoare', 'Televizor Android', 'Alt televizor...', 'wrist-watch.jpg', 400.30);

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, 'telefoane'),
(2, 'televizoare');

INSERT INTO `credentials` (`id`, `username`, `password`) VALUES (1, 'root', 'root');
