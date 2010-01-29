<?php
	/*
		Plugin Name: My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (xhanch)
		Author URI: http://xhanch.com
		Version: 1.1.1
	*/

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
		<?php 
			if (get_option("my_twitter_title")!='')
				echo $before_title.get_option("my_twitter_title").$after_title;				
		?>
		<div id="xhanch_my_twitter">
			<h2><a href="http://twitter.com/<?php echo get_option('my_twitter_id'); ?>"><?php echo get_option("my_twitter_name"); ?></a></h2>

			<?php echo html_entity_decode(get_option("my_twitter_text_header")); ?>

			<ul>
			<?php foreach($res as $row){ ?>
				<li>
					<a href="<?php echo $row['author_url']; ?>"><img class="avatar" src="<?php echo $row['author_img']; ?>" alt="<?php echo $row['author_name']; ?>"/></a> <?php echo $row['tweet']; ?> <?php echo $row['timestamp']; ?>
					<div class="clear"></div>
				</li>
			<?php } ?>
			</ul>
			
			<?php echo html_entity_decode(get_option("my_twitter_text_footer")); ?>

			<?php if (get_option("my_twitter_credit")){ ?>
				<div class="credit"><a href="http://xhanch.com/" rel="section" title="My Twitter - A WordPress plugin by Xhanch.com">My Twitter, by Xhanch</a></div>
			<? }?>
		</div>
<?php		
		echo $after_widget;
	}

	function my_twitter_control(){			
		$arr_date_format = array(
			'' => 'Hidden',
			'd/m/Y H:i:s' => 'dd/mm/yyyy hh:mm:ss',
			'd/m/Y H:i' => 'dd/mm/yyyy hh:mm',
			'd/m/Y' => 'dd/mm/yyyy',
			'm/d/Y H:i:s' => 'mm/dd/yyyy hh:mm:ss',
			'm/d/Y H:i' => 'mm/dd/yyyy hh:mm',
			'm/d/Y' => 'mm/dd/yyyy',
			'M d, Y H:i:s' => 'mmm dd, yyyy hh:mm:ss',
			'M d, Y H:i' => 'mmm dd, yyyy hh:mm',
			'M d, Y' => 'mmm dd, yyyy',
			'span' => '? period ago',
		);

		$title = get_option('my_twitter_title');
		$name = htmlspecialchars(get_option('my_twitter_name'));
		$uid = get_option('my_twitter_id');
		$limit = intval(get_option('my_twitter_count'));
		$date_format = get_option('my_twitter_date_format');
		$text_header = htmlspecialchars(get_option('my_twitter_text_header'));
		$text_footer = htmlspecialchars(get_option('my_twitter_text_footer'));
		$credit = get_option('my_twitter_credit');

		if ($_POST['my_twitter_submit']){
			update_option("my_twitter_title", htmlspecialchars($_POST['my_twitter_title']));
			update_option("my_twitter_name", my_twitter_form_post('my_twitter_name'));
			update_option("my_twitter_id", htmlspecialchars($_POST['my_twitter_id']));
			update_option("my_twitter_count", intval($_POST['my_twitter_count']));
			update_option("my_twitter_date_format", htmlspecialchars($_POST['my_twitter_date_format']));
			update_option("my_twitter_text_header", my_twitter_form_post('my_twitter_text_header'));
			update_option("my_twitter_text_footer", my_twitter_form_post('my_twitter_text_footer'));
			update_option("my_twitter_credit", intval($_POST['my_twitter_credit']));
		}
?>
		<table>
			<tr>
				<td width="150"><label for="my_twitter_title">Title</label></td>
				<td><input type="text" id="my_twitter_title" name="my_twitter_title" value="<?php echo $title; ?>" /></td>
			</tr>
			<tr>
				<td width="150"><label for="my_twitter_title">Name</label></td>
				<td><input type="text" id="my_twitter_name" name="my_twitter_name" value="<?php echo $name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="my_twitter_id">Username</label></td>
				<td><input type="text" id="my_twitter_id" name="my_twitter_id" value="<?php echo $uid; ?>" /></td>
			</tr>
			<tr>
				<td><label for="my_twitter_count"># Latest Tweets</label></td>
				<td><input type="text" id="my_twitter_count" name="my_twitter_count" value="<?php echo $limit; ?>" size="5"  maxlength="2"/></td>
			</tr>
			<tr>
				<td><label for="my_twitter_date_format">Date Format</label></td>
				<td>
					<select id="my_twitter_date_format" name="my_twitter_date_format" style="width:100%">
						<?php foreach($arr_date_format as $fmt_val=>$fmt_ex){ ?>
							<option value="<?php echo $fmt_val; ?>" <?php echo ($fmt_val==$date_format)?'selected="selected"':''; ?>><?php echo $fmt_ex; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label for="my_twitter_text_header">Header Text</label>
					<textarea id="my_twitter_text_header" name="my_twitter_text_header" style="width:100%" rows="2"><?php echo $text_header; ?></textarea>
					<label for="my_twitter_text_footer">Footer Text</label>
					<textarea id="my_twitter_text_footer" name="my_twitter_text_footer" style="width:100%" rows="2"><?php echo $text_footer; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="my_twitter_credit">Display Credit</label></td>
				<td><input type="checkbox" id="my_twitter_credit" name="my_twitter_credit" value="1" <?php echo ($credit?'checked="checked"':''); ?>/></td>
			</tr>
		</table>
		<input type="hidden" id="my_twitter_submit" name="my_twitter_submit" value="1" />
<?php
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