-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role ENUM('admin', 'editor', 'journalist', 'user') DEFAULT 'user',
    profile_picture VARCHAR(255) NULL,
    phone_number VARCHAR(20) NULL,
    dob DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIES TABLE
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL
);

-- ARTICLES TABLE
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    summary TEXT NULL,
    image_url VARCHAR(255) NULL,
    image_position ENUM('left', 'center', 'right', 'full') DEFAULT 'center',
    image_caption VARCHAR(255) NULL,
    article_color VARCHAR(7) DEFAULT '#c0392b',
    language ENUM('en', 'ne', 'bilingual') DEFAULT 'en',
    article_type ENUM('standard', 'opinion', 'interview', 'photo_essay', 'breaking') DEFAULT 'standard',
    meta_description TEXT NULL,
    slug VARCHAR(255) UNIQUE NULL,
    is_featured TINYINT(1) DEFAULT 0,
    scheduled_at DATETIME NULL,
    internal_note TEXT NULL,
    ai_summary TEXT NULL,
    author_id INT,
    category_id INT,
    status ENUM('draft', 'pending', 'published', 'rejected') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- COMMENTS TABLE
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT,
    user_id INT,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- TAGS TABLE
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- ARTICLE_TAGS TABLE
CREATE TABLE IF NOT EXISTS article_tags (
    article_id INT,
    tag_id INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- SEED DATA
INSERT IGNORE INTO categories (name, description) VALUES
('Politics', 'Political news from Nepal and around the world.'),
('Business', 'Economic and business updates.'),
('Sports', 'Sports news, scores, and highlights.'),
('Technology', 'Latest in tech and startups.'),
('Entertainment', 'Movies, music, and celebrity news.');

-- Default admin user (password: Admin@123)
-- hash: $2y$10$/9Ddt6OmW0lPzrQYrMsSyeawqi2Q5i7TD/fRhfD6PGNU7JHRt92yy
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Admin User', 'admin@nepalbulletin.com', '$2y$10$/9Ddt6OmW0lPzrQYrMsSyeawqi2Q5i7TD/fRhfD6PGNU7JHRt92yy', 'admin');

-- AUDIT LOGS TABLE
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- SITE SETTINGS TABLE
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NULL
);

-- Seed site settings
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('accent_color', '#c0392b'),
('allow_registration', '1'),
('site_name', 'Nepal Bulletin'),
('site_tagline', 'Delivering the latest news from Nepal and around the world.');

