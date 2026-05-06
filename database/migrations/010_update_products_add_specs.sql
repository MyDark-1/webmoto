ALTER TABLE products
  ADD COLUMN specifications TEXT AFTER characteristics,
  ADD COLUMN stock_status VARCHAR(20) DEFAULT 'in_stock' AFTER status;

UPDATE products SET stock_status = 'in_stock' WHERE stock_status IS NULL;