-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role ENUM('admin', 'editor', 'journalist', 'user') DEFAULT 'user',
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
-- hash: $2y$10$w3G.TqS.L41H1U.R5.YkGu1F4Z1O5vTzL.vM1m/Z1A.K0j3E2R.G2
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Admin User', 'admin@nepalbulletin.com', '$2y$10$w3G.TqS.L41H1U.R5.YkGu1F4Z1O5vTzL.vM1m/Z1A.K0j3E2R.G2', 'admin');
