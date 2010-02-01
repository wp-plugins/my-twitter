<?php
	function my_twitter_time_span($unix_date){	 
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
	 
		$now = time();
	 
		if(empty($unix_date))  
			return "Bad date";
			 
		if($now > $unix_date){
			$difference = $now - $unix_date;
			$tense = "ago";	 
		}else{
			$difference = $unix_date - $now;
			$tense = "from now";
		}
	 
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
			$difference /= $lengths[$j];
			 
		$difference = round($difference);
	 
		if($difference != 1)
			$periods[$j].= "s";
			 
		return "$difference $periods[$j] {$tense}";
	}

	function my_twitter_form_get($str){
		if(!isset($_GET[$str]))
			return false;
		return urldecode($_GET[$str]);
	}

	function my_twitter_read_var($str){
		$res = $str;
		$res = str_replace('\\\'','\'',$res);
		$res = str_replace('\\\\','\\',$res);
		$res = str_replace('\\"','"',$res);
		return $res;
	}

	function my_twitter_form_post($str, $parse = true){
		if(!isset($_POST[$str]))
			return false;
		if($parse)
			return my_twitter_read_var($_POST[$str]);
		return $_POST[$str];
	}

	function my_twitter_get_dir($type) {
		if ( !defined('WP_CONTENT_URL') )
			define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
		if ( !defined('WP_CONTENT_DIR') )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
		else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	}

	function my_twitter_get_file($name){
		$res = '';
		$res = @file_get_contents($name);
		if($res === false || $res == ''){
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $name);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$res = curl_exec($ch);
			curl_close($ch);
		}
		return $res;
	}	

	function my_twitter_get_tweets(){
		$uid = get_option('my_twitter_id');
		$limit = intval(get_option('my_twitter_count'));

		if($limit <= 0)
			$limit = 5;

		$api_url = 'http://twitter.com/statuses/user_timeline/'.urlencode($uid).'.xml?count='.$limit;

		$req = my_twitter_get_file($api_url); 
		if($req == '')
			return array();

		$xml = @new SimpleXMLElement($req);
		
		$items_count= count($xml->entry);
		if($items_count < $limit)
			$limit = $items_count;

		$arr = array();
		foreach($xml->status as $res){
			$sts_id = (string)$res->id;
			$rpl = (string)$res->in_reply_to_status_id;
			if($rpl != '')
				$sts_id = $rpl;
			$date_time = (string)$res->created_at;
			
			$timestamp = '';
			$date_format = get_option('my_twitter_date_format');
			if($date_format != ''){
				$timestamp = ' - posted ';
				if($date_format == 'span')
					$timestamp .= my_twitter_time_span(strtotime($date_time));
				else
					$timestamp .= ' on '.date($date_format, strtotime($date_time));
			}
			
			$pattern = '/\@([a-zA-Z]+)/';
			$replace = '<a href="http://twitter.com/'.strtolower('\1').'">@\1</a>';
			$output = convert_smilies(preg_replace($pattern,$replace,make_clickable($res->text)));

			$author_name = (string)$res->user->name;
			$author_uid = (string)$res->user->screen_name;
			$author_img = (string)$res->user->profile_image_url;
			
			$arr[$sts_id] = array(
				'timestamp' => $timestamp,
				'tweet' => $output,
				'author' => $author_uid,
				'author_name' => $author_name,
				'author_url' => 'http://twitter.com/'.$author_uid,
				'author_img' => $author_img,
			);
		}
		unset($xml);

		$api_url_reply = 'http://search.twitter.com/search.atom?q=to:'.urlencode($uid);
		$req = my_twitter_get_file($api_url_reply); 
		if($req == '')
			return array();

		$xml = @new SimpleXMLElement($req);		
		$items_count = count($xml->entry);
		
		$limit = intval(get_option('my_twitter_count'));
		if($items_count < $limit)
			$limit = $items_count;

		$i = 0;			
		while($i < $limit){
			$id_tag = (string)$xml->entry[$i]->id;			
			$id_tag_part = explode(':', $id_tag);
			$sts_id = $id_tag_part[2];

			$date_time = (string)$xml->entry[$i]->published;
			$pos_t = strpos($date_time, 'T');
			$pos_z = strpos($date_time, 'Z');

			$date_raw = substr($date_time, 0, $pos_t);
			$date_part = explode('-', $date_raw);
			$date = $date_part[2].'/'.$date_part[1].'/'.$date_part[0];
			$time = substr($date_time, $pos_t+1, $pos_z-$pos_t-1);

			$author = (string)$xml->entry[$i]->author->name;
			$author_name = substr($author, strpos($author, ' ') + 2, strlen($author) - (strpos($author, ' ') + 3));
			$author_uid = substr($author, 0, strpos($author, ' '));

			$author_img = $xml->entry[$i]->link[1]->attributes();	
			$author_img = (string)$author_img['href'];

			$arr[$sts_id] = array(
				'date' => $date,
				'time' => $time,
				'tweet' => (string)$xml->entry[$i]->content,
				'author' => $author_uid,
				'author_name' => $author_name,
				'author_url' => (string)$xml->entry[$i]->author->uri,
				'author_img' => $author_img,
			);
			$i++;
		}
		krsort($arr);

		$limit = intval(get_option('my_twitter_count'));
		if(count($arr) > $limit){
			do{
				array_pop($arr);
			}while(count($arr) > $limit);
		}
		return $arr;
	}
?>