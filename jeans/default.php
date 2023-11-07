<?php
/*
 * Jeans CMS (GPL license)
 * $Id: default.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */
define('_CONF_SELF',basename(__FILE__));
require(dirname(__FILE__).'/../config.php');
// Parse skin as the admin mode.
admin::selector('/admin/adminskin.inc');
