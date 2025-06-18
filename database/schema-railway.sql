-- Database schema for Indonesian Blog on Railway MySQL
-- Run this in MySQL Workbench connected to your Railway database

-- Create the database (if you haven't already)
-- CREATE DATABASE blog_indonesia;
-- USE blog_indonesia;

-- Table for authors/users
CREATE TABLE IF NOT EXISTS penulis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for categories
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for articles
CREATE TABLE IF NOT EXISTS artikel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    ringkasan TEXT,
    image_url VARCHAR(500),
    penulis_id INT NOT NULL,
    kategori_id INT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (penulis_id) REFERENCES penulis(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Insert default admin user
INSERT IGNORE INTO penulis (username, password, nama, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@blog.com');

-- Insert sample categories
INSERT IGNORE INTO kategori (nama, deskripsi) VALUES 
('Teknologi', 'Artikel tentang teknologi terkini'),
('Lifestyle', 'Tips dan trik kehidupan sehari-hari'),
('Bisnis', 'Dunia bisnis dan entrepreneurship'),
('Pendidikan', 'Artikel seputar pendidikan'),
('Kesehatan', 'Tips kesehatan dan gaya hidup sehat');

-- Insert sample articles
INSERT IGNORE INTO artikel (judul, konten, ringkasan, image_url, penulis_id, kategori_id, status) VALUES 
('Perkembangan AI di Indonesia', 'Artificial Intelligence atau kecerdasan buatan semakin berkembang pesat di Indonesia. Berbagai startup dan perusahaan teknologi mulai mengadopsi teknologi AI untuk meningkatkan efisiensi dan inovasi dalam berbagai sektor.', 'Membahas perkembangan AI di Indonesia', 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=400&fit=crop', 1, 1, 'published'),
('Tips Produktif Bekerja dari Rumah', 'Bekerja dari rumah memerlukan strategi khusus untuk tetap produktif. Berikut adalah beberapa tips yang dapat membantu Anda memaksimalkan produktivitas saat work from home.', 'Tips untuk meningkatkan produktivitas WFH', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=400&fit=crop', 1, 2, 'published'),
('Memulai Bisnis Online di Era Digital', 'Era digital memberikan peluang besar untuk memulai bisnis online. Dengan strategi yang tepat, siapa pun dapat membangun bisnis yang sukses di dunia digital.', 'Panduan memulai bisnis online', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=400&fit=crop', 1, 3, 'published'),
('Pentingnya Literasi Digital', 'Literasi digital menjadi keterampilan wajib di abad 21. Kemampuan untuk memahami dan menggunakan teknologi digital dengan bijak sangat penting untuk masa depan.', 'Mengapa literasi digital penting', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800&h=400&fit=crop', 1, 4, 'published'),
('Olahraga Ringan untuk Kesehatan', 'Olahraga ringan yang dilakukan secara rutin dapat memberikan manfaat besar bagi kesehatan tubuh. Tidak perlu olahraga berat, aktivitas sederhana pun sudah cukup.', 'Tips olahraga ringan sehari-hari', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=400&fit=crop', 1, 5, 'published');
