<?php
	require_once(ABSPATH . 'wp-admin/upgrade.php');

	global $wpdb;
	$my_twitter_version = "1.0.0";

	$cur_ver = get_option( "my_twitter_version" );

	if($cur_ver === false){
		add_option("my_twitter_title", "Latest Tweets");
		add_option("my_twitter_id", "");
		add_option("my_twitter_count", "5");

		add_option("my_twitter_version", $my_twitter_version);
	}else if( $cur_ver != $my_twitter_version)
		update_option( "my_twitter_version", $my_twitter_version );
?>