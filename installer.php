<?php
	require_once(ABSPATH . 'wp-admin/upgrade.php');

	global $wpdb;

	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == ''){
		add_option("my_twitter_title", "Latest Tweets");
		add_option("my_twitter_id", "");
		add_option("my_twitter_count", "5");
		add_option("my_twitter_version", '1.0.0');
	}
	
	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == '1.0.0' || $cur_ver == '1.0')
		update_option("my_twitter_version", '1.0.1');	

	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == '1.0.1')
		update_option("my_twitter_version", '1.0.2');	

	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == '1.0.2'){
		add_option("my_twitter_date_format", 'd/m/Y H:i:s');
		update_option( "my_twitter_version", '1.0.3');
	}

	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == '1.0.3')
		update_option("my_twitter_version", '1.0.4');	

	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == '1.0.4')
		update_option("my_twitter_version", '1.0.5');	

	$cur_ver = get_option("my_twitter_version");
	if($cur_ver == '1.0.5'){
		add_option("my_twitter_credit", 1);
		update_option("my_twitter_version", '1.0.6');	
	}
?>