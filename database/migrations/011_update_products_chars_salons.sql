-- Таблица характеристик (привязаны к категории)
CREATE TABLE IF NOT EXISTS characteristics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  sort INT DEFAULT 0,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Значения характеристик для товара
CREATE TABLE IF NOT EXISTS product_characteristics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  characteristic_id INT NOT NULL,
  value VARCHAR(500) NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (characteristic_id) REFERENCES characteristics(id) ON DELETE CASCADE,
  UNIQUE KEY (product_id, characteristic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Салоны
CREATE TABLE IF NOT EXISTS salons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  city VARCHAR(100) NOT NULL,
  address VARCHAR(500) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(255) DEFAULT NULL,
  image VARCHAR(500) DEFAULT NULL,
  brands TEXT DEFAULT NULL,
  sort INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Наличие товара в салонах
CREATE TABLE IF NOT EXISTS product_salons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  salon_id INT NOT NULL,
  stock_status ENUM('in_stock', 'out_of_stock', 'on_order') DEFAULT 'in_stock',
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE,
  UNIQUE KEY (product_id, salon_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Заполняем салоны
INSERT INTO salons (name, city, address, phone, email, image, brands, sort) VALUES
('Radar Extreme Иваново', 'Иваново', 'ул. Пограничника Рыжикова, д. 36', '+7(4932) 245-220', 'yamaha@radar-avto.ru', 'https://radarextreme.ru/upload/iblock/d08/d0873b5f8f94d001bdf85bed9913016a.jpg', 'Yamaha,Русская механика,BRP,AODES,Stels,Bajaj,Honda,Tohatsu,Kayo,Polaris,Phoenix,Bester,Salut,Volzhanka,Marlin,Hidea,M1NSK,Gladiator,VOGE,Loncin,CYCLONE,Lifan,Segway,Zontes,Avantis,МЗСА', 1),
('Radar Extreme Ярославль', 'Ярославль', 'ул. Вишняки, д.2', '+7 (4852) 63-98-59', 'radarextreme76@yandex.ru', 'https://radarextreme.ru/upload/iblock/3ea/eqdv8b0g54l5njwykwdsoh0rs1zkfbbx.jpg', 'Kayo,Marlin,Volzhanka,Phoenix,Hidea,Gladiator,Stels,Bajaj,Salut', 2),
('Radar Extreme Кострома', 'Кострома', 'ул. Никитская, д. 70А', '+7 (4942) 63-49-42', 'extreme.kostroma@yandex.ru', 'https://radarextreme.ru/upload/iblock/f16/aekiii2865p63aqc41ehhbdex1hv7k88.jpg', 'Yamaha,Stels,Salut,Volzhanka,Marlin,Hidea,Gladiator', 3),
('Radar Extreme Кинешма', 'Кинешма', 'Савинская улица, 17Б', '+7(4932)24-55-30', 'yamaha@radar-avto.ru', 'https://radarextreme.ru/upload/iblock/eb9/eb93665187b839b47f1c30bd92c2b39b.jpg', 'Kayo,Marlin,Volzhanka,Phoenix,Hidea,Gladiator,Stels,Bajaj,Salut', 4),
('Radar Extreme Владимир', 'Владимир', 'ул. Опольевская, 1А', '+7 (4922) 49-48-49', 'yamaha@radar-avto.ru', 'https://radarextreme.ru/upload/iblock/115/xt0ir0x52wp6re3nngskw42lq9rjac3w.jpg', 'Русская механика,AODES,GLADIATOR,Marlin,Hidea,ZONTES,Волжанка,Триера,Салют,M1NSK,Progasi,Voge,Regulmoto,Ataki,CYCLONE,Lonchin,KOVE,GAOKIN', 5);