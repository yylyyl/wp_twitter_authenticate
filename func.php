<?php
function can_view_post(&$connection, $id, $selfname, $username)
{
	$settings = get_auth_type($id);
	switch($settings[1])
	{
		case "ifollow": $forbidden_user_array = explode("|", strtolower($settings[2]));
						if(in_array(strtolower($username),$forbidden_user_array))
						{
							wlog(date("Y-n-j H:i:s")." ".$_SERVER['REMOTE_ADDR']." post=".$id." auth_type=ifollow username=".$username." result=forbidden\n");
							return false;
						}
						$t_follow=$connection->get('friendships/show', array('target_screen_name' => $selfname, 'source_screen_name' => $username));
						$result = $t_follow->relationship->target->following;
						$resultstr = $result?"ok":"no";
						wlog(date("Y-n-j H:i:s")." ".$_SERVER['REMOTE_ADDR']." post=".$id." auth_type=ifollow username=".$username." result=".$resultstr."\n");
						return $result;
						
		case "user":	$allow_user_array = explode("|", strtolower($settings[2]));
						$result = in_array(strtolower($username), $allow_user_array);
						$resultstr = $result?"ok":"no";
						wlog(date("Y-n-j H:i:s")." ".$_SERVER['REMOTE_ADDR']." post=".$id." auth_type=user username=".$username." result=".$resultstr."\n");
						return $result;
						
		case "inlist":	$result=false;
						$next=-1;
						while($next!=0 && $connection->http_code==200)
						{
							$in_list=$connection->get('lists/memberships',array('cursor'=>$next));
							foreach($in_list->lists as $row)
							{
								if(strtolower("@".$selfname."/".$settings[2])==strtolower($row->full_name))
								{
									$result=true;
									break;
								}
							}
							$next=$in_list->next_cursor;
						}
						$resultstr = $result?"ok":"no";
						wlog(date("Y-n-j H:i:s")." ".$_SERVER['REMOTE_ADDR']." post=".$id." auth_type=inlist username=".$username." result=".$resultstr."\n");
						return $result;
						
		default:		wlog(date("Y-n-j H:i:s")." ".$_SERVER['REMOTE_ADDR']." post=".$id." auth_type=unknown username=".$username." result=no\n");
						return false;
		
	}
}

function get_auth_type($id)
{
	$string = get_option("twitter_authenticate_settings");
	$string = str_replace(" ", "", $string);
	$array = explode("\n", $string);
	foreach($array as $row)
	{
		$row = trim($row);
		$result = explode(",", $row);
		if($result[0]==$id)
			return $result;
	}
	return false;
}

function wlog($string)
{
	$file=fopen("log.txt","a");
	fwrite($file, $string);
	fclose($file);
}