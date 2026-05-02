**Database Design**

System needs ** core tables + relationships**:

**Tables:**

1.  **users**
2.  **articles**
3.  **categories**
4.  **comments / tags**


Creating the Table 
database/schema.sql

-- USERS TABLE  
CREATE TABLE users (  
id SERIAL PRIMARY KEY,  
name VARCHAR(100) NOT NULL,  
email VARCHAR(150) UNIQUE NOT NULL,  
password TEXT NOT NULL,  
role VARCHAR(20) CHECK (role IN ('admin', 'editor', 'user')) DEFAULT 'user',  
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
);  
  
-- CATEGORIES TABLE  
CREATE TABLE categories (  
id SERIAL PRIMARY KEY,  
name VARCHAR(100) UNIQUE NOT NULL  
);  
  
-- ARTICLES TABLE  
CREATE TABLE articles (  
id SERIAL PRIMARY KEY,  
title VARCHAR(255) NOT NULL,  
content TEXT NOT NULL,  
author_id INT REFERENCES users(id) ON DELETE CASCADE,  
category_id INT REFERENCES categories(id),  
status VARCHAR(20) CHECK (status IN ('draft', 'published')) DEFAULT 'draft',  
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
);  
  
-- OPTIONAL  
-- COMMENTS TABLE  
CREATE TABLE comments (  
id SERIAL PRIMARY KEY,  
article_id INT REFERENCES articles(id) ON DELETE CASCADE,  
user_id INT REFERENCES users(id),  
content TEXT NOT NULL,  
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
);



-   Uses **relationships (FKs)** 
-   Uses **constraints (CHECK, UNIQUE)** 
-   Uses **real-world logic (roles, status)** 


**Sample Data**

Create:  
database/seed.sql

INSERT INTO categories (name) VALUES  
('Politics'), ('Sports'), ('Technology');  
  
INSERT INTO users (name, email, password, role) VALUES  
('Admin User', 'admin@test.com', 'hashed_password', 'admin'),  
('Editor User', 'editor@test.com', 'hashed_password', 'editor');  
  
INSERT INTO articles (title, content, author_id, category_id, status)  
VALUES  
('First News', 'This is sample news content', 1, 1, 'published');


**STEP 4: API Specifications**

Create:  
docs/api.md

 **Auth APIs**

POST /api/auth/register  
POST /api/auth/login

**Validation:**

-   Email must be valid
-   Password ≥ 6 characters

**Article APIs**

GET /api/articles  
GET /api/articles/:id  
POST /api/articles  
PUT /api/articles/:id  
DELETE /api/articles/:id

**Validation:**

-   Title cannot be empty
-   Content required
-   Only admin/editor can create

 **Category APIs**

GET /api/categories  
POST /api/categories

 **User APIs**

GET /api/users  
DELETE /api/users/:id
