<?php
add_action('admin_menu', 'twitter_authenticate_menu');

function twitter_authenticate_menu()
{
	add_submenu_page('options-general.php', 'Twitter user authenticate', 'Twitter user authenticate', 'manage_options',
'twitter_authenticate/admin.php', 'showadminpage');
}

function showadminpage()
{
	if ( isset($_POST['submit']) ) 
	{
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die('No cheating, please!');
		
		$consumer_key = $_POST['consumer_key'];
		$consumer_secret = $_POST['consumer_secret'];
		$username = $_POST['username'];
		$settings = $_POST['settings'];

		update_option('twitter_authenticate_consumer_key', $consumer_key);
		update_option('twitter_authenticate_consumer_secret', $consumer_secret);
		update_option("twitter_authenticate_username", $username);
		update_option("twitter_authenticate_settings", $settings);
		
		echo '<span style="color: red">Options saved!</span>'."<br />\n";
	}else
	{
		$consumer_key = get_option("twitter_authenticate_consumer_key");
		$consumer_secret = get_option("twitter_authenticate_consumer_secret");
		$username = get_option("twitter_authenticate_username");
		$settings = get_option("twitter_authenticate_settings");
	}
	//echo 'Twitter app settings:'."<br />\n";
?>
Steps:<br />
1. Write your article, but don't publish it.<br />
2. Set a password, it should be <span style="color: blue"><i>twitter</i></span><span style="color: red">blablabla</span> (Starting with "twitter", the following can be anything except nothing)<br />
3. Come to this page, fill in the following form.<br />
4. Publish your article.<br />
<br />

<?php
	echo '<form action="" method="post">'."<br />\n";
	echo 'Consumer Key:<input type="text" value="'.$consumer_key.'" name="consumer_key" />'."<br />\n";
	echo 'Consumer Kecret:<input type="text" value="'.$consumer_secret.'" name="consumer_secret" />'."<br />\n";
	echo 'My Twitter Username:<input type="text" value="'.$username.'" name="username" />'."<br />\n";
	echo "<br />\nSettings:<br />\n";
	echo '<textarea name="settings" rows="5" cols="50">'.$settings.'</textarea>'."<br />\n";
	echo 'Format:<br />
&lt;article id&gt;, ifollow[, &lt;forbidden usernames&gt;]<br />
&lt;article id&gt;, user, &lt;allowed usernames&gt;<br />
&lt;article id&gt;, inlist, &lt;list name&gt;<br />
(Spaces are ignored. Forbidden/allowed usernames should be separated by "|".)<br />';
	echo '<input type="submit" name="submit" value="Save" />'."<br />\n";
	echo "</form><br /><br />";

	$settings = str_replace(" ", "", $settings);
	$array = explode("\n", $settings);
	foreach($array as $row)
	{
		$row = trim($row);
		$options = explode(",", $row);
		echo $options[0];
		$post = get_post($options[0]);
		if(!$post)
		{
			echo " <span style=\"color: red\">Article not found!</span><br />\n";
			continue;
		}
		echo " ".$post->post_title." ";
		switch($options[1])
		{
			case "ifollow":  echo "Users <i>".$username."</i> follows";
							 if(isset($options[2]))
							 	echo " except <i>".str_replace("|", ", ", $options[2])."</i>";
							 break;
			case "user":	 echo "Following users: <i>";
							 echo str_replace("|", ", ", $options[2]);
							 echo "</i>";
							 break;
			case "inlist":	 echo "Users in list <i>".$options[2]."</i>";
							 break;
			default:		 echo "<span style=\"color: red\">unknown</span>";
		}
		if(substr($post->post_password, 0, 7)!='twitter')
			echo " <span style=\"color: red\">(Password isn't set correctly)</span>";
		if($post->post_status!="publish")
			echo " <span style=\"color: red\">(Wrong article status)</span>";
		
		echo "<br />\n";
		
	}

}