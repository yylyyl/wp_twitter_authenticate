<?php
require_once(dirname(__FILE__)."/../../../wp-load.php");
require_once(dirname(__FILE__)."/../../../wp-includes/class-phpass.php");
date_default_timezone_set('Asia/Chongqing');
define('CONSUMER_KEY', get_option("twitter_authenticate_consumer_key") );
define('CONSUMER_SECRET', get_option("twitter_authenticate_consumer_secret") );
define('OAUTH_CALLBACK', get_bloginfo('wpurl').'/wp-content/plugins/twitter_authenticate/callback.php');
