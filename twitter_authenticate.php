<?php
/**
 * @package twitter_authenticate
 */
/*
Plugin Name: Twitter user authenticate
Plugin URI: http://waiwaier.com/
Description: only specific twitter users can view some of the articles
Version: 0.2
Author: yylyyl
Author URI: http://waiwaier.com/
*/
if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';

add_filter('the_password_form', 'get_twitter_authenticate_link');

function get_twitter_authenticate_link($form)
{
	global $post;
	if(substr($post->post_password, 0, 7)=='twitter')
	{
		$url = plugin_dir_url( __FILE__ )."auth.php?post_id=".$post->ID;
		$output = "这篇文章受Twitter用户验证保护。只有特定的Twitter用户可以访问。\n<a href=\"$url\">点击这里前往Twitter进行验证</a>";
		return $output;
	}
	else
		return $form;
}



/*
function get_the_password_form() {
      global $post;
      $label = 'pwbox-'.(empty($post->ID) ? rand() : $post->ID);
      $output = '<form action="' . get_option('siteurl') . '/wp-pass.php" method="post">
      <p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
      <p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("Submit") . '" /></p>
      </form>
      ';
      return apply_filters('the_password_form', $output);
 }
 */
?>