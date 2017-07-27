<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
	</head>
	<body bgcolor="black">
		<form method="get" action="">
			<div><center>
				
					
					<table>
							<tr>
								<td><font color="white">SteamID / FriendID / customURL:</font><br>
									<input type="text" size="70" name="s" value="<?php echo htmlentities(stripslashes($_GET['s']),ENT_QUOTES); ?>">
								</td>
							</tr>
							<tr>
								<td align="center">
									<input class="button" type="submit" accesskey="s" value="Submit">
								</td>
							</tr>
					</table>
			
			</div>
		</form>

		<?php
		function convert($type,$data){
			switch($type){
				case 'steamid':
				$main='http://steamcommunity.com/profiles/'.bcadd((($data['auth']*2)+$data['server']),'76561197960265728');
				echo '<p><font color=\"#FFFFFF\">FriendID: <a href="'.$main.'" title="Visit Steam Community page" target="iframe"><font color=\"#FFFFFF\">'.bcadd((($data['auth']*2)+$data['server']),'76561197960265728').'</a>';
				break;
				case 'friendid':
				if (substr($data,-1)%2==0) $server=0; else $server=1;
				$auth=bcsub($data,'76561197960265728');
				if (bccomp($auth,'0')!=1) {echo "<p><font color=\"#FFFFFF\">Error: invalid FriendID or SteamID";return;}
				$auth=bcsub($auth,$server);
				$auth=bcdiv($auth,2);
				echo '<p><font color=\"#FFFFFF\">SteamID: STEAM_0:'.$server.':'.$auth;
				break;
			}
		}

		function get_input_type($data){
			$data=strtolower(trim($data));
			if ($data!='') {
				if (strlen($data)>80) return "too long";
				if (substr($data,0,7)=='steam_0') {
					$tmp=explode(':',$data);
					if ((count($tmp)==3) && is_numeric($tmp[1]) && is_numeric($tmp[2])){
						return array('type'=>'steamid','data'=>array('auth'=>$tmp[2],'server'=>$tmp[1]));
					}else{
						return "Error: invalid SteamID";
					}
				}else if ($p=strrpos($data,'/')){
					$tmp=explode('/',$data);
					foreach ($tmp as $item){
						if (is_numeric($item)){
							$a=$item;
							break;
						}
					}
					if ((is_numeric($a)) && (ereg('7656119', $a))) return array('type'=>'friendid','data'=>$a);
					else {
						$xml = @simplexml_load_file($data."?xml=1");
						$steamid64=$xml->steamID64;
						if (!ereg('7656119', $steamid64)) return "Error: invalid link";
						else return array('type'=>'friendid','data'=>$steamid64);
					}
				}else if ((is_numeric($data)) && (ereg('7656119', $a))){
					return array('type'=>'friendid','data'=>$data);
				}else{
					$xml = @simplexml_load_file("http://steamcommunity.com/id/".$data."?xml=1");
					$steamid64=$xml->steamID64;
					if (!ereg('7656119', $steamid64)) return "Error: invalid input";
					else return array('type'=>'friendid','data'=>$steamid64);
				}
			}else{
				return "";
			}
		}
		
		$ret=get_input_type($_GET['s']);
		if ($ret==''){

		}else if (is_string($ret)){			
			echo $ret;
		}else if (is_array($ret)){
			echo convert($ret['type'],$ret['data']);
		}

		?>
	</body>
</html>