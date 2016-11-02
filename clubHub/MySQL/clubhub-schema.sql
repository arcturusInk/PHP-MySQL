
DROP DATABASE IF EXISTS Clubhub;
CREATE DATABASE IF NOT EXISTS clubHub;
USE clubHub;
/*

	Project Part 2 SQL 

*/

-- PERSON
create table person(
	pid varchar(30),
	passwd  char(32),
	fname varchar(50),
	lname varchar(100),
	PRIMARY KEY (pid)
);

-- Advisor
create table advisor(
	pid varchar(30),
	phone char(12),
	PRIMARY KEY (pid),
	FOREIGN KEY (pid) REFERENCES person(pid)
);

-- Student
create table student(
	pid varchar(30),
	gender varchar(10),
	class varchar(20),
	PRIMARY KEY (pid),
    FOREIGN KEY (pid) REFERENCES person(pid)
);


-- Club
create table club(
	clubid integer auto_increment,
	cname varchar(100),
	descr varchar(100),
	PRIMARY KEY (clubid)
);

-- Advisor_of
create table advisor_of(
	pid varchar(30),
	clubid integer,
	PRIMARY KEY (pid,clubid),
	FOREIGN KEY (pid) REFERENCES advisor(pid),
	FOREIGN KEY (clubid) REFERENCES club(clubid)
);


-- Event
create table event(
	eid integer auto_increment,
	ename varchar(100),
	description varchar(255),
	edatetime datetime,
	location varchar(100),
	is_public_e boolean,
    sponsored_by integer,	
	PRIMARY KEY (eid),
 	FOREIGN KEY (sponsored_by) REFERENCES club(clubid)
);

-- Keywords
create table keywords(
	topic varchar(30),
	PRIMARY KEY (topic)
);

-- Interested_in
create table interested_in(
	pid varchar(30),
	topic varchar(100),
	PRIMARY KEY (pid,topic),
	FOREIGN KEY (pid) REFERENCES person(pid),
	FOREIGN KEY (topic) REFERENCES keywords(topic)
);


-- Member_of
create table member_of(
	pid varchar(30),
	clubid integer,
	PRIMARY KEY (pid,clubid),
	FOREIGN KEY (pid) REFERENCES student(pid),
	FOREIGN KEY (clubid) REFERENCES club(clubid)
);


-- multivalued attribute representing role in club
create table role_in(
	pid varchar(30),
	clubid integer,
	role varchar(30),
	PRIMARY KEY (pid,clubid,role),
	FOREIGN KEY (pid,clubid) REFERENCES member_of(pid,clubid)
);


-- Sign_up
create table sign_up(
	pid varchar(30),
	eid integer,
	PRIMARY KEY (pid,eid),
	FOREIGN KEY (pid) REFERENCES person(pid),
	FOREIGN KEY (eid) REFERENCES event(eid)
);

-- Sponsored_by (only needed if events can be co-sponsored)
-- create table sponsored_by(
-- 	clubid integer,
-- 	eid integer,
-- 	PRIMARY KEY (clubid, eid),
-- 	FOREIGN KEY (clubid) REFERENCES club(clubid),
-- 	FOREIGN KEY (eid) REFERENCES event(eid)
-- );


-- Club_topics
create table club_topics(
	clubid integer,
	topic varchar(100),
	PRIMARY KEY (clubid, topic),
	FOREIGN KEY (clubid) REFERENCES club(clubid),
	FOREIGN KEY (topic) REFERENCES keywords(topic)
);

-- Comment
create table comment(
	comment_id integer auto_increment,
	commenter varchar(30),
	ctext varchar(255),
	is_public_c boolean,
	PRIMARY KEY (comment_id),
	FOREIGN KEY (commenter) REFERENCES person(pid)
);

-- Event comment
create table event_comment(
	comment_id integer,
	eid integer,
	PRIMARY KEY (comment_id),
	FOREIGN KEY (comment_id) REFERENCES comment(comment_id),
	FOREIGN KEY (eid) REFERENCES event(eid)
);

-- Club comment
create table club_comment(
	comment_id integer,
	clubid integer,
	PRIMARY KEY (comment_id),
	FOREIGN KEY (comment_id) REFERENCES comment(comment_id),
	FOREIGN KEY (clubid) REFERENCES club(clubid)
);




