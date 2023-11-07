<?php
/*
 * Jeans CMS (GPL license)
 * $Id: index.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */
define('_CONF_SELF',basename(__FILE__));
require(dirname(__FILE__).'/config.php');
// Parse skin as the blog mode.
blog::selector();
