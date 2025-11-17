CREATE DATABASE QuanLySinhVienTNT;

USE QuanLySinhVienTNT;

CREATE TABLE user
(
    user_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL ,
    gender CHAR(1) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(255) NOT NULL,
    citizen_id VARCHAR(15) NOT NULL,
    image VARCHAR(255) NULL
);

CREATE TABLE user_role
(
    user_id BIGINT NOT NULL,
    role_id INT  NOT NULL,
    PRIMARY KEY (user_id, role_id)
);

CREATE TABLE role
(
    role_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    role_name VARCHAR(30) NOT NULL
);

CREATE TABLE user_account
(
    account_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_id BIGINT NOT NULL
);

CREATE TABLE course
(
    course_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    course_background VARCHAR(255),
    course_code VARCHAR(10) NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    course_description TEXT NOT NULL,
    teacher_id BIGINT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status CHAR(1) NOT NULL
);

CREATE TABLE course_schedule
(
  course_schedule_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  course_id INT NOT NULL,
  day_of_week VARCHAR(1) NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL
);

CREATE TABLE course_member
(
  member_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  course_id INT NOT NULL,
  student_id BIGINT NOT NULL
);

CREATE TABLE grade_column
(
  column_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  course_id INT NOT NULL,
  grade_column_name VARCHAR(255) NOT NULL,
  proportion INT NOT NULL
);

CREATE TABLE grade
(
  grade_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  column_id INT NOT NULL,
  member_id INT NOT NULL,
  score DECIMAL
);

CREATE TABLE post
(
    post_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    course_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE TABLE topics (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    title_topic VARCHAR(255) NOT NULL,
    course_id INT,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE course_contents (
    contents_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    title_content VARCHAR(255) NOT NULL,
    content_type VARCHAR(100) NOT NULL,
    description_content TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES topics(topic_id) ON DELETE CASCADE ON UPDATE CASCADE

);

CREATE TABLE video_contents (
    video_id INT AUTO_INCREMENT PRIMARY KEY,
    course_content_id INT,
    video_url VARCHAR(255) NOT NULL,
    video_size DECIMAL,
    FOREIGN KEY (course_content_id) REFERENCES course_contents(contents_id) ON DELETE CASCADE ON UPDATE CASCADE

);

CREATE TABLE embedded_contents (
    embedded_id INT AUTO_INCREMENT PRIMARY KEY,
    course_content_id INT,
    embed_code TEXT NOT NULL,
    FOREIGN KEY (course_content_id) REFERENCES course_contents(contents_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE file_contents (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    course_content_id INT,
    file_name VARCHAR(255) NOT NULL,
    file_size DECIMAL,
    FOREIGN KEY (course_content_id) REFERENCES course_contents(contents_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE text_contents (
    text_id INT AUTO_INCREMENT PRIMARY KEY,
    course_content_id INT,
    text_content TEXT NOT NULL,
    FOREIGN KEY (course_content_id) REFERENCES course_contents(contents_id) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE user_account
ADD CONSTRAINT fk_user_account_user_id FOREIGN KEY (user_id) REFERENCES user(user_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE user_role
ADD CONSTRAINT fk_user_role_user_id FOREIGN KEY (user_id) REFERENCES user(user_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE user_role
ADD CONSTRAINT fk_user_role_role_id FOREIGN KEY (role_id) REFERENCES role(role_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE course_member
ADD CONSTRAINT fk_course_member_student_id FOREIGN KEY (student_id) REFERENCES user(user_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE course
ADD CONSTRAINT fk_course_teacher_id FOREIGN KEY (teacher_id) REFERENCES user(user_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE course_member
ADD CONSTRAINT fk_course_member_course_id FOREIGN KEY (course_id) REFERENCES course(course_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE grade
ADD CONSTRAINT fk_grade_member_id FOREIGN KEY (member_id) REFERENCES course_member(member_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE course_schedule
ADD CONSTRAINT fk_course_schedule_course_id FOREIGN KEY (course_id) REFERENCES course(course_id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE grade
ADD CONSTRAINT fk_grade_column_id FOREIGN KEY (column_id) REFERENCES grade_column(column_id)
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO user (full_name, date_of_birth, gender, address, phone, email, citizen_id)
VALUES
('Vĩnh Thuận', '2005-10-01', 'M', 'Xã Vĩnh Phương, Thành phố Nha Trang, Khánh Hòa', '0349396534', 'vinhthuan@gmail.com', '056205001946'),
('Trần Ngọc Hân', '2003-07-15', 'F', 'Phường Tân Lập, Thành phố Nha Trang, Khánh Hòa', '0358237456', 'tranngochan@gmail.com', '056205001952'),
('Lê Việt Hùng', '1999-11-02', 'M', 'Phường Phước Long, Thành phố Nha Trang, Khánh Hòa', '0394728165', 'lethanhbinh@gmail.com', '056205001953'),
('Phạm Thị Mỹ Duyên', '2001-04-23', 'F', 'Phường 7, Thành phố Tuy Hòa, Phú Yên', '0387429513', 'phammyduyen@gmail.com', '056205001954'),
('Ngô Đức Toàn', '2002-06-30', 'M', 'Thị trấn Vạn Giã, Huyện Vạn Ninh, Khánh Hòa', '0374638295', 'ngoductoan@gmail.com', '056205001955'),
('Đặng Hoàng Yến', '2004-09-11', 'F', 'Xã Cam Hải Tây, Huyện Cam Lâm, Khánh Hòa', '0362859437', 'danghoangyen@gmail.com', '056205001956'),
('Võ Hữu Tài', '2000-05-09', 'M', 'Phường Ninh Hiệp, Thị xã Ninh Hòa, Khánh Hòa', '0371948256', 'vohuutai@gmail.com', '056205001957'),
('Nguyễn Thị Thu Hà', '2003-02-18', 'F', 'Phường Phú Lâm, Thành phố Tuy Hòa, Phú Yên', '0392748615', 'nguyenthuhuha@gmail.com', '056205001958'),
('Huỳnh Xuân Nam', '2005-12-01', 'M', 'Xã An Phú, Thành phố Tuy Hòa, Phú Yên', '0349396524', 'huynhxuannam@gmail.com', '056205001943'),
('Nguyễn Minh Tài', '2000-01-20', 'M', 'Xã Đại Lãnh, Huyện Vạn Ninh, Khánh Hòa', '0372424264', 'nguyenminhtai@gmail.com', '056205000218');

INSERT INTO role (role_name)
VALUES
('student'),
('teacher'),
('admin');

INSERT INTO user_role (user_id, role_id)
VALUES
('1', '1'),
('2', '1'),
('3', '1'),
('4', '1'),
('5', '1'),
('6', '1'),
('7', '1'),
('8', '1'),
('9', '2'),
('10','3');

INSERT INTO user_account (username, password, user_id)
VALUES
('vinhthuan', '1234a$', '1'),
('tranngochan', '1234a$', '1'),
('leviethung', '1234a$', '1'),
('phamthimyduyen', '1234a$', '1'),
('ngoductoan', '1234a$', '1'),
('danghoangyen', '1234a$', '1'),
('vohuutai', '1234a$', '1'),
('nguyenthithuha', '1234a$', '1'),
('huynhxuannam', '1234a$', '2'),
('nguyenminhtai', '1234a$', '3');

insert into course (course_id, course_code, course_name, course_description, teacher_id, start_date, end_date, status, course_background)
values  (1, 'SOT366', 'Phát triển mã nguồn mở', 'No description', 9, '2025-01-01', '2025-12-12', 'A', '1.jpg'),
        (2, 'SOT357', 'Kiểm thử phần mềm', 'No description', 9, '2025-01-01', '2025-12-12', 'A', '2.jpg'),
        (3, 'SOT344', 'Trí tuệ nhân tạo', 'No description', 9, '2025-01-01', '2025-12-12', 'A', '3.jpg');

INSERT INTO course_schedule (course_id, day_of_week, start_time, end_time)
VALUES
(1, '2', '7:00:00', '8:30:00'),
(2, '2', '9:30:00', '10:20:00'),
(3, '3', '7:00:00', '8:30:00');

INSERT INTO course_member (course_id, student_id)
VALUES
(1, 1),
(2, 1),
(3, 1),
(1, 2),
(2, 2),
(3, 2),
(1, 3),
(2, 3),
(3, 3),
(1, 4),
(2, 4),
(3, 4),
(1, 5),
(2, 5),
(3, 5),
(1, 6),
(2, 6),
(3, 6),
(1, 7),
(2, 7),
(3, 7),
(1, 8),
(2, 8),
(3, 8);
INSERT INTO grade_column (grade_column_name, proportion, course_id)
VALUES
('Quá trình', 20, 1),
('Giữa kỳ', 30, 1),
('Cuối kỳ', 50, 1),
('Quá trình', 40, 2),
('Giữa kỳ', 10, 2),
('Cuối kỳ', 50, 2),
('Quá trình', 30, 3),
('Giữa kỳ', 30, 3),
('Cuối kỳ', 30, 3);

INSERT INTO grade (column_id, member_id, score)
VALUES
(1, 1, 6), (1, 4, 1), (1, 7, 4), (1, 10, 9), (1, 13, 8), (1, 16, 3), (1, 19, 8), (1, 22, 1),
(2, 1, 9), (2, 4, 3), (2, 7, 0), (2, 10, 0), (2, 13, 2), (2, 16, 8), (2, 19, 8), (2, 22, 10),
(3, 1, 2), (3, 4, 7), (3, 7, 9), (3, 10, 0), (3, 13, 8), (3, 16, 10), (3, 19, 3), (3, 22, 3),

(4, 2, 1), (4, 5, 2), (4, 8, 7), (4, 11, 4), (4, 14, 4), (4, 17, 8), (4, 20, 4), (4, 23, 10),
(5, 2, 2), (5, 5, 10), (5, 8, 2), (5, 11, 2), (5, 14, 8), (5, 17, 8), (5, 20, 6), (5, 23, 10),
(6, 2, 2), (6, 5, 3), (6, 8, 4), (6, 11, 5), (6, 14, 5), (6, 17, 10), (6, 20, 3), (6, 23, 9),

(7, 3, 9), (7, 6, 5), (7, 9, 3), (7, 12, 8), (7, 15, 7), (7, 18, 4), (7, 21, 3), (7, 24, 7),
(8, 3, 1), (8, 6, 10), (8, 9, 1), (8, 12, 8), (8, 15, 6), (8, 18, 6), (8, 21, 8), (8, 24, 3),
(9, 3, 3), (9, 6, 7), (9, 9, 6), (9, 12, 5), (9, 15, 10), (9, 18, 4), (9, 21, 6), (9, 24, 9);