<?php
/*
 * Jeans CMS (GPL license)
 * $Id: english.php 295 2010-10-18 19:42:49Z kmorimatsu $
 */

define('_INSTALL_LANGUAGE',substr(basename(__FILE__),0,-4));

define('_INSTALL_WELCOME','Welcome to Jeans CMS world!');
define('_INSTALL_SELECT_LANGUAGE','Please select the language to be used on Jeans CMS.');
define('_INSTALL_CONTINUE','Continue');
define('_INSTALL_INSTALL','Install');
define('_INSTALL_SET_PERMISSION','Before installing Jeans CMS, please confirm that the permission of sqlite directory is writable value (for example, 777).');
define('_INSTALL_PERMISSION_OK','The permission of sqlite directory is correctly set, probably.');
define('_INSTALL_PERMISSION_NG','The permission of sqlite directory may not be correctly set.');
define('_INSTALL_INPUT_INFORMATION','Please input the information required for installing Jeans CMS.');
define('_INSTALL_SITE_NAME','Site Name');
define('_INSTALL_TIME_ZONE','Time Zone');
define('_INSTALL_YOUR_EMAIL','Your E-mail');
define('_INSTALL_YOUR_LOGINNAME','Your login name (alphabetic, non-public)');
define('_INSTALL_YOUR_NAME','Your name (public)');
define('_INSTALL_PASSWORD','Password');
define('_INSTALL_PASSWORD_AGAIN','Password (again to confirm)');

define('_INSTALL_GENERAL','General');
define('_INSTALL_ITEM_TITLE','Welcome to Jeans CMS version '._JEANS_VERSION.'.');
define('_INSTALL_ITEM_BODY','This is the first post on your Jeans CMS. Jeans offers you the building blocks you need to create a web presence.
Whether you want to create a personal blog, a family page, or an online business site, Jeans CMS can help you achieve your goals.');
define('_INSTALL_ITEM_MORE','<br /><br />Though you can delete this entry, it will eventually scroll off the main page as you add content to your site.
Add your comments while you learn to work with Jeans CMS, or bookmark this page so you can come back to it when you need to.');

define('_INSTALL_CONGRATULATIONS','Congratulations!');
define('_INSTALL_DONE','The instration of Jeans CMS has been completed.');
define('_INSTALL_GOTO_SITE','To see the constructed site, please click following link.');
define('_INSTALL_GOTO_ADMIN','To configure site settings, please click following link.');

define('_INSTALL_FAILED','Instration failed');
define('_INSTALL_FAILED_UNFORTUNATELY','Unfortunately, the instration of Jeans CMS failed.');
define('_INSTALL_FAILED_DESCRIPTION','Before re-installing Jeans CMS, please check the permission of sqlite directory again.
If needed, please delete the files, .htdbsqlite and .htdblogin in sqlite directory.');
