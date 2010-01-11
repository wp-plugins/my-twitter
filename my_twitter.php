<?php
	/*
		Plugin Name: My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (xhanch)
		Author URI: http://xhanch.com
		Version: 1.0.1
	*/

	define("MANAGEMENT_PERMISSION", "edit_themes");

	function my_twitter_install () {
		require_once(dirname(__FILE__).'/installer.php');
	}
	register_activation_hook(__FILE__,'my_twitter_install');
		
	require_once(dirname(__FILE__).'/my_twitter.function.php');	

	//Widget
	
	function widget_my_twitter($args) {
		extract($args);
		$res = my_twitter_get_tweets();
		if(count($res) == 0) 
			return;		
		echo $before_widget;
?>
		<? 
			if (get_option("my_twitter_title")!='')
				echo "\n".$before_title.get_option("my_twitter_title").$after_title;				
		?>
		<div class="my_twitter">
			<h2><a href="http://twitter.com/<?=get_option('my_twitter_id'); ?>"><?=get_option("my_twitter_name"); ?></a></h2>
			<? foreach($res as $row){ ?>
				<div class="tweet">
					<a href="<?=$row['author_url']; ?>"><img class="avatar" src="<?=$row['author_img']; ?>"/></a> <?=$row['tweet']; ?> <?=$row['date']; ?> <?=$row['time']; ?>
				</div>
				<div class="clear"></div>
			<? } ?>
		</div>
<?		
		echo $after_widget;
	}

	function my_twitter_control(){				
		$title = get_option('my_twitter_title');
		$name = get_option('my_twitter_name');
		$uid = get_option('my_twitter_id');
		$limit = intval(get_option('my_twitter_count'));

		if ($_POST['my_twitter_submit']){
			update_option("my_twitter_title", htmlspecialchars($_POST['my_twitter_title']));
			update_option("my_twitter_name", htmlspecialchars($_POST['my_twitter_name']));
			update_option("my_twitter_id", htmlspecialchars($_POST['my_twitter_id']));
			update_option("my_twitter_count", intval($_POST['my_twitter_count']));
		}
?>
		<table>
			<tr>
				<td width="150"><label for="my_twitter_title">Title</label></td>
				<td><input type="text" id="my_twitter_title" name="my_twitter_title" value="<?=$title; ?>" /></td>
			</tr>
			<tr>
				<td width="150"><label for="my_twitter_title">Name</label></td>
				<td><input type="text" id="my_twitter_name" name="my_twitter_name" value="<?=$name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="my_twitter_id">Username</label></td>
				<td><input type="text" id="my_twitter_id" name="my_twitter_id" value="<?=$uid; ?>" /></td>
			</tr>
			<tr>
				<td><label for="my_twitter_count"># Latest Tweets</label></td>
				<td><input type="text" id="my_twitter_count" name="my_twitter_count" value="<?=$limit; ?>" size="5"  maxlength="2"/></td>
			</tr>
		</table>
		<input type="hidden" id="my_twitter_submit" name="my_twitter_submit" value="1" />
<?
	}

	function widget_my_twitter_init(){
		register_sidebar_widget('My Twitter', 'widget_my_twitter');
		register_widget_control('My Twitter', 'my_twitter_control', 300, 200 );     
	}
	add_action("plugins_loaded", "widget_my_twitter_init");

	function my_twitter_css() {
		echo '<link rel="stylesheet" href="'.my_twitter_get_dir('url').'/css.css" type="text/css" media="screen" />';
	}
	add_action('wp_print_styles', 'my_twitter_css');
?>