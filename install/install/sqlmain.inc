<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
CREATE TABLE jeans_config (
	id INTEGER NOT NULL PRIMARY KEY,
	type TEXT NOT NULL DEFAULT 'global',
	owner TEXT NOT NULL DEFAULT 'jeans',
	name TEXT NOT NULL DEFAULT '',
	contextid INTEGER NOT NULL DEFAULT '0',
	value TEXT NOT NULL DEFAULT '' );
CREATE INDEX jeans_config_context ON jeans_config(type,contextid);
CREATE UNIQUE INDEX jeans_config_uniquekey ON jeans_config(type,name,contextid);

CREATE TABLE jeans_config_desc (
	id INTEGER NOT NULL PRIMARY KEY,
	name TEXT NOT NULL DEFAULT '',
	desc TEXT NOT NULL DEFAULT '',
	type TEXT NOT NULL DEFAULT 'text',
	defvalue TEXT NOT NULL DEFAULT '',
	extra TEXT,
	configtype TEXT NOT NULL DEFAULT 'global',
	owner TEXT NOT NULL DEFAULT 'jeans',
	sequence INTEGER NOT NULL DEFAULT '0' );
CREATE UNIQUE INDEX jeans_config_desc_uniquekey ON jeans_config_desc(name,configtype,owner);

CREATE TABLE jeans_member (
	id INTEGER NOT NULL PRIMARY KEY,
	name TEXT NOT NULL,
	language TEXT NOT NULL,
	flags INTEGER NOT NULL DEFAULT '0',
	xml TEXT NOT NULL DEFAULT '<?xml version="1.0"?><xml></xml>' );

CREATE TABLE jeans_group (
	id INTEGER NOT NULL PRIMARY KEY,
	name TEXT NOT NULL DEFAULT 'NO NAME',
	desc TEXT ,
	gid INTEGER NOT NULL DEFAULT '0',
	sgid INTEGER NOT NULL DEFAULT '0',
	flags INTEGER NOT NULL DEFAULT '0',
	xml TEXT NOT NULL DEFAULT '<?xml version="1.0"?><xml></xml>' );

CREATE TABLE jeans_item (
	id INTEGER NOT NULL PRIMARY KEY,
	author INTEGER NOT NULL DEFAULT '0',
	time TEXT NOT NULL DEFAULT '1970-01-01 00:00:00',
	title TEXT NOT NULL DEFAULT 'NO TITLE',
	body TEXT,
	more TEXT,
	keywords TEXT,
	gid INTEGER NOT NULL DEFAULT '0',
	sgid INTEGER NOT NULL DEFAULT '0',
	flags INTEGER NOT NULL DEFAULT '0',
	xml TEXT NOT NULL DEFAULT '<?xml version="1.0"?><xml></xml>' );

CREATE TABLE jeans_comment (
	id INTEGER NOT NULL PRIMARY KEY,
	itemid INTEGER NOT NULL, body TEXT,
	author INTEGER NOT NULL DEFAULT '0',
	flags INTEGER NOT NULL DEFAULT '0',
	xml TEXT NOT NULL DEFAULT '<?xml version="1.0"?><xml></xml>' );

CREATE TABLE jeans_plugin (
	id TEXT NOT NULL PRIMARY KEY,
	name TEXT NOT NULL,
	desc TEXT,
	author TEXT,
	version TEXT,
	url TEXT,
	filemtime INTEGER,
	sequence INTEGER NOT NULL);

CREATE TABLE jeans_event (
	id INTEGER NOT NULL PRIMARY KEY,
	event TEXT NOT NULL,
	class TEXT NOT NULL);
CREATE UNIQUE INDEX jeans_event_uniquekey ON jeans_event(event,class);

CREATE TABLE jeans_event_group (
	id INTEGER NOT NULL PRIMARY KEY,
	event TEXT NOT NULL,
	eventgroup TEXT NOT NULL);
CREATE UNIQUE INDEX jeans_event_group_uniquekey ON jeans_event_group(event,eventgroup);

CREATE TABLE jeans_binary (
	id INTEGER NOT NULL PRIMARY KEY,
	type TEXT NOT NULL DEFAULT 'media',
	name TEXT NOT NULL,
	contextid INTEGER NOT NULL DEFAULT '0',
	bindata BLOB NOT NULL,
	binsize INTEGER NOT NULL,
	time TEXT NOT NULL DEFAULT '1970-01-01 00:00:00',
	mime TEXT NOT NULL,
	owner TEXT NOT NULL DEFAULT 'jeans',
	flags INTEGER NOT NULL DEFAULT '0',
	xml TEXT NOT NULL DEFAULT '<?xml version="1.0"?><xml></xml>' );
CREATE UNIQUE INDEX jeans_binary_uniquekey ON jeans_binary (type,name,contextid,owner);

CREATE TABLE jeans_log (
	id INTEGER NOT NULL PRIMARY KEY,
	mid INTEGER NOT NULL,
	ip TEXT NOT NULL,
	referer TEXT NOT NULL DEFAULT '',
	time TEXT NOT NULL,
	uri TEXT NOT NULL,
	type TEXT NOT NULL,
	desc TEXT,
	owner TEXT NOT NULL DEFAULT 'jeans');

INSERT INTO jeans_member (id,name,language) VALUES (1,<%sql.quote(name)%>,'<%text(_INSTALL_LANGUAGE)%>');

INSERT INTO jeans_group (id,name,desc,xml) VALUES (1,<%sql.quote(sitename)%>,<%sql.quote(sitename)%>,'<?xml version="1.0"?><xml><url><%text(_CONF_URL_INDEX)%></url></xml>');
INSERT INTO jeans_group (id,name,desc,gid,sgid) VALUES (2,'<%text(_INSTALL_GENERAL)%>','<%text(_INSTALL_GENERAL)%>',1,1);

INSERT INTO jeans_item (author,time,title,body,more,gid,sgid) VALUES (1,'<%text(_NOW)%>','<%text(_INSTALL_ITEM_TITLE)%>','<%text(_INSTALL_ITEM_BODY)%>','<%text(_INSTALL_ITEM_MORE)%>',1,2);

INSERT INTO jeans_config (name,value) VALUES ('SITE_NAME',<%sql.quote(sitename)%>);
INSERT INTO jeans_config (name,value) VALUES ('ADMIN_EMAIL',<%sql.quote(email)%>);
INSERT INTO jeans_config (name,value) VALUES ('DEFAULT_LANGUAGE','<%text(_INSTALL_LANGUAGE)%>');
INSERT INTO jeans_config (name,value) VALUES ('TIMEZONE',<%sql.quote(timezone)%>);
INSERT INTO jeans_config (name,value) VALUES ('DEFAULT_GROUP','1');
INSERT INTO jeans_config (name,value) VALUES ('DEFAULT_SKIN','/default/skin.inc');
INSERT INTO jeans_config (name,value) VALUES ('DEFAULT_ADMIN_SKIN','/admin/skin.inc');
INSERT INTO jeans_config (name,value) VALUES ('URL_INDEX',<%sql.quote(url_index)%>);
INSERT INTO jeans_config (name,value) VALUES ('URL_SKINS',<%sql.quote(url_skins)%>);
INSERT INTO jeans_config (name,value) VALUES ('URL_ADMIN',<%sql.quote(url_admin)%>);
INSERT INTO jeans_config (name,value) VALUES ('URL_PLUGINS',<%sql.quote(url_plugins)%>);
INSERT INTO jeans_config (name,value) VALUES ('COOKIE_PREFIX',<%sql.quote(cookie_prefix)%>);
INSERT INTO jeans_config (name,value) VALUES ('TIMEZONE',<%sql.quote(timezone)%>);

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('SITE_NAME', '_ADMIN_CONF_SITE_NAME', 'text', '', NULL, 'global', 'jeans', '10');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('URL_INDEX', '_ADMIN_CONF_URL_INDEX', 'text', '', NULL, 'global', 'jeans', '20');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('URL_ADMIN', '_ADMIN_CONF_URL_ADMIN', 'text', '', NULL, 'global', 'jeans', '30');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('URL_SKINS', '_ADMIN_CONF_URL_SKINS', 'text', '', NULL, 'global', 'jeans', '40');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('URL_PLUGINS', '_ADMIN_CONF_URL_PLUGINS', 'text', '', NULL, 'global', 'jeans', '50');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('DEFAULT_LANGUAGE', '_ADMIN_CONF_DEFAULT_LANGUAGE', 'select', 'english', 'languagelist', 'global', 'jeans', '60');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('ADMIN_EMAIL', '_ADMIN_CONF_ADMIN_EMAIL', 'text', '', NULL, 'global', 'jeans', '70');

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('DEFAULT_GROUP', '_ADMIN_CONF_DEFAULT_GROUP', 'select', '1', 'grouplist', 'global', 'jeans', '100');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('DEFAULT_GROUP_SKIN', '_ADMIN_CONF_DEFAULT_GROUP_SKIN', 'select', '/default/skin.inc', 'skinlist', 'global', 'jeans', '110');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('DEFAULT_ADMIN_SKIN', '_ADMIN_CONF_DEFAULT_ADMIN_SKIN', 'select', '/admin/skin.inc', 'adminskinlist', 'global', 'jeans', '120');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('DEFAULT_EDITOR', '_ADMIN_SELECT_DEFAULT_EDITOR', 'select', 'default', 'editorlist', 'global', 'jeans', '130');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('TIMEZONE', '_ADMIN_CONF_TIMEZONE', 'text', 'America/Los_Angeles', NULL, 'global', 'jeans', '140');

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('media_settings', '_ADMIN_CONF_MEDIA_SETTINGS', 'separator', '', NULL, 'global', 'jeans', '200');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('DEFAULT_MEDIA_MANAGER', '_ADMIN_SELECT_DEFAULT_MEDIA_MANAGER', 'select', 'default', 'mediamanagerlist', 'global', 'jeans', '210');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('ALLOW_FILE_UPLOAD', '_ADMIN_CONF_ALLOW_FILE_UPLOAD', 'yesno', '1', NULL, 'global', 'jeans', '220');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('ALLOW_FILE_TYPES', '_ADMIN_CONF_ALLOW_FILE_TYPES', 'text', 'txt,tar.gz,jpg,jpeg,gif,mpg,mpeg,avi,mov,mp3,swf,png', NULL, 'global', 'jeans', '230');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('MAX_UPLOAD_SIZE', '_ADMIN_CONF_MAX_UPLOAD_SIZE', 'text', '1048576', 'numeric', 'global', 'jeans', '240');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('MAX_UPLOAD_TOTAL', '_ADMIN_CONF_MAX_UPLOAD_TOTAL', 'text', '1048576', 'numeric', 'global', 'jeans', '250');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('PREFIX_UPLOADED_FILES', '_ADMIN_CONF_PREFIX_UPLOADED_FILES', 'yesno', '1', NULL, 'global', 'jeans', '260');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('THUMBNAIL_SIZE', '_ADMIN_CONF_THUMBNAIL_SIZE', 'text', '240', 'numeric', 'global', 'jeans', '270');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('THUMBNAIL_VIEW_SIZE', '_ADMIN_CONF_THUMBNAIL_VIEW_SIZE', 'text', '120', 'numeric', 'global', 'jeans', '271');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('THUMBNAIL_ADMIN_SIZE', '_ADMIN_CONF_THUMBNAIL_ADMIN_SIZE', 'text', '80', 'numeric', 'global', 'jeans', '272');

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('cookie_settings', '_ADMIN_COOKIE_SETTINGS', 'separator', '', NULL, 'global', 'jeans', '300');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('COOKIE_PREFIX', '_ADMIN_CONF_COOKIE_PREFIX', 'text', '', NULL, 'global', 'jeans', '310');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('COOKIE_DOMAIN', '_ADMIN_CONF_COOKIE_DOMAIN', 'text', '', NULL, 'global', 'jeans', '320');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('COOKIE_PATH', '_ADMIN_CONF_COOKIE_PATH', 'text', '/', NULL, 'global', 'jeans', '330');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('COOKIE_SECURE', '_ADMIN_CONF_COOKIE_SECURE', 'yesno', '0', NULL, 'global', 'jeans', '340');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('SECURE_COOKIE_KEY', '_ADMIN_CONF_SECURE_COOKIE_KEY', 'select', '24', '0|0|8|8|16|16|24|24|32|32', 'global', 'jeans', '350');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('LASTVISIT', '_ADMIN_CONF_LASTVISIT', 'yesno', '0', NULL, 'global', 'jeans', '360');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('COOKIE_LIFETIME', '_ADMIN_CONF_COOKIE_LIFETIME', 'yesno', '0', NULL, 'global', 'jeans', '370');

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('member_settings', '_ADMIN_CONF_MEMBER_SETTINGS', 'separator', '', NULL, 'global', 'jeans', '400');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('PROTECT_MEMBER_NAMES', '_ADMIN_CONF_PROTECT_MEMBER_NAMES', 'yesno', '1', NULL, 'global', 'jeans', '410');

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('url', '_ADMIN_GROUP_URL_DESC', 'text', '', NULL, 'group', 'jeans', '10');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('group_skin', '_ADMIN_GROUP_SKIN', 'select', '_CONF_DEFAULT_GROUP_SKIN', 'skinlist', 'group', 'jeans', '20');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('comments_enabled', '_ADMIN_GROUP_COMMENTS_ENABLED', 'yesno', '1', NULL, 'group', 'jeans', '30');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('comments_non_member', '_ADMIN_GROUP_COMMENTS_NON_MEMBER', 'yesno', '1', NULL, 'group', 'jeans', '40');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('comments_require_email', '_ADMIN_GROUP_COMMENTS_REQUIRE_EMAIL', 'yesno', '0', NULL, 'group', 'jeans', '50');

INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('web', '_ADMIN_MEMBERINFO_WEB', 'text', '', NULL, 'member_guest', 'jeans', '10');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('email', '_ADMIN_MEMBERINFO_EMAIL', 'text', '', NULL, 'member_guest', 'jeans', '20');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('admin_skin', '_ADMIN_MEMBERINFO_ADMIN_SKIN', 'select', '_CONF_DEFAULT_ADMIN_SKIN', 'adminskinlist', 'member_guest', 'jeans', '30');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('editor', '_ADMIN_SELECT_EDITOR_USED', 'select', '_CONF_DEFAULT_EDITOR', 'editorlist', 'member', 'jeans', '40');
INSERT INTO jeans_config_desc (name, desc, type, defvalue, extra, configtype, owner, sequence) VALUES ('media_manager', '_ADMIN_SELECT_MEDIA_MANAGER_USED', 'select', '_CONF_DEFAULT_MEDIA_MANAGER', 'mediamanagerlist', 'member', 'jeans', '50');


