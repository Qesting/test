CREATE TABLE IF NOT EXISTS users (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	username varchar(50) UNIQUE NOT NULL,
	password varchar(255) NOT NULL,
	created_at datetime DEFAULT CURRENT_TIMESTAMP,
	priv int DEFAULT 0
);

CREATE TABLE IF NOT EXISTS module (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name varchar(60) NOT NULL
);

CREATE TABLE IF NOT EXISTS test (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	module_id int NOT NULL,
	name varchar(60) NOT NULL,
	created_at datetime DEFAULT CURRENT_TIMESTAMP,
	owner int,
	time int DEFAULT 30,
	can_take int DEFAULT 1,
	can_laa int DEFAULT 0,
	vert int DEFAULT 0,
	part int DEFAULT 0,

	FOREIGN KEY (module_id) REFERENCES module(id)
	ON DELETE CASCADE,
	FOREIGN KEY (owner) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS question (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	test_id int NOT NULL,
	content varchar(60) NOT NULL,
	quest_type int NOT NULL,
	points int DEFAULT 1,

	ans int,
	ans_text varchar(60),

	img_path varchar(60),

	FOREIGN KEY (test_id) REFERENCES test(id)
	ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS answer (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	quest_id int NOT NULL,
	content varchar(60),
	ans_id int NOT NULL,

	FOREIGN KEY (quest_id) REFERENCES question(id)
	ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS session (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	test_id int NOT NULL,
	code varchar(6) UNIQUE NOT NULL,
	owner int NOT NULL,
	started datetime,
	closed datetime,
	is_open int DEFAULT 0,
	can_laa int DEFAULT 0,
	part int DEFAULT 0,
	
	FOREIGN KEY (test_id) REFERENCES test(id)
	ON DELETE CASCADE,
	FOREIGN KEY (owner) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS grade (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	str varchar(20)
);

INSERT IGNORE INTO grade VALUES ('1', 'niedostateczny'); 
INSERT IGNORE INTO grade VALUES ('2', 'dopuszczający');
INSERT IGNORE INTO grade VALUES ('3', 'dostateczny');
INSERT IGNORE INTO grade VALUES ('4', 'dobry');
INSERT IGNORE INTO grade VALUES ('5', 'bardzo dobry');
INSERT IGNORE INTO grade VALUES ('6', 'celujący');

CREATE TABLE IF NOT EXISTS s_entry (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	ent_id int,
	session_id int NOT NULL,
	name varchar(100),
	class varchar(5),
	perc int NOT NULL,
	grade int NOT NULL,

	FOREIGN KEY (session_id) REFERENCES session(id)
	ON DELETE CASCADE,
	FOREIGN KEY (grade) REFERENCES grade(id)
);

CREATE TABLE IF NOT EXISTS quotes (
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	quote varchar(255) NOT NULL
);