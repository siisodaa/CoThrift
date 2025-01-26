-- Create the database
CREATE DATABASE menowaste;

USE menowaste;

-- 'member' table
CREATE TABLE member (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(255) UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100) UNIQUE NOT NULL,
    school VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- 'items' table
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id VARCHAR(255) UNIQUE,
    name VARCHAR(100) NOT NULL,
    member_id VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    image_url TEXT,
    status TINYINT NOT NULL, 
    FOREIGN KEY (member_id) REFERENCES member(member_id) ON DELETE CASCADE
);

-- 'requests' table
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id VARCHAR(255) UNIQUE,
    member_from VARCHAR(255)NOT NULL, 
    member_to VARCHAR(255) NOT NULL,  
    item_id VARCHAR(255)NOT NULL,
    quantity INT NOT NULL,
    status TINYINT NOT NULL,
    FOREIGN KEY (member_from) REFERENCES member(member_id) ON DELETE CASCADE,
    FOREIGN KEY (member_to) REFERENCES member(member_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);


-- items.status:

-- 0 → Unavailable
-- 1 → Available

-- requests.status:

-- 0 → Pending
-- 1 → Approved
-- 2 → Declined