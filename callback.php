<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');
require_once('func.php');

/* If the oauth_token is old redirect to the connect page. */
if( !isset($_SESSION['post_id']) )
{
	header('Location: '.get_bloginfo('wpurl') );
	die();
}
/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  //$_SESSION['status'] = 'verified';

  $t_user = $connection->get('account/verify_credentials');
  //echo $content->screen_name." | ";
  //echo $_SESSION['post_id']." | ";
  $post=get_post($_SESSION['post_id']);
  //echo $post->post_password;
  
  //$t_follow=$connection->get('friendships/show',array('target_screen_name' => get_option("twitter_authenticate_username") ,'source_screen_name' => $content->screen_name));
  //print_r($c2);
  $selfname = get_option("twitter_authenticate_username");
  
  if( strtolower($t_user->screen_name)==strtolower( $selfname ) )
  {
  	wlog(date("Y-n-j H:i:s")." ".$_SERVER['REMOTE_ADDR']." post=".$_SESSION['post_id']." owner\n");
  	$version = get_bloginfo("version");
  	if($version < 3.4)
  		setcookie('wp-postpass_' . COOKIEHASH, $post->post_password, time() + 864000, COOKIEPATH);
  	else
  	{
  		$wp_hasher = new PasswordHash(8, true);
  		$hash = $wp_hasher->HashPassword($post->post_password);
  		setcookie('wp-postpass_' . COOKIEHASH, $hash, time() + 864000, COOKIEPATH);
  	}
  	
  	//the blog owner can view this of course
  }
  elseif( can_view_post($connection, $_SESSION['post_id'], $selfname, $t_user->screen_name) )
  {
  	if($version < 3.4)
  		setcookie('wp-postpass_' . COOKIEHASH, $post->post_password, time() + 864000, COOKIEPATH);
  	else
  	{
  		$wp_hasher = new PasswordHash(8, true);
  		$hash = $wp_hasher->HashPassword($post->post_password);
  		setcookie('wp-postpass_' . COOKIEHASH, $hash, time() + 864000, COOKIEPATH);
  	}
  }
  /*
  if($c2->relationship->target->following || strtolower($content->screen_name)==strtolower( get_option("twitter_authenticate_username") ) )
  	setcookie('wp-postpass_' . COOKIEHASH, $post->post_password, time() + 864000, COOKIEPATH);
  	*/
}
session_destroy();
header('Location: '.get_permalink( $_SESSION['post_id'] ) );
