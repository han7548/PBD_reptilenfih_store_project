USE Tugas_Proyek_PBD_Toko;

CREATE TABLE IF NOT EXISTS role (
idrole INT AUTO_INCREMENT PRIMARY KEY,
nama_role VARCHAR(100) NOT NULL
);

DESCRIBE role;

CREATE TABLE IF NOT EXISTS satuan (
idsatuan INT AUTO_INCREMENT PRIMARY KEY,
nama_satuan VARCHAR(45) NOT NULL,
STATUS TINYINT NOT NULL DEFAULT 1
);
DESCRIBE satuan;

---- something just isint sits right witth me on status
-- especially those who uses tinyint
---- its not eveTugas_Proyek_PBD_TokoTugas_Proyek_PBD_Tokon tiinyint (1) or sum like alr i get hhow sql treats this the
--- same way like boolean but this way there would be possibilities of a man putting 5 out of nowhere
-- im adding a conditions bro, for my own sanity, i overthink this again
--- btw im stating the valid status fot whatever atributes that uses char(1)
--- as A = active I = inactive

CREATE TABLE IF NOT EXISTS vendor (
idvendor INT AUTO_INCREMENT PRIMARY KEY,
nama_vendor VARCHAR(100) NOT NULL,
badan_hukum CHAR(1) NOT NULL,
status CHAR(1) NOT NULL DEFAULT 'A',
CONSTRAINT check_vendor_status CHECK (status IN ('A','I'))
);
DESCRIBE vendor;

CREATE TABLE IF NOT EXISTS USER (
iduser INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(45) NOT NULL,
PASSWORD VARCHAR(100) NOT NULL, 
idrole INT  NOT NULL,
CONSTRAINT fk_user_role FOREIGN KEY (idrole) REFERENCES role(idrole)
);

DESCRIBE USER;
-- since its a char(100) passwrd ill just assuume it isnt a hashed pswrd for now

CREATE TABLE IF NOT EXISTS barang (
idbarrang INT AUTO_INCREMENT PRIMARY KEY,
jenis CHAR(1) NOT NULL,
nama VARCHAR(45) NOT NULL,
idsatuan INT NOT NULL,
STATUS TINYINT NOT NULL DEFAULT 1,
harga INT NOT NULL,
CONSTRAINT fk_barang_satuan FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan)
);

ALTER TABLE barang
ADD CONSTRAINT check_jenis_barang CHECK (jenis IN ('H', 'F', 'P'));
---- hewan, makanan loh perlengkapan kelupaan
ALTER TABLE barang DROP CONSTRAINT check_jenis_barang;


DESCRIBE barang;

CREATE TABLE IF NOT EXISTS margin_penjualan (
idmargin_penjualan INT AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
persen DOUBLE NOT NULL,
STATUS TINYINT NOT NULL DEFAULT 1,
iduser INT NOT NULL,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_margin_penjualan_user FOREIGN KEY (iduser) REFERENCES USER(iduser)
);
DESCRIBE margin_penjualan;


CREATE TABLE IF NOT EXISTS pengadaan (
idpengadaan BIGINT AUTO_INCREMENT PRIMARY KEY,
timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
user_iduser INT NOT NULL,
status CHAR(1) NOT NULL DEFAULT 'P',
vendor_idvendor INT NOT NULL,
subtotal_nilai INT,
ppn INT,
total_nilai INT,
CONSTRAINT chk_pengadaan_status CHECK (status IN ('P', 'C', 'F')),
CONSTRAINT fk_pengadaan_user FOREIGN KEY (user_iduser) REFERENCES USER(iduser),
CONSTRAINT fk_pengadaan_vendor FOREIGN KEY (vendor_idvendor) REFERENCES vendor(idvendor)
);
--- Proceed, canceled, finished yeurr
DESCRIBE pengadaan;

CREATE TABLE IF NOT EXISTS detail_pengadaan (
iddetail_pengadaan BIGINT AUTO_INCREMENT PRIMARY KEY,
harga_satuan INT NOT NULL,
jumlah INT NOT NULL,
sub_total INT NOT NULL,
idbarang INT NOT NULL,
idpengadaan BIGINT NOT NULL, 
CONSTRAINT fk_detail_pengadaan_barang FOREIGN KEY (idbarang) REFERENCES barang(idbarrang),
CONSTRAINT fk_detail_pengadaan_pengadaan FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan)
);
---- idpengadaan ganti bigint
DESCRIBE detail_pengadaan;

CREATE TABLE IF NOT EXISTS penerimaan (
idpenerimaan BIGINT AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
status CHAR(1) NOT NULL DEFAULT 'P',
idpengadaan BIGINT NOT NULL,
iduser INT NOT NULL,
CONSTRAINT chk_penerimaan_status CHECK (status IN ('P', 'C', 'F')),
CONSTRAINT fk_penerimaan_pengadaan FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan),
CONSTRAINT fk_penerimaan_user FOREIGN KEY (iduser) REFERENCES USER(iduser)
);
--- i geniuenly got no idea what options should i put at status
DESCRIBE penerimaan;

CREATE TABLE IF NOT EXISTS detail_penerimaan (
iddetail_penerimaan BIGINT AUTO_INCREMENT PRIMARY KEY,
idpenerimaan BIGINT NOT NULL,
barang_idbarang INT NOT NULL,
jumlah_terima INT NOT NULL,
harga_satuan_terima INT NOT NULL,
sub_total_terima INT NOT NULL,
CONSTRAINT fk_detail_penerimaan_parent FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
CONSTRAINT fk_detail_penerimaan_barang FOREIGN KEY (barang_idbarang) REFERENCES barang(idbarrang)
);
DESCRIBE detail_penerimaan;

CREATE TABLE IF NOT EXISTS penjualan (
idpenjualan INT AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
subtotal_nilai INT,
ppn INT,
total_nilai INT,
iduser INT NOT NULL,
idmargin_penjualan INT NOT NULL,
CONSTRAINT fk_penjualan_user FOREIGN KEY (iduser) REFERENCES USER(iduser),
CONSTRAINT fk_penjualan_margin FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan)
);
DESCRIBE penjualan;

CREATE TABLE IF NOT EXISTS detail_penjualan (
iddetail_penjualan BIGINT AUTO_INCREMENT PRIMARY KEY,
harga_satuan INT NOT NULL,
jumlah INT NOT NULL,
subtotal INT NOT NULL,
penjualan_idpenjualan INT NOT NULL,
idbarang INT NOT NULL,
CONSTRAINT fk_detail_penjualan_parent FOREIGN KEY (penjualan_idpenjualan) REFERENCES penjualan(idpenjualan),
CONSTRAINT fk_detail_penjualan_barang FOREIGN KEY (idbarang) REFERENCES barang(idbarrang)
);

--- daily reminder to refresh your database everytime u do a ddl or dml 
DESCRIBE detail_penjualan;

CREATE TABLE IF NOT EXISTS retur (
idretur BIGINT AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
idpenerimaan BIGINT NOT NULL,
iduser INT NOT NULL,
CONSTRAINT fk_retur_penerimaan FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
CONSTRAINT fk_retur_user FOREIGN KEY (iduser) REFERENCES USER(iduser)
);
DESCRIBE retur;

CREATE TABLE IF NOT EXISTS detail_retur (
iddetail_retur INT AUTO_INCREMENT PRIMARY KEY,
jumlah INT NOT NULL,
alasan VARCHAR(200),
idretur BIGINT NOT NULL,
iddetail_penerimaan BIGINT NOT NULL,
CONSTRAINT fk_detail_retur_parent FOREIGN KEY (idretur) REFERENCES retur(idretur),
CONSTRAINT fk_detail_retur_penerimaan FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan)
);
DESCRIBE detail_retur;

CREATE TABLE IF NOT EXISTS kartu_stok (
idkartu_stok BIGINT AUTO_INCREMENT PRIMARY KEY,
jenis_transaksi CHAR(1) NOT NULL,
masuk INT DEFAULT 0,
keluar INT DEFAULT 0,
stock INT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
idtransaksi INT NOT NULL, 
idbarang INT NOT NULL,
CONSTRAINT chk_kartu_stok_jenis CHECK (jenis_transaksi IN ('I', 'O')), 
CONSTRAINT fk_kartu_stok_barang FOREIGN KEY (idbarang) REFERENCES barang(idbarrang)
);
--- in, out
DESCRIBE kartu_stok;

-----------------------------------------------------------------------------------------------------------------------------------------------


INSERT INTO role (nama_role) VALUES 
('Admin'), 
('Kasir'), 
('Manager');
-- role functions and previledges : 
-- admin : kasir and manager functions, control panel CRUD on each master functions
--- manager : karir functions, crud pengadaan and penerimaan
--- kasir : penjualan, see laporan 

 SELECT * FROM role;

INSERT INTO satuan (nama_satuan, STATUS) VALUES 
('Ekor', 1),   -- livestock (fishes, reptiles)
('Pcs', 1),    -- For equipment/husbandry
('Gram', 1),   -- For small food items or supplements
('Pack', 1),   -- frozen foods or substrates
('Liter', 1);  -- water treatments

SELECT * FROM satuan;

INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES 
('U Got Jumped Underwater', 'P', 'A'),
('Reptile Spawner (Trusted)', 'C', 'A'),
('ExoHighQuality gear Supplier', 'P', 'A'),
('Meals Supply not for Humans', 'C', 'A');

SELECT * FROM vendor;

INSERT INTO USER (username, PASSWORD, idrole) VALUES 
('Han_admin', 'syadmin123', 1),
('han_kasir', 'sykasir123', 2),
('han_manager', 'symanager123', 3);

SELECT * FROM USER;

INSERT INTO barang (jenis, nama, idsatuan, STATUS, harga) VALUES 

('H', 'Channa Barca 15cm', 1, 1, 15000000),
('H', 'Bearded Dragon (Juvenile)', 1, 1, 850000),
('H', 'Ball Python (Normal)', 1, 1, 700000),
('H', 'Peacock Bass Monoculus', 1, 1, 150000),
('H', 'Argus Monitor (Biawak)', 1, 1, 1200000),

('F', 'Tikus Putih / Mice (Frozen)', 2, 1, 7500),
('F', 'Rat Sapih (Frozen)', 2, 1, 15000),
('F', 'Jangkrik Alam', 4, 1, 10000),
('F', 'Hikari Carnivore Pellets', 4, 1, 125000),
('F', 'Kecoa Dubia', 4, 1, 25000),

('P', 'Lampu UVA/UVB 100W', 2, 1, 185000),
('P', 'Cocopeat Substrate 1Kg', 4, 1, 15000),
('P', 'Canister Filter Aquascape', 2, 1, 450000),
('P', 'Thermometer / Hygrometer Digital', 2, 1, 35000),
('P', 'Kalsium Bubuk Reptil D3', 3, 1, 80000);

SELECT * FROM barang;

INSERT INTO margin_penjualan (persen, STATUS, iduser) VALUES 
(26.0, 1, 1), 
(36.5, 1, 1), 
(21.0, 1, 1); 
--- includes tax 11%  cries)

SELECT * FROM margin_penjualan;


----------- sy coba bikin stored procedures i got intrigued, wish me luck-----------
-- coba vendor dulu kalik
DELIMITER $$ 

CREATE PROCEDURE sp_insert_vendor (
IN p_nama_vendor VARCHAR(100),
IN p_badan_hukum CHAR(1),
IN p_status CHAR(1)
)
BEGIN INSERT INTO vendor (nama_vendor, badan_hukum, STATUS)
VALUES (p_nama_vendor, p_badan_hukum, p_status);
END$$

CREATE PROCEDURE sp_update_vendor(
IN p_idvendor INT,
IN p_nama_vendor VARCHAR(100),
IN p_badan_hukum CHAR(1),
IN p_status CHAR(1)
)
BEGIN
UPDATE vendor 
SET nama_vendor = p_nama_vendor, 
badan_hukum = p_badan_hukum, 
status = p_status
WHERE idvendor = p_idvendor;
END$$

CREATE PROCEDURE sp_delete_vendor(
IN p_idvendor INT
)
BEGIN
DELETE FROM vendor WHERE idvendor = p_idvendor;
END$$

DELIMITER ;


-----------------------VIEWEWWEWEW--------------------

CREATE OR REPLACE VIEW view_role AS 
SELECT idrole AS 'no', nama_role 
FROM role;

CREATE OR REPLACE VIEW view_satuan AS
SELECT idsatuan AS 'no' , nama_satuan AS nama, 
CASE 
WHEN STATUS = 1 THEN 'Active'
ELSE 'INACTIVE'
END AS status
FROM satuan;


CREATE OR REPLACE VIEW view_vendor AS
SELECT idvendor AS 'no', nama_vendor, badan_hukum,
CASE 
WHEN STATUS = 'A' THEN 'Active'
ELSE 'INACTIVE'
END AS status
FROM vendor;


CREATE VIEW view_user AS
SELECT u.iduser AS 'no', u.username, r.nama_role
FROM USER u
JOIN role r ON u.idrole = r.idrole;


CREATE OR REPLACE VIEW view_barang AS
SELECT b.idbarrang AS 'no', b.jenis, b.nama, s.nama_satuan, b.harga,
CASE 
WHEN b.STATUS = 1 THEN 'Active'
ELSE 'INACTIVE'
END AS status
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan;


CREATE OR REPLACE VIEW view_margin AS
SELECT m.idmargin_penjualan AS 'no', m.persen, u.username AS created_by, m.created_at,
CASE 
WHEN m.STATUS = 1 THEN 'Active'
ELSE 'INACTIVE'
END AS status
FROM margin_penjualan m
JOIN USER u ON m.iduser = u.iduser;


SELECT * FROM view_barang;


CREATE OR REPLACE VIEW view_pengadaan AS
SELECT 
    p.idpengadaan AS 'no',
    p.timestamp AS tanggal,
    v.nama_vendor,
    u.username AS dibuat_oleh,
    p.subtotal_nilai,
    p.ppn,
    p.total_nilai,
    CASE 
        WHEN p.status = 'P' THEN 'Pending'
        WHEN p.status = 'C' THEN 'Canceled'
        WHEN p.status = 'F' THEN 'Finished'
    END AS status
FROM pengadaan p
JOIN vendor v ON p.vendor_idvendor = v.idvendor
JOIN USER u ON p.user_iduser = u.iduser;


CREATE OR REPLACE VIEW view_penerimaan AS
SELECT 
    pen.idpenerimaan AS 'no',
    pen.created_at AS tanggal_terima,
    pen.idpengadaan AS no_pengadaan,
    u.username AS diterima_oleh,
    CASE 
        WHEN pen.status = 'P' THEN 'Pending'
        WHEN pen.status = 'C' THEN 'Canceled'
        WHEN pen.status = 'F' THEN 'Finished'
    END AS status
FROM penerimaan pen
JOIN USER u ON pen.iduser = u.iduser;


CREATE OR REPLACE VIEW view_penjualan AS
SELECT 
    pj.idpenjualan AS 'no',
    pj.created_at AS tanggal,
    u.username AS kasir,
    pj.subtotal_nilai,
    pj.ppn,
    pj.total_nilai,
    m.persen AS margin_persen
FROM penjualan pj
JOIN USER u ON pj.iduser = u.iduser
JOIN margin_penjualan m ON pj.idmargin_penjualan = m.idmargin_penjualan;


CREATE OR REPLACE VIEW view_retur AS
SELECT 
    r.idretur AS 'no',
    r.created_at AS tanggal,
    r.idpenerimaan AS no_penerimaan,
    u.username AS diproses_oleh
FROM retur r
JOIN USER u ON r.iduser = u.iduser;

CREATE OR REPLACE VIEW view_detail_pengadaan AS
SELECT 
    dp.iddetail_pengadaan AS 'no_detail',
    dp.idpengadaan AS 'no_pengadaan',
    b.idbarrang AS 'idbarang',
    b.nama AS nama_barang,
    b.jenis,
    dp.jumlah AS jumlah_order,
    dp.harga_satuan,
    dp.sub_total
FROM detail_pengadaan dp
JOIN barang b ON dp.idbarang = b.idbarrang ;

CREATE OR REPLACE VIEW view_detail_penerimaan AS
SELECT 
    dpn.iddetail_penerimaan AS 'no_detail_terima',
    dpn.idpenerimaan AS 'no_penerimaan',
    pn.idpengadaan AS 'no_pengadaan',
    b.idbarrang AS 'idbarang',
    b.nama AS nama_barang,
    dpn.jumlah_terima,
    dpn.harga_satuan_terima,
    dpn.sub_total_terima
FROM detail_penerimaan dpn
JOIN penerimaan pn ON dpn.idpenerimaan = pn.idpenerimaan
JOIN barang b ON dpn.barang_idbarang = b.idbarrang ;

CREATE OR REPLACE VIEW view_kartu_stok AS
SELECT 
    k.idkartu_stok AS 'no',
    k.created_at AS tanggal,
    k.idbarang,
    b.nama AS nama_barang,
    k.jenis_transaksi,
    CASE 
        WHEN k.jenis_transaksi = 'I' THEN 'Masuk (IN)'
        WHEN k.jenis_transaksi = 'O' THEN 'Keluar (OUT)'
        ELSE k.jenis_transaksi
    END AS label_transaksi,
    k.masuk,
    k.keluar,
    k.stock AS saldo_stok,
    k.idtransaksi AS no_transaksi_ref
FROM kartu_stok k
JOIN barang b ON k.idbarang = b.idbarrang;

CREATE OR REPLACE VIEW view_barang AS
SELECT 
    b.idbarrang AS 'no', 
    b.jenis, 
    b.nama, 
    s.nama_satuan, 
    b.harga,
    fn_get_current_stock(b.idbarrang) AS stok,
    CASE 
        WHEN b.STATUS = 1 THEN 'Active'
        ELSE 'INACTIVE'
    END AS status
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan;

----------------------------------------------------------------------------------

DELIMITER $$

DROP FUNCTION IF EXISTS fn_get_current_stock$$
CREATE FUNCTION fn_get_current_stock(p_idbarang INT)
RETURNS INT
DETERMINISTIC
BEGIN 
DECLARE v_stock INT DEFAULT 0;

SELECT stock INTO v_stock
FROM kartu_stok
WHERE idbarang = p_idbarang
ORDER BY idkartu_stok DESC
LIMIT 1;
RETURN COALESCE(v_stock, 0);
END$$

DROP FUNCTION IF EXISTS fn_calculate_ppn$$
CREATE FUNCTION fn_calculate_ppn(p_subtotal INT)
RETURNS INT 
DETERMINISTIC
BEGIN
RETURN ROUND(p_subtotal * 0.11);
END$$

DROP FUNCTION IF EXISTS fn_total_pembelian_vendor$$
CREATE FUNCTION fn_total_pembelian_vendor(p_idvendor INT)
RETURNS INT 
DETERMINISTIC
BEGIN
DECLARE v_total INT DEFAULT 0;
SELECT SUM(total_nilai) INTO v_total
FROM pengadaan
WHERE vendor_idvendor = p_idvendor AND STATUS = 'F' ;
RETURN COALESCE(v_total, 0);
END$$


DELIMITER ;


------------trigur
DELIMITER $$

DROP TRIGGER IF EXISTS trg_after_penerimaan_insert$$
CREATE TRIGGER trg_after_penerimaan_insert
AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
DECLARE v_prev_stock INT;
SET v_prev_stock = fn_get_current_stock(NEW.barang_idbarang);
INSERT INTO kartu_stok (jenis_transaksi, masuk,keluar,stock,idtransaksi,idbarang)
VALUES ( 
'I', NEW.jumlah_terima, 0, v_prev_stock + NEW.jumlah_terima, NEW.idpenerimaan, NEW.barang_idbarang
);
END$$

DROP TRIGGER IF EXISTS tgr_after_penjualan_insert$$
CREATE TRIGGER tgr_after_penjualan_insert
AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
DECLARE v_prev_stock INT;
SET V_prev_stock = fn_get_current_stock(NEW.idbarang);
INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock,idtransaksi, idbarang)
VALUES (
'O', 0, New.jumlah, v_prev_stock - NEW.jumlah, NEW.Penjualan_idPenjualan, NEW.idbarang
);
END$$


DROP TRIGGER IF EXISTS trg_after_retur_insert$$
CREATE TRIGGER trg_after_retur_insert
AFTER INSERT ON detail_retur
FOR EACH ROW
BEGIN
DECLARE v_prev_stock INT;
DECLARE V_idbarang INT;

SELECT barang_idbarang INTO v_idbarang
FROM detail_penerimaan
WHERE iddetail_penerimaan = NEW.iddetail_penerimaan;
SET v_prev_stock = fn_get_current_stock(v_idbarang);
INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
VALUES (
'O', 0, NEW.jumlah, v_prev_stock - NEW.jumlah, NEW.idretur, v_idbarang
);
END$$



DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_checkout_single_item$$
CREATE PROCEDURE sp_checkout_single_item (
IN p_iduser INT, IN p_idmargin INT, IN p_barang INT, IN p_jumlah INT, IN p_harga_satuan INT
)
BEGIN 
DECLARE v_current_stock INT;
DECLARE v_subtotal INT;
DECLARE v_ppn INT;
DECLARE v_total INT;
DECLARE v_idpenjualan INT;

DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
ROLLBACK;
RESIGNAL;
END;

START TRANSACTION;
SET v_current_stock = fn_get_current_stock(p_idbarang);
IF v_current_stock < p_jumlah THEN
SIGNAL SQLSTATE '45000'
SET MESSAGE_TEXT = 'Transaction cancelled: Insufficient stock available!';
END IF;

SET v_subtotal = p_harga_satuan * p_jumlah;
SET v_ppn = fn_calculate_ppn(v_subtotal);
SET v_total = v_subtotal + v_ppn;

INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai,iduser, idmargin_penjualan)
VALUES (v_subtotal, v_ppn, v_total, p_iduser, p_idmargin);
SET v_idpenjualan = LAST_INSERT_ID();

INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
VALUES (p_harga_satuan, p_jumlah, v_subtotal, v_idpenjualan, p_idbarang);
COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_proses_penerimaan$$
CREATE PROCEDURE sp_proses_penerimaan (
    IN p_idpengadaan BIGINT,IN p_iduser INT,IN p_idbarang INT,IN p_jumlah_terima INT,IN p_harga_satuan INT
)
BEGIN
    DECLARE v_ordered_qty INT;
    DECLARE v_ordered_item INT;
    DECLARE v_idpenerimaan BIGINT;
    DECLARE v_subtotal INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    
    SELECT idbarang, jumlah INTO v_ordered_item, v_ordered_qty
    FROM detail_pengadaan 
    WHERE idpengadaan = p_idpengadaan 
    LIMIT 1;

    IF v_ordered_item IS NULL THEN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Error: Selected Pengadaan order has no detail items!';
    END IF;

    IF p_idbarang != v_ordered_item THEN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Error: Received item does not match the item ordered in this Pengadaan!';
    END IF;

    
    IF p_jumlah_terima > v_ordered_qty THEN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Error: Quantity received exceeds the ordered quantity!';
    END IF;

    SET v_subtotal = p_jumlah_terima * p_harga_satuan;

    
    INSERT INTO penerimaan (status, idpengadaan, iduser)
    VALUES ('F', p_idpengadaan, p_iduser);

    SET v_idpenerimaan = LAST_INSERT_ID();

    INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
    VALUES (v_idpenerimaan, p_idbarang, p_jumlah_terima, p_harga_satuan, v_subtotal);

    UPDATE pengadaan 
    SET status = 'F' 
    WHERE idpengadaan = p_idpengadaan;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS sp_ajukan_retur$$
CREATE PROCEDURE sp_ajukan_retur (
    IN p_idpenerimaan BIGINT,IN p_iddetail_penerimaan BIGINT,IN p_iduser INT,IN p_jumlah_retur INT,IN p_alasan VARCHAR(200)
)
BEGIN
    DECLARE v_received_qty INT;
    DECLARE v_idretur BIGINT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    ROLLBACK;
    RESIGNAL;
    END;

    START TRANSACTION;

    
    SELECT jumlah_terima INTO v_received_qty
    FROM detail_penerimaan 
    WHERE iddetail_penerimaan = p_iddetail_penerimaan 
    LIMIT 1;

    IF v_received_qty IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Error: Selected Penerimaan detail record does not exist!';
    END IF;

    IF p_jumlah_retur > v_received_qty THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Error: Cannot return more items than were originally received!';
    END IF;

 
    INSERT INTO retur (idpenerimaan, iduser)
    VALUES (p_idpenerimaan, p_iduser);

    SET v_idretur = LAST_INSERT_ID();

    INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan)
    VALUES (p_jumlah_retur, p_alasan, v_idretur, p_iddetail_penerimaan);

    COMMIT;
END$$


DELIMITER ;


DELIMITER $$


DROP PROCEDURE IF EXISTS sp_create_pengadaan_multi$$
CREATE PROCEDURE sp_create_pengadaan_multi (
    IN p_iduser INT,
    IN p_idvendor INT,
    IN p_items_json JSON -- Format: '[{"idbarang": 1, "jumlah": 5, "harga": 150000}, {"idbarang": 2, "jumlah": 10, "harga": 25000}]'
)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_idbarang INT;
    DECLARE v_jumlah INT;
    DECLARE v_harga INT;
    DECLARE v_subtotal INT DEFAULT 0;
    DECLARE v_grand_subtotal INT DEFAULT 0;
    DECLARE v_ppn INT;
    DECLARE v_total INT;
    DECLARE v_idpengadaan BIGINT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SET v_count = JSON_LENGTH(p_items_json);

    IF v_count = 0 OR v_count IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cannot create Pengadaan with zero detail items!';
    END IF;

    -- Calculate Header Totals
    WHILE i < v_count DO
        SET v_jumlah = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].jumlah'))) AS UNSIGNED);
        SET v_harga  = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].harga'))) AS UNSIGNED);
        SET v_grand_subtotal = v_grand_subtotal + (v_harga * v_jumlah);
        SET i = i + 1;
    END WHILE;

    SET v_ppn = fn_calculate_ppn(v_grand_subtotal);
    SET v_total = v_grand_subtotal + v_ppn;

    -- Insert 1 Header Row
    INSERT INTO pengadaan (user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
    VALUES (p_iduser, 'P', p_idvendor, v_grand_subtotal, v_ppn, v_total);

    SET v_idpengadaan = LAST_INSERT_ID();

    -- Insert N Detail Rows
    SET i = 0;
    WHILE i < v_count DO
        SET v_idbarang = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].idbarang'))) AS UNSIGNED);
        SET v_jumlah   = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].jumlah'))) AS UNSIGNED);
        SET v_harga    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].harga'))) AS UNSIGNED);
        SET v_subtotal = v_harga * v_jumlah;

        INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
        VALUES (v_harga, v_jumlah, v_subtotal, v_idbarang, v_idpengadaan);

        SET i = i + 1;
    END WHILE;

    COMMIT;
END$$



DROP PROCEDURE IF EXISTS sp_proses_penerimaan_multi$$
CREATE PROCEDURE sp_proses_penerimaan_multi (
    IN p_idpengadaan BIGINT,
    IN p_iduser INT,
    IN p_items_json JSON -- Format: '[{"idbarang": 1, "jumlah_terima": 5, "harga": 150000}, ...]'
)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_idbarang INT;
    DECLARE v_jumlah_terima INT;
    DECLARE v_harga INT;
    DECLARE v_subtotal INT;
    DECLARE v_idpenerimaan BIGINT;
    DECLARE v_ordered_qty INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SET v_count = JSON_LENGTH(p_items_json);

    IF v_count = 0 OR v_count IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cannot receive shipment with empty item list!';
    END IF;

    -- Insert 1 Header Row
    INSERT INTO penerimaan (status, idpengadaan, iduser)
    VALUES ('F', p_idpengadaan, p_iduser);

    SET v_idpenerimaan = LAST_INSERT_ID();

    -- Loop & Insert Detail Rows (Fires Stock IN Trigger per item!)
    WHILE i < v_count DO
        SET v_idbarang      = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].idbarang'))) AS UNSIGNED);
        SET v_jumlah_terima = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].jumlah_terima'))) AS UNSIGNED);
        SET v_harga         = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].harga'))) AS UNSIGNED);

        -- Validate against detail_pengadaan
        SELECT jumlah INTO v_ordered_qty
        FROM detail_pengadaan
        WHERE idpengadaan = p_idpengadaan AND idbarang = v_idbarang
        LIMIT 1;

        IF v_ordered_qty IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Validation Error: Item was not part of the original Pengadaan order!';
        END IF;

        IF v_jumlah_terima > v_ordered_qty THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Validation Error: Quantity received exceeds ordered amount!';
        END IF;

        SET v_subtotal = v_jumlah_terima * v_harga;

        INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
        VALUES (v_idpenerimaan, v_idbarang, v_jumlah_terima, v_harga, v_subtotal);

        SET i = i + 1;
    END WHILE;

    
    UPDATE pengadaan SET status = 'F' WHERE idpengadaan = p_idpengadaan;

    COMMIT;
END$$



DROP PROCEDURE IF EXISTS sp_checkout_penjualan_multi$$
CREATE PROCEDURE sp_checkout_penjualan_multi (
    IN p_iduser INT,
    IN p_idmargin INT,
    IN p_items_json JSON -- Format: '[{"idbarang": 1, "jumlah": 2, "harga": 15000000}, ...]'
)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_idbarang INT;
    DECLARE v_jumlah INT;
    DECLARE v_harga INT;
    DECLARE v_subtotal INT DEFAULT 0;
    DECLARE v_grand_subtotal INT DEFAULT 0;
    DECLARE v_ppn INT;
    DECLARE v_total INT;
    DECLARE v_idpenjualan INT;
    DECLARE v_current_stock INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SET v_count = JSON_LENGTH(p_items_json);

    IF v_count = 0 OR v_count IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Cart is empty!';
    END IF;

    -- Pre-validate stock and calculate totals
    WHILE i < v_count DO
        SET v_idbarang = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].idbarang'))) AS UNSIGNED);
        SET v_jumlah   = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].jumlah'))) AS UNSIGNED);
        SET v_harga    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].harga'))) AS UNSIGNED);

        SET v_current_stock = fn_get_current_stock(v_idbarang);
        IF v_current_stock < v_jumlah THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Checkout Error: Insufficient stock for one or more items in cart!';
        END IF;

        SET v_grand_subtotal = v_grand_subtotal + (v_harga * v_jumlah);
        SET i = i + 1;
    END WHILE;

    SET v_ppn = fn_calculate_ppn(v_grand_subtotal);
    SET v_total = v_grand_subtotal + v_ppn;

    -- Insert 1 Header Row
    INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
    VALUES (v_grand_subtotal, v_ppn, v_total, p_iduser, p_idmargin);

    SET v_idpenjualan = LAST_INSERT_ID();

    -- Loop & Insert Detail Rows (Fires Stock OUT Trigger per item!)
    SET i = 0;
    WHILE i < v_count DO
        SET v_idbarang = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].idbarang'))) AS UNSIGNED);
        SET v_jumlah   = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].jumlah'))) AS UNSIGNED);
        SET v_harga    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_items_json, CONCAT('$[', i, '].harga'))) AS UNSIGNED);
        SET v_subtotal = v_harga * v_jumlah;

        INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
        VALUES (v_harga, v_jumlah, v_subtotal, v_idpenjualan, v_idbarang);

        SET i = i + 1;
    END WHILE;

    COMMIT;
END$$

DELIMITER ;




