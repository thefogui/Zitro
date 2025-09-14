CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  firstname VARCHAR(100),
  lastname VARCHAR(100),
  password VARCHAR(100),
  startdate INT,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0
);

CREATE TABLE department (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0
);

CREATE TABLE company_position (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0
);

CREATE TABLE user_company_position (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userid INT NOT NULL,
  departmentid INT NULL,
  companypositionid INT NULL,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0,
  FOREIGN KEY (userid) REFERENCES user(id),
  FOREIGN KEY (departmentid) REFERENCES department(id),
  FOREIGN KEY (companypositionid) REFERENCES company_position(id)
);

CREATE TABLE app (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  url VARCHAR(255) NOT NULL UNIQUE,
  active TINYINT,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0
);

CREATE TABLE user_app_access (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userid INT,
  appid INT,
  active TINYINT,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0,
  FOREIGN KEY (userid) REFERENCES user(id),
  FOREIGN KEY (appid) REFERENCES app(id)
);

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userid INT,
  active TINYINT,
  timemodified INT,
  modifiedby INT,
  deleted TINYINT DEFAULT 0,
  FOREIGN KEY (userid) REFERENCES user(id)
);

CREATE TABLE user_session (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userid INT NOT NULL,
  jwttoken TEXT NOT NULL,
  createdat INT NOT NULL,
  expiresat INT NOT NULL,
  FOREIGN KEY (userid) REFERENCES user(id)
);