-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 17 Ara 2025, 10:33:53
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `lezzet_bahcesi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'İçecekler', 'İçecekler'),
(2, 'Çorbalar', 'Çorbalar'),
(3, 'Makarnalar', 'makarnalar'),
(4, 'Mezeler', 'mezeler');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `recipe_id`, `comment`, `rating`, `created_at`) VALUES
(1, 2, 1, 'güzel olmuş', 5, '2025-12-15 15:27:28'),
(2, 102, 201, 'Harika bir tat, çok ferahlatıcı.', 5, '2025-12-17 06:43:38'),
(3, 103, 201, 'Şekeri biraz fazla geldi bana.', 4, '2025-12-17 06:43:38'),
(4, 104, 206, 'Tam kıvamında, ellerinize sağlık.', 5, '2025-12-17 06:43:38'),
(5, 105, 206, 'Lokanta usulü olmuş gerçekten.', 5, '2025-12-17 06:43:38'),
(6, 106, 211, 'Kreması çok güzel oldu.', 5, '2025-12-17 06:43:38'),
(7, 107, 211, 'Mantarlar taze olmalı kesinlikle.', 4, '2025-12-17 06:43:38'),
(8, 108, 216, 'Humus biraz koyu oldu, su ekledim.', 4, '2025-12-17 06:43:38'),
(9, 109, 216, 'Tahin oranı mükemmel.', 5, '2025-12-17 06:43:38'),
(10, 110, 202, 'Ayran çok köpürdü süper.', 5, '2025-12-17 06:43:38'),
(11, 111, 202, 'Biraz tuzlu olmuş.', 3, '2025-12-17 06:43:38'),
(12, 112, 207, 'Ezogelin favorimdir, beğendim.', 5, '2025-12-17 06:43:38'),
(13, 113, 207, 'Baharatı az geldi.', 4, '2025-12-17 06:43:38'),
(14, 114, 212, 'Bolonez sos efsane.', 5, '2025-12-17 06:43:38'),
(15, 115, 212, 'Kıyma yerine tavuk denedim güzel oldu.', 4, '2025-12-17 06:43:38'),
(16, 116, 217, 'Haydari sarımsaklı güzel.', 5, '2025-12-17 06:43:38'),
(17, 117, 217, 'Dereotu çok yakışmış.', 5, '2025-12-17 06:43:38'),
(18, 118, 203, 'Çocuklar bayıldı.', 5, '2025-12-17 06:43:38'),
(19, 119, 203, 'Rengi harika.', 5, '2025-12-17 06:43:38'),
(20, 120, 208, 'Kaşar peyniriyle servis ettim.', 5, '2025-12-17 06:43:38'),
(21, 101, 208, 'Domatesler ekşiydi biraz.', 3, '2025-12-17 06:43:38'),
(22, 102, 213, 'Fırında nar gibi kızardı.', 5, '2025-12-17 06:43:38'),
(23, 103, 213, 'Beşamel sos topaklandı.', 3, '2025-12-17 06:43:38'),
(24, 104, 218, 'Acısı tam yerinde.', 5, '2025-12-17 06:43:38'),
(25, 105, 218, 'Kebap yanında harika.', 5, '2025-12-17 06:43:38'),
(26, 106, 204, 'Kışın içilecek en güzel şey.', 5, '2025-12-17 06:43:38'),
(27, 107, 204, 'Marshmallow ekledim süper oldu.', 5, '2025-12-17 06:43:38'),
(28, 108, 209, 'Anne tarifi gibi.', 5, '2025-12-17 06:43:38'),
(29, 109, 209, 'Sarımsak da ekledim.', 4, '2025-12-17 06:43:38'),
(30, 110, 214, 'Çok acı oldu yiyemedim.', 2, '2025-12-17 06:43:38'),
(31, 111, 214, 'Tam benlik bir makarna.', 5, '2025-12-17 06:43:38'),
(32, 112, 219, 'Patlıcanlar yağ çekmedi.', 5, '2025-12-17 06:43:38'),
(33, 113, 219, 'Domates sosu biraz sulu kaldı.', 4, '2025-12-17 06:43:38'),
(34, 114, 205, 'Besleyici ve doyurucu.', 5, '2025-12-17 06:43:38'),
(35, 115, 205, 'Süt sevmeyen çocuğum bile içti.', 5, '2025-12-17 06:43:38'),
(36, 116, 210, 'Yayla çorbası çok severim.', 5, '2025-12-17 06:43:38'),
(37, 117, 210, 'Pirinçler biraz diri kaldı.', 3, '2025-12-17 06:43:38'),
(38, 118, 215, 'Ceviz çok yakışmış.', 5, '2025-12-17 06:43:38'),
(39, 119, 215, 'Tereyağı bol olunca güzel.', 5, '2025-12-17 06:43:38'),
(40, 120, 220, 'Fava yapmak zordur ama bu tarif kolay.', 5, '2025-12-17 06:43:38'),
(41, 101, 220, 'Kıvamı tutturamadım.', 3, '2025-12-17 06:43:38'),
(42, 102, 206, 'Limon sıkınca daha güzel.', 5, '2025-12-17 06:43:38'),
(43, 103, 211, 'Akşam yemeği için pratik.', 5, '2025-12-17 06:43:38'),
(44, 104, 216, 'Meze sofrasının kralı.', 5, '2025-12-17 06:43:38'),
(45, 105, 201, 'Naneler ferahlık vermiş.', 5, '2025-12-17 06:43:38'),
(46, 106, 207, 'Sıcak sıcak içmek lazım.', 5, '2025-12-17 06:43:38'),
(47, 107, 212, 'Makarna çok haşlanmış.', 3, '2025-12-17 06:43:38'),
(48, 108, 217, 'Ekmek banmalık.', 5, '2025-12-17 06:43:38'),
(49, 109, 203, 'Şekersiz yaptım yine de güzel.', 4, '2025-12-17 06:43:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `rating` float DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `category_id`, `title`, `description`, `image`, `views`, `rating`, `created_at`) VALUES
(1, 2, 4, 'Pancarlı Marul Salatası (Pcos Dostu)', '???? Neden PCOS dostu?\r\n• Pancar → antiinflamatuar, folat zengini\r\n• Marul & maydanoz → lif ve su yönünden zengin\r\n• Ceviz → iyi yağ, kan şekerini dengeler\r\n• Zeytinyağı + limon → metabolizmayı rahatlatır', 'recipe_1_69402124baf46.jpg', 43, 0, '2025-12-11 08:22:12'),
(201, 101, 1, 'Naneli Limonata', 'Yaz aylarının vazgeçilmezi, ferahlatıcı ev yapımı limonata.', 'https://placehold.co/600x400/fff8dc/orange?text=Limonata', 150, 0, '2025-12-17 06:43:37'),
(202, 102, 1, 'Köpüklü Ayran', 'Yoğurt ve suyun mükemmel uyumu, bol köpüklü.', 'https://placehold.co/600x400/f0f8ff/black?text=Ayran', 200, 0, '2025-12-17 06:43:37'),
(203, 103, 1, 'Çilekli Smoothie', 'Taze çileklerle hazırlanan vitamin deposu.', 'https://placehold.co/600x400/ffb6c1/white?text=Smoothie', 120, 0, '2025-12-17 06:43:37'),
(204, 104, 1, 'Sıcak Çikolata', 'Kış günlerinde içinizi ısıtacak yoğun çikolata lezzeti.', 'https://placehold.co/600x400/8b4513/white?text=Sicak+Cikolata', 300, 0, '2025-12-17 06:43:37'),
(205, 105, 1, 'Muzlu Süt', 'Çocukların en sevdiği, besleyici ve lezzetli içecek.', 'https://placehold.co/600x400/ffe4b5/black?text=Muzlu+Sut', 180, 0, '2025-12-17 06:43:37'),
(206, 106, 2, 'Süzme Mercimek Çorbası', 'Lokanta usulü, tam kıvamında mercimek çorbası.', 'https://placehold.co/600x400/ffa500/white?text=Mercimek+Corbasi', 500, 0, '2025-12-17 06:43:37'),
(207, 107, 2, 'Ezogelin Çorbası', 'Anadolu mutfağının incisi, bol baharatlı şifa kaynağı.', 'https://placehold.co/600x400/cd853f/white?text=Ezogelin', 450, 0, '2025-12-17 06:43:37'),
(208, 108, 2, 'Domates Çorbası', 'Közlenmiş domates tadında, kaşar rendesiyle servis edilir.', 'https://placehold.co/600x400/ff6347/white?text=Domates+Corbasi', 350, 0, '2025-12-17 06:43:37'),
(209, 109, 2, 'Tarhana Çorbası', 'Geleneksel lezzetimiz, ev yapımı tarhana.', 'https://placehold.co/600x400/d2b48c/white?text=Tarhana', 400, 0, '2025-12-17 06:43:37'),
(210, 110, 2, 'Yayla Çorbası', 'Naneli sosuyla ferahlatıcı pirinçli yoğurt çorbası.', 'https://placehold.co/600x400/fffacd/black?text=Yayla+Corbasi', 380, 0, '2025-12-17 06:43:37'),
(211, 111, 3, 'Kremalı Mantarlı Makarna', 'İtalyan restoranlarını aratmayacak lezzette.', 'https://placehold.co/600x400/f5deb3/black?text=Kremali+Makarna', 600, 0, '2025-12-17 06:43:37'),
(212, 112, 3, 'Spagetti Bolonez', 'Kıymalı özel sosuyla klasik bir lezzet.', 'https://placehold.co/600x400/8b0000/white?text=Bolonez', 701, 0, '2025-12-17 06:43:37'),
(213, 113, 3, 'Fırın Makarna', 'Beşamel soslu, üzeri nar gibi kızarmış kaşarlı makarna.', 'https://placehold.co/600x400/ffd700/black?text=Firin+Makarna', 550, 0, '2025-12-17 06:43:37'),
(214, 114, 3, 'Penne Arrabbiata', 'Acı severler için domatesli ve acılı makarna.', 'https://placehold.co/600x400/ff4500/white?text=Arrabbiata', 420, 0, '2025-12-17 06:43:37'),
(215, 115, 3, 'Peynirli Cevizli Erişte', 'Anne eli değmiş gibi tereyağlı erişte.', 'https://placehold.co/600x400/ffe4c4/black?text=Eriste', 300, 0, '2025-12-17 06:43:37'),
(216, 116, 4, 'Humus', 'Tahin ve nohutun muhteşem uyumu.', 'https://placehold.co/600x400/deb887/white?text=Humus', 250, 0, '2025-12-17 06:43:37'),
(217, 117, 4, 'Haydari', 'Süzme yoğurt, nane ve sarımsaklı ferah meze.', 'https://placehold.co/600x400/f0ffff/black?text=Haydari', 280, 0, '2025-12-17 06:43:37'),
(218, 118, 4, 'Acılı Ezme', 'Kebapların en iyi eşlikçisi, iştah açıcı.', 'https://placehold.co/600x400/b22222/white?text=Acili+Ezme', 320, 0, '2025-12-17 06:43:37'),
(219, 119, 4, 'Şakşuka', 'Kızarmış patlıcanların domates sosuyla buluşması.', 'https://placehold.co/600x400/4b0082/white?text=Saksuka', 290, 0, '2025-12-17 06:43:37'),
(220, 120, 4, 'Fava', 'Zeytinyağlı bakla ezmesi, dereotu ile servis edilir.', 'https://placehold.co/600x400/9acd32/white?text=Fava', 210, 0, '2025-12-17 06:43:37');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `recipe_images`
--

CREATE TABLE `recipe_images` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `recipe_images`
--

INSERT INTO `recipe_images` (`id`, `recipe_id`, `image_path`, `image`) VALUES
(1, 1, 'recipe_1_69402124baf46.jpg', NULL),
(2, 1, 'recipe_1_69402124bb90f.jpg', NULL),
(3, 201, 'https://placehold.co/600x400/fff8dc/orange?text=Limonata', NULL),
(4, 202, 'https://placehold.co/600x400/f0f8ff/black?text=Ayran', NULL),
(5, 203, 'https://placehold.co/600x400/ffb6c1/white?text=Smoothie', NULL),
(6, 204, 'https://placehold.co/600x400/8b4513/white?text=Sicak+Cikolata', NULL),
(7, 205, 'https://placehold.co/600x400/ffe4b5/black?text=Muzlu+Sut', NULL),
(8, 206, 'https://placehold.co/600x400/ffa500/white?text=Mercimek+Corbasi', NULL),
(9, 207, 'https://placehold.co/600x400/cd853f/white?text=Ezogelin', NULL),
(10, 208, 'https://placehold.co/600x400/ff6347/white?text=Domates+Corbasi', NULL),
(11, 209, 'https://placehold.co/600x400/d2b48c/white?text=Tarhana', NULL),
(12, 210, 'https://placehold.co/600x400/fffacd/black?text=Yayla+Corbasi', NULL),
(13, 211, 'https://placehold.co/600x400/f5deb3/black?text=Kremali+Makarna', NULL),
(14, 212, 'https://placehold.co/600x400/8b0000/white?text=Bolonez', NULL),
(15, 213, 'https://placehold.co/600x400/ffd700/black?text=Firin+Makarna', NULL),
(16, 214, 'https://placehold.co/600x400/ff4500/white?text=Arrabbiata', NULL),
(17, 215, 'https://placehold.co/600x400/ffe4c4/black?text=Eriste', NULL),
(18, 216, 'https://placehold.co/600x400/deb887/white?text=Humus', NULL),
(19, 217, 'https://placehold.co/600x400/f0ffff/black?text=Haydari', NULL),
(20, 218, 'https://placehold.co/600x400/b22222/white?text=Acili+Ezme', NULL),
(21, 219, 'https://placehold.co/600x400/4b0082/white?text=Saksuka', NULL),
(22, 220, 'https://placehold.co/600x400/9acd32/white?text=Fava', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `ingredient_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`id`, `recipe_id`, `ingredient_text`) VALUES
(7, 1, '1 küçük boy pancar (haşlanmış ve küp doğranmış)'),
(8, 1, '6–7 yaprak marul (ince doğranmış)'),
(9, 1, '1 küçük salatalık (yarım ay doğranmış)'),
(10, 1, '1 avuç maydanoz (ince kıyılmış)'),
(11, 1, '2 yemek kaşığı haşlanmış nohut'),
(12, 1, '1 yemek kaşığı ceviz (iri kırılmış)'),
(13, 201, '5 Limon, 1 Su Bardağı Şeker, Taze Nane'),
(14, 202, '1 Kase Yoğurt, 2 Bardak Su, Tuz'),
(15, 203, '10 Adet Çilek, 1 Bardak Süt, 1 Kaşık Bal'),
(16, 204, '2 Bardak Süt, 2 Kaşık Kakao, 2 Kaşık Şeker'),
(17, 205, '1 Adet Muz, 1 Bardak Süt, 1 Çay Kaşığı Bal'),
(18, 206, '1 Bardak Mercimek, 1 Soğan, 1 Patates'),
(19, 207, '1 Bardak Mercimek, 1 Kaşık Bulgur, Baharatlar'),
(20, 208, '4 Adet Domates, 1 Kaşık Un, 1 Bardak Süt'),
(21, 209, '3 Kaşık Tarhana, 1 Kaşık Salça, Nane'),
(22, 210, '1 Kase Yoğurt, 1 Yumurta, 1 Kaşık Un, Pirinç'),
(23, 211, '1 Paket Makarna, 1 Kutu Krema, Mantar'),
(24, 212, '1 Paket Spagetti, 200g Kıyma, Domates Sos'),
(25, 213, '1 Paket Fırın Makarna, Beşamel Sos, Kaşar'),
(26, 214, '1 Paket Penne, Acı Biber, Sarımsak, Domates'),
(27, 215, '2 Su Bardağı Erişte, Tereyağı, Peynir, Ceviz'),
(28, 216, '2 Bardak Nohut, Yarım Bardak Tahin, Limon'),
(29, 217, '1 Kase Süzme Yoğurt, Dereotu, Sarımsak'),
(30, 218, 'Domates, Biber, Soğan, Maydanoz, Nar Ekşisi'),
(31, 219, '3 Patlıcan, 2 Biber, Domates Sosu'),
(32, 220, '1 Bardak Kuru Bakla, 1 Soğan, Zeytinyağı');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `recipe_steps`
--

CREATE TABLE `recipe_steps` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `step_number` int(11) DEFAULT NULL,
  `step_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `recipe_steps`
--

INSERT INTO `recipe_steps` (`id`, `recipe_id`, `step_number`, `step_text`) VALUES
(7, 1, 1, 'Marulu ince ince doğrayıp geniş bir kaseye alalım.'),
(8, 1, 2, 'Salatalık, maydanoz ve nohutu ekleyelim.'),
(9, 1, 3, 'Küp doğranmış pancarları en üste ekleyelim. (dağılmaması için karıştırmayı en sona bırakacağız).'),
(10, 1, 4, 'Cevizi üzerine serpiştirelim.'),
(11, 1, 5, 'Sos malzemelerini küçük bir kapta çırpalım.'),
(12, 1, 6, 'Servisten hemen önce sosu salatanın üzerine dökelim ve nazikçe karıştıralım. Afiyetle ✨.'),
(13, 201, 1, 'Limonların suyunu sıkın ve şekerle karıştırın.'),
(14, 202, 1, 'Yoğurdu su ve tuzla köpürene kadar çırpın.'),
(15, 203, 1, 'Tüm malzemeleri blenderdan geçirin.'),
(16, 204, 1, 'Süt, kakao ve şekeri tencerede kaynatın.'),
(17, 205, 1, 'Muzu ezin ve sütle karıştırın.'),
(18, 206, 1, 'Sebzeleri haşlayıp blenderdan geçirin.'),
(19, 207, 1, 'Bakliyatları haşlayın, baharatları ekleyin.'),
(20, 208, 1, 'Unu kavurun, rendelenmiş domatesi ekleyin.'),
(21, 209, 1, 'Tarhanayı suda çözüp kaynayana kadar karıştırın.'),
(22, 210, 1, 'Yoğurtlu terbiyeyi hazırlayıp çorbaya ekleyin.'),
(23, 211, 1, 'Makarnayı haşlayın, mantarlı krema sosla karıştırın.'),
(24, 212, 1, 'Kıymalı sosu pişirip makarnanın üzerine dökün.'),
(25, 213, 1, 'Makarnayı haşlayın, sosla karıştırıp fırınlayın.'),
(26, 214, 1, 'Acı sosu hazırlayıp makarna ile harmanlayın.'),
(27, 215, 1, 'Erişteyi haşlayıp tereyağı ve peynirle servis edin.'),
(28, 216, 1, 'Tüm malzemeleri robotta pürüzsüz olana kadar çekin.'),
(29, 217, 1, 'Malzemeleri karıştırıp soğuk servis yapın.'),
(30, 218, 1, 'Tüm sebzeleri çok ince doğrayıp karıştırın.'),
(31, 219, 1, 'Sebzeleri kızartın, üzerine domates sosu dökün.'),
(32, 220, 1, 'Baklayı haşlayıp püre haline getirin, zeytinyağı ekleyin.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `role` enum('user','manager','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_code` int(6) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `new_email_pending` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar`, `role`, `created_at`, `reset_code`, `verification_code`, `new_email_pending`) VALUES
(2, 'Yasin ÖZEN', 'yasinvefizik@gmail.com', '$2y$10$62MgoA73OoW9fLD5a1u./.ZVbnV.nH2392wIVqxr/CAbBwARBg3Hi', 'user_2_693a7e298e60a.jpeg', 'admin', '2025-12-04 14:38:46', 344811, NULL, NULL),
(12, 'Nurtaç MOSMOS', 'nurtac@gmail.com', '$2y$10$yFTbvRasLvKj4hJgmMU1FuGaCH4mKy/wpePg.2UQ.R1JgcniptYl2', 'default_avatar.png', 'user', '2025-12-11 08:25:27', 886862, NULL, NULL),
(101, 'Seda Yılmaz', 'seda@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffa07a/white?text=S', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(102, 'Kerem Bürsin', 'kerem@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/b0e0e6/white?text=K', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(103, 'Elçin Sangu', 'elcin@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffdab9/white?text=E', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(104, 'Barış Arduç', 'baris@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffd1dc/white?text=B', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(105, 'Hande Erçel', 'hande@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffa07a/white?text=H', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(106, 'Burak Özçivit', 'burak@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/b0e0e6/white?text=B', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(107, 'Fahriye Evcen', 'fahriye@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffdab9/white?text=F', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(108, 'Kıvanç Tatlıtuğ', 'kivanc@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffd1dc/white?text=K', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(109, 'Serenay Sarıkaya', 'serenay@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffa07a/white?text=S', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(110, 'Çağatay Ulusoy', 'cagatay@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/b0e0e6/white?text=C', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(111, 'Demet Özdemir', 'demet@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffdab9/white?text=D', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(112, 'Can Yaman', 'can@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffd1dc/white?text=C', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(113, 'Neslihan Atagül', 'neslihan@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffa07a/white?text=N', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(114, 'Kadir Doğulu', 'kadir@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/b0e0e6/white?text=K', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(115, 'Aras Bulut İynemli', 'aras@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffdab9/white?text=A', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(116, 'Bige Önal', 'bige@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffd1dc/white?text=B', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(117, 'Engin Akyürek', 'engin@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffa07a/white?text=E', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(118, 'Tuba Büyüküstün', 'tuba@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/b0e0e6/white?text=T', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(119, 'Bergüzar Korel', 'berguzar@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffdab9/white?text=B', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL),
(120, 'Halit Ergenç', 'halit@test.com', '$2y$10$8.hzC.X1/1k.K/h.1/1.1.1.1.1.1.1.1.1.1.1.1.1.1.1', 'https://placehold.co/150x150/ffd1dc/white?text=H', 'user', '2025-12-17 06:43:37', NULL, NULL, NULL);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `recipe_images`
--
ALTER TABLE `recipe_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `recipe_steps`
--
ALTER TABLE `recipe_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Tablo için AUTO_INCREMENT değeri `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- Tablo için AUTO_INCREMENT değeri `recipe_images`
--
ALTER TABLE `recipe_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Tablo için AUTO_INCREMENT değeri `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `recipe_steps`
--
ALTER TABLE `recipe_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Tablo kısıtlamaları `recipe_images`
--
ALTER TABLE `recipe_images`
  ADD CONSTRAINT `recipe_images_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `recipe_steps`
--
ALTER TABLE `recipe_steps`
  ADD CONSTRAINT `recipe_steps_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
