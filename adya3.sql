
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    course_name VARCHAR(255),
    course_price DECIMAL(10,2),
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

/* Tracks all available courses */
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    old_price DECIMAL(10,2),
    new_price DECIMAL(10,2),
    duration VARCHAR(50),
    image_path VARCHAR(255),
    instructor_name VARCHAR(100) DEFAULT 'Adya3 Solutions'
);

/* Tracks which user bought which course */
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    payment_status ENUM('pending', 'completed') DEFAULT 'pending',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    section_name VARCHAR(255) DEFAULT 'Welcome to the course',
    lesson_title VARCHAR(255) NOT NULL,
    video_url VARCHAR(255),
    duration VARCHAR(50),
    is_preview TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);


ALTER TABLE users 
ADD COLUMN phone VARCHAR(20) DEFAULT '-',
ADD COLUMN occupation VARCHAR(100) DEFAULT '-',
ADD COLUMN bio VARCHAR(2000) DEFAULT '-';

CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contact_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    service VARCHAR(100) NOT NULL,
    message TEXT,
    status ENUM('new', 'contacted', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE users ADD COLUMN role ENUM('student', 'admin') DEFAULT 'student';

/* Quiz Table: Linked to specific courses */
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

/* Q&A Table: Students ask, Admin answers */
CREATE TABLE IF NOT EXISTS course_qa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    user_id INT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    data TEXT NOT NULL,
    last_access INT(11) NOT NULL
);
INSERT INTO `courses` 
(`title`, `category`, `old_price`, `new_price`, `duration`, `image_path`, `instructor_name`) 
VALUES 
('Ultimate Website Designing & Client course in ( Telugu )', 'Wordpress Web Development', 35000.00, 9500.00, '45h', 'assets/images/web-design-course.jpg', 'Adya3 Solutions');

INSERT INTO `lessons` 
(`course_id`, `section_name`, `lesson_title`, `video_url`, `duration`, `is_preview`, `sort_order`) 
VALUES 
/* Section 1 */
(1, '1) Welcome to the Ultimate website designing course', 'Few words about this course', '', '03:10', 1, 1),

/* Section 2 */
(1, '2) Domain & Hosting', 'What is Domain ? How to purchase it ?', '', '07:12', 0, 2),
(1, '2) Domain & Hosting', 'What is Hosting ? How to purchase it ?', '', '11:05', 0, 3),
(1, '2) Domain & Hosting', 'Why I Choose Host Sakthi ?', '', '14:12', 0, 4),
(1, '2) Domain & Hosting', 'How to map the Domain to Hosting ?', '', '03:02', 0, 5),

/* Section 3 */
(1, '3) SSL Certificate', 'How to add SSL on your Domain', '', '04:35', 0, 6),

/* Section 4 */
(1, '4) Mail IDs / Email IDs', 'How to create the Professional / Webmail / Business Email id', '', '04:48', 0, 7),

/* Section 5 */
(1, '5) Maping Addon Domain and Create Subdomains', 'How to Addon Domain in hosting', '', '04:53', 0, 8),
(1, '5) Maping Addon Domain and Create Subdomains', 'How to create Subdomains', '', '03:40', 0, 9),

/* Section 6 */
(1, '6) Important Resources', 'Important Resources ( photos / Videos / Vectors / Icons )', '', '05:45', 0, 10),

/* Section 7 */
(1, '7) Chrome Extensions', 'Most important Chrome Extensions', '', '12:48', 0, 11),

/* Section 8 */
(1, '8) WordPress', 'Word press Introduction', '', '03:54', 0, 12),
(1, '8) WordPress', 'How to install Word press ?', '', '05:18', 0, 13),
(1, '8) WordPress', 'How to Solve SSL Error While install in WordPress?', '', '01:41', 0, 14),

/* Section 9 */
(1, '9) WordPress Website Admin Panel Area', 'Word Press Admin Panel Overview', '', '08:49', 0, 15),
(1, '9) WordPress Website Admin Panel Area', 'Themes & Plugins', '', '09:30', 0, 16),
(1, '9) WordPress Website Admin Panel Area', 'Creating Pages & Website Menus', '', '09:42', 0, 17),
(1, '9) WordPress Website Admin Panel Area', 'Blog Post', '', '09:00', 0, 18),
(1, '9) WordPress Website Admin Panel Area', 'User Roles', '', '19:48', 0, 19),
(1, '9) WordPress Website Admin Panel Area', 'Media, Settings, Permalinks', '', '11:19', 0, 20),
(1, '9) WordPress Website Admin Panel Area', 'Customization', '', '06:02', 0, 21),

/* Section 10 */
(1, '10) Sliders', 'Creating Sliders', '', '25:04', 0, 22),

/* Section 11 */
(1, '11) Page Builder', 'Complete Guide Wp Page Bakery Builder', '', '24:53', 0, 23),

/* Section 12 */
(1, '12) How to Create the Company website in Single Page', 'Website Overview', '', '02:04', 0, 24),
(1, '12) How to Create the Company website in Single Page', 'Install word press on your domain', '', '05:45', 0, 25),
(1, '12) How to Create the Company website in Single Page', 'Install Wp Page Bakery Builder & Required Plugins', '', '02:39', 0, 26),
(1, '12) How to Create the Company website in Single Page', 'Install Theme and Uses', '', '01:55', 0, 27),
(1, '12) How to Create the Company website in Single Page', 'Creating Home Page Design & Menu section', '', '06:12', 0, 28),
(1, '12) How to Create the Company website in Single Page', 'Home page Design Section 1', '', '13:02', 0, 29),
(1, '12) How to Create the Company website in Single Page', 'Home page Design Section 2', '', '17:50', 0, 30),
(1, '12) How to Create the Company website in Single Page', 'Home page Design Section 3', '', '06:35', 0, 31),
(1, '12) How to Create the Company website in Single Page', 'Home page Design Section 4', '', '07:00', 0, 32),
(1, '12) How to Create the Company website in Single Page', 'Home page Design Section 5', '', '08:44', 0, 33),
(1, '12) How to Create the Company website in Single Page', 'Create Footer Section', '', '09:10', 0, 34),
(1, '12) How to Create the Company website in Single Page', 'Creating IDs and Assigning IDs to Menu Section', '', '04:17', 0, 35),
(1, '12) How to Create the Company website in Single Page', 'Final Conclusion', '', '04:24', 0, 36),

/* Section 13 */
(1, '13) Contact Forms', 'How to Create Contact Forms', '', '19:36', 0, 37),
(1, '13) Contact Forms', 'How to check mails', '', '02:18', 0, 38),

/* Section 14 */
(1, '14) How to Create the Company website ( Method 1 )', 'Install Word Press on your Domain', '', '04:26', 0, 39),
(1, '14) How to Create the Company website ( Method 1 )', 'Install Theme & Required Plugins', '', '04:49', 0, 40),
(1, '14) How to Create the Company website ( Method 1 )', 'Theme Options & General Settings', '', '11:38', 0, 41),
(1, '14) How to Create the Company website ( Method 1 )', 'Header & Footer Layout', '', '10:20', 0, 42),
(1, '14) How to Create the Company website ( Method 1 )', 'Elements and all theme options', '', '10:06', 0, 43),
(1, '14) How to Create the Company website ( Method 1 )', 'Home Page Design', '', '22:09', 0, 44),
(1, '14) How to Create the Company website ( Method 1 )', 'How to create About us page', '', '12:31', 0, 45);

UPDATE `users` 
SET `role` = 'admin' 
WHERE `username` = 'ADYA32022';