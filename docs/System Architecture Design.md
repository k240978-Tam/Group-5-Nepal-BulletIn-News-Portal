# System Architecture Document
## Nepal Bulletin Board Online News Portal

---

## 1. Introduction

This document defines the **technical architecture** of the Nepal Bulletin Board Online News Portal, a full-stack web application designed for digital news publishing, article management, categorization, and content discovery.

The system provides:
- a seamless reading experience for public users
- a structured content management platform for admins and editors

The purpose of this document is to describe:

1. the selected architecture style
2. the technology stack
3. the system components
4. the service interactions
5. the data flow and security design

---

## 2. Architecture Objectives

The system architecture is designed to:

1. Provide a reliable and scalable news publishing platform
2. Support article creation, editing, publishing, and deletion
3. Enable categorization and tagging of content
4. Allow efficient search and filtering
5. Provide secure authentication and role-based access control
6. Maintain modular and manageable system structure
7. Support deployment within the project timeline

---

## 3. Architecture Style

### 3.1 Selected Architecture: Modular Monolithic

The system follows a **modular monolithic architecture**, where:

- the application is deployed as a single system
- internal functionality is divided into modules such as:
  - authentication
  - article management
  - category management
  - search and filtering
  - user management
  - admin dashboard

### 3.2 Justification

This architecture is selected because:

1. It is simple to design and implement within a 12-week project
2. It reduces complexity compared to microservices
3. It is easier to test, debug, and deploy
4. It still allows clear separation of modules
5. It can be scaled or converted to microservices in the future

---

## 4. Choice of Tech Stack

### 4.1 Frontend Technologies

- React.js
- HTML5
- CSS3
- JavaScript
- Bootstrap or Tailwind CSS

**Reason:**  
React provides a dynamic and responsive UI for pages like news feeds, search results, and dashboards.

### 4.2 Backend Technologies

- Node.js
- Express.js

**Reason:**  
Provides efficient API handling, routing, and business logic processing.

### 4.3 Database

- PostgreSQL

**Reason:**  
A relational database is suitable for structured data like users, articles, categories, and tags.

### 4.4 Authentication and Security

- JWT (JSON Web Token)
- bcrypt

**Reason:**  
Ensures secure login, protected routes, and password encryption.

### 4.5 Development and Deployment Tools

- GitHub (version control)
- Postman (API testing)
- VS Code (development)
- Cloud hosting platform

**Deployment Options:**

- Frontend: Vercel / Netlify
- Backend: Render / Railway / AWS
- Database: Managed PostgreSQL

---

## 5. High-Level Architecture

The system follows a **3-tier architecture**:

### 5.1 Presentation Layer

Handles user interaction.

Includes:
- homepage
- news feed
- article detail page
- category pages
- search page
- login page
- admin dashboard

### 5.2 Application Layer

Handles business logic.

Includes:
- authentication
- article CRUD operations
- category and tag management
- search and filtering
- dashboard operations

### 5.3 Data Layer

Handles data storage.

Includes:
- users
- roles
- articles
- categories
- tags
- comments
- activity logs

---

## 6. System Components

### 6.1 Frontend Client

**Responsibilities:**

1. Display articles and categories
2. Enable search and filtering
3. Show article details
4. Provide admin dashboard UI
5. Send API requests to backend

### 6.2 Backend Server

**Responsibilities:**

1. Process client requests
2. Validate data
3. Handle authentication and authorization
4. Execute business logic
5. Communicate with database
6. Return JSON responses

### 6.3 Authentication Module

**Responsibilities:**

1. Login and logout
2. Verify credentials
3. Hash passwords
4. Generate JWT tokens
5. Protect routes
6. Manage roles

**Roles:**
- Admin
- Editor
- Public User

### 6.4 Article Management Module

**Responsibilities:**

1. Create articles
2. Edit articles
3. Delete articles
4. Publish/unpublish articles
5. Manage metadata

### 6.5 Category and Tag Module

**Responsibilities:**

1. Create categories
2. Assign categories to articles
3. Manage tags
4. Support content organization

### 6.6 Search and Filter Module

**Responsibilities:**

1. Search by keywords
2. Filter by category
3. Filter by date
4. Filter by popularity

### 6.7 Admin Dashboard Module

**Responsibilities:**

1. Manage articles
2. Manage categories
3. Manage users
4. Monitor activity

### 6.8 Database Layer

**Core Tables:**

- users
- roles
- articles
- categories
- tags
- article_tags
- comments
- activity_logs

---

## 7. Service Interactions

### 7.1 Authentication Flow

1. User enters email and password
2. Frontend sends request to backend
3. Backend verifies credentials
4. Password checked using bcrypt
5. JWT token generated
6. Token sent to frontend
7. Token used for protected routes

### 7.2 Article Publishing Flow

1. Admin logs in
2. Frontend sends article data
3. Backend verifies token
4. Article validated
5. Data stored in database
6. Dashboard updated

### 7.3 Article Viewing Flow

1. User opens homepage
2. Frontend requests articles
3. Backend fetches data
4. Database returns results
5. Frontend displays articles

### 7.4 Search Flow

1. User enters keyword
2. Frontend sends query
3. Backend searches database
4. Results returned
5. Frontend displays results

### 7.5 Category Browsing Flow

1. User selects category
2. Frontend sends request
3. Backend fetches category articles
4. Results returned
5. Frontend displays content

### 7.6 Dashboard Flow

1. Admin opens dashboard
2. Backend verifies access
3. Data retrieved from database
4. Frontend displays analytics

---

## 8. Data Flow

General data flow:

**User/Admin → Frontend → Backend → Database → Backend → Frontend**

### Examples

#### News Feed
1. User visits homepage
2. Backend fetches articles
3. Frontend displays results

#### Article Creation
1. Admin submits form
2. Backend saves data
3. Confirmation returned

#### Search
1. User enters keyword
2. Backend processes query
3. Results displayed

---

## 9. Database Design Overview

### 9.1 Users Table
- id
- name
- email
- password_hash
- role_id

### 9.2 Roles Table
- id
- role_name

### 9.3 Articles Table
- id
- title
- content
- author_id
- category_id
- publish_date
- status

### 9.4 Categories Table
- id
- name
- description

### 9.5 Tags Table
- id
- name

### 9.6 Article_Tags Table
- article_id
- tag_id

### 9.7 Comments Table
- id
- article_id
- user_id
- comment

### 9.8 Activity Logs Table
- id
- action
- timestamp

---

## 10. API Design

### Authentication APIs
- `POST /api/auth/login`
- `POST /api/auth/register`
- `GET /api/auth/me`

### Article APIs
- `GET /api/articles`
- `GET /api/articles/:id`
- `POST /api/articles`
- `PUT /api/articles/:id`
- `DELETE /api/articles/:id`

### Category APIs
- `GET /api/categories`
- `POST /api/categories`
- `PUT /api/categories/:id`
- `DELETE /api/categories/:id`

### Search APIs
- `GET /api/search?keyword=...`
- `GET /api/articles?category=...`

### Dashboard APIs
- `GET /api/admin/dashboard`

---

## 11. Security Architecture

The system includes:

1. Password hashing using bcrypt
2. JWT authentication
3. Role-based access control
4. Protected admin routes
5. Input validation
6. Secure API communication using HTTPS
7. Token expiration and validation

---

## 12. Deployment Architecture

### Deployment Setup

- Frontend: Vercel / Netlify
- Backend: Render / Railway / AWS
- Database: PostgreSQL

### Benefits

1. Public accessibility
2. Easy deployment
3. Scalable infrastructure
4. Reliable cloud hosting

---

## 13. Architecture Diagram

```text
User/Admin
   ↓
Frontend (React)
   ↓
Backend (Node.js / Express)
   ↓
PostgreSQL Database
   ↑
Response back to Frontend
