# The following code fragments contain sql statements used to create a
# database for the Online Library Application. The sql statements below 
# are designed for MySQL, but with minor modifications they should work
# on any sql database. This file is not an executable, each code
# fragment must be copied and pasted to the command line seperately.

# Command-line login

mysql -p -u sample sample
Password: sample

# Creation of "Resource" Table

CREATE TABLE resource(
  resource_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  location VARCHAR(16) DEFAULT "Main St",
  media VARCHAR(16) DEFAULT "Book",
  status VARCHAR(10) DEFAULT "On Shelf",
  subject VARCHAR(16),
  title VARCHAR(100),
  author VARCHAR(50),
  year VARCHAR(4),
  comments VARCHAR(50),
  date_acquired VARCHAR(12),
  isbn VARCHAR(20),
  donated_by VARCHAR(20),
  PRIMARY KEY (resource_id)
);

# Creation of "Loan" table

CREATE TABLE loan(
  loan_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  resource_id INTEGER UNSIGNED NOT NULL,
  date_time DATETIME NOT NULL,
  person_name VARCHAR(32) NOT NULL,
  person_contact_info VARCHAR(32) NOT NULL,
  is_returned TINYINT NOT NULL DEFAULT 0,
  is_lost TINYINT NOT NULL DEFAULT 0,
  comments VARCHAR(50),
  PRIMARY KEY (loan_id)
);

# Importing of the comma delimited text file as resources

LOAD DATA LOCAL INFILE 'sample.csv' INTO TABLE resource
           FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
           LINES TERMINATED BY '\n';
