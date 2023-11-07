<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
CREATE TABLE jeans_login (
	id INTEGER NOT NULL PRIMARY KEY,
	loginname TEXT NOT NULL,
	authority INTEGER NOT NULL DEFAULT '0',
	email TEXT NOT NULL,
	password TEXT NOT NULL DEFAULT '',
	cookie TEXT );
CREATE UNIQUE INDEX jeans_login_loginname ON jeans_login(loginname);
CREATE UNIQUE INDEX jeans_login_email ON jeans_login(email);

CREATE TABLE jeans_ticket (
	memberid INTEGER NOT NULL,
	action TEXT,
	time TEXT NOT NULL,
	ticket TEXT NOT NULL );
CREATE INDEX member_action ON jeans_ticket(memberid,action);

CREATE TABLE jeans_reactivate (
	memberid INTEGER NOT NULL,
	key TEXT NOT NULL DEFAULT '',
	timestamp INTEGER NOT NULL);

INSERT INTO jeans_login (id,loginname,authority,email,password) VALUES (1,<%sql.quote(loginname)%>,255,<%sql.quote(email)%>,<%sql.quote(password)%>);
