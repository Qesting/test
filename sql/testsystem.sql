CREATE TABLE IF NOT EXISTS users (
	id INT AUTO_INCREMENT,
	username VARCHAR(50) UNIQUE NOT NULL,
	password VARCHAR(255) NOT NULL,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	priv INT DEFAULT 0,

	PRIMARY KEY (id)
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS module (
	id INT AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,

	PRIMARY KEY (id)
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS test (
	id INT AUTO_INCREMENT,
	module_id INT NOT NULL,
	name VARCHAR(60) NOT NULL,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	owner INT,
	time INT DEFAULT 30,
	can_take INT DEFAULT 1,
	can_laa INT DEFAULT 0,
	vert INT DEFAULT 0,
	part INT DEFAULT 0,

	PRIMARY KEY (id),
	FOREIGN KEY (module_id) REFERENCES module(id)
	ON DELETE CASCADE,
	FOREIGN KEY (owner) REFERENCES users(id)
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS question (
	id INT AUTO_INCREMENT,
	test_id INT NOT NULL,
	content VARCHAR(60) NOT NULL,
	quest_type INT NOT NULL,
	poINTs INT DEFAULT 1,

	ans INT,
	ans_text VARCHAR(60),

	img_path VARCHAR(60),

	PRIMARY KEY (id),
	FOREIGN KEY (test_id) REFERENCES test(id)
	ON DELETE CASCADE
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS answer (
	id INT AUTO_INCREMENT,
	quest_id INT NOT NULL,
	content VARCHAR(60),
	ans_id INT NOT NULL,

	PRIMARY KEY (id),
	FOREIGN KEY (quest_id) REFERENCES question(id)
	ON DELETE CASCADE
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS session (
	id INT AUTO_INCREMENT,
	test_id INT NOT NULL,
	code VARCHAR(6) UNIQUE NOT NULL,
	owner INT NOT NULL,
	started DATETIME,
	closed DATETIME,
	is_open INT DEFAULT 0,
	can_laa INT DEFAULT 0,
	part INT DEFAULT 0,
	
	PRIMARY KEY (id),
	FOREIGN KEY (test_id) REFERENCES test(id)
	ON DELETE CASCADE,
	FOREIGN KEY (owner) REFERENCES users(id)
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS grade (
	id INT AUTO_INCREMENT,
	str VARCHAR(20),

	PRIMARY KEY (id)
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

INSERT IGNORE INTO grade VALUES ('1', 'niedostateczny'); 
INSERT IGNORE INTO grade VALUES ('2', 'dopuszczający');
INSERT IGNORE INTO grade VALUES ('3', 'dostateczny');
INSERT IGNORE INTO grade VALUES ('4', 'dobry');
INSERT IGNORE INTO grade VALUES ('5', 'bardzo dobry');
INSERT IGNORE INTO grade VALUES ('6', 'celujący');

CREATE TABLE IF NOT EXISTS s_entry (
	id INT AUTO_INCREMENT,
	ent_id INT,
	session_id INT NOT NULL,
	name VARCHAR(100),
	class VARCHAR(5),
	perc INT NOT NULL,
	grade INT NOT NULL,

	PRIMARY KEY (id),
	FOREIGN KEY (session_id) REFERENCES session(id)
	ON DELETE CASCADE,
	FOREIGN KEY (grade) REFERENCES grade(id)
); CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci'

CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT,
	quote VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

CREATE TABLE IF NOT EXISTS article (
	id INT AUTO_INCREMENT,
	publication_date DATE,
	title VARCHAR(255),
	summary VARCHAR(2048),
	content TEXT,
	author INT NOT NULL,
	thumbnail VARCHAR(255),
	published SMALLINT,

	PRIMARY KEY (id),
	FOREIGN KEY (author) REFERENCES users(id); 
)