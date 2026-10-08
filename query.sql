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