<?php
/*
Plugin Name: Original Post Ranking Widget
Description: To view the post ranking in the form of a widget.
Plugin URI: http://wordpress.org/extend/plugins/original-post-ranking-widget/
Version: 1.0.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/
Text Domain: original_post_ranking_widget
Domain Path: /languages
*/

/*  Copyright 2012 gqevu6bsiz (email : gqevu6bsiz@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain('original_post_ranking_widget', false, basename(dirname(__FILE__)).'/languages');

define ('ORIGINAL_POST_RANKING_WIDGET_VER', '1.0.1');
define ('ORIGINAL_POST_RANKING_WIDGETPLUGIN_NAME', 'Original Post Ranking Widget');
define ('ORIGINAL_POST_RANKING_WIDGETMANAGE_URL', admin_url('options-general.php').'?page=original_post_ranking_widget');
define ('ORIGINAL_POST_RANKING_WIDGETRECORD_NAME', 'original_post_ranking_widget');
define ('ORIGINAL_POST_RANKING_WIDGETPLUGIN_DIR', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/');
?>
<?php
function original_post_ranking_widget_add_menu() {
	// add menu
	add_options_page(__('Original Post Ranking Widget\'s Setting', 'original_post_ranking_widget'), __(ORIGINAL_POST_RANKING_WIDGETPLUGIN_NAME, 'original_post_ranking_widget') , 'administrator', 'original_post_ranking_widget', 'original_post_ranking_widget_setting');

	// plugin links
	add_filter('plugin_action_links', 'original_post_ranking_widget_plugin_setting', 10, 2);
}



// plugin setup
function original_post_ranking_widget_plugin_setting($links, $file) {
	if(plugin_basename(__FILE__) == $file) {
		$settings_link = '<a href="'.ORIGINAL_POST_RANKING_WIDGETMANAGE_URL.'">'.__('Settings').'</a>'; 
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_action('admin_menu', 'original_post_ranking_widget_add_menu');



// setting
function original_post_ranking_widget_setting() {
	$UPFN = 'sett';
	$Msg = '';

	if(!empty($_POST[$UPFN])) {

		// update
		if($_POST[$UPFN] == 'Y') {
			unset($_POST[$UPFN]);

			$Update = array();
			$Default = original_post_ranking_widget_get();
			$ResetFlg = false;
			foreach($Default as $name => $tag) {
				if(!empty($_POST[$name])) {
					$Update[$name] = $_POST[$name];
					$ResetFlg = true;
				}
			}
			if($ResetFlg == false) {
				foreach($Default as $name => $tag) {
					$Update[$name] = $tag;
				}
			}

			update_option(ORIGINAL_POST_RANKING_WIDGETRECORD_NAME, $Update);
			$Msg = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';
		}

	}

	// get data
	$Data = original_post_ranking_widget_get(get_option(ORIGINAL_POST_RANKING_WIDGETRECORD_NAME));

	// include js css
	$ReadedJs = array('jquery');
	wp_enqueue_script('original-post-ranking-widget', ORIGINAL_POST_RANKING_WIDGETPLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.js', $ReadedJs, ORIGINAL_POST_RANKING_WIDGET_VER);
	wp_enqueue_style('original-post-ranking-widget', ORIGINAL_POST_RANKING_WIDGETPLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.css', array(), ORIGINAL_POST_RANKING_WIDGET_VER);
?>
<div class="wrap">
	<div class="icon32" id="icon-themes"></div>
	<h2><?php _e('Original Post Ranking Widget\'s Setting', 'original_post_ranking_widget'); ?></h2>
	<?php echo $Msg; ?>
	<p>&nbsp;</p>

	<form id="original_post_ranking_widget_form" method="post" action="">
		<input type="hidden" name="<?php echo $UPFN; ?>" value="Y">
		<?php wp_nonce_field(); ?>

		<?php $Arr = array('before_ranking_loop', 'after_ranking_loop'); ?>
		<?php original_post_ranking_widget_template_list(__('Ranking Loop', 'original_post_ranking_widget'), $Arr, $Data); ?>

		<?php $Arr = array('entry_title'); ?>
		<?php original_post_ranking_widget_template_list(__('Title'), $Arr, $Data); ?>

		<?php $Arr = array('entry_date'); ?>
		<?php original_post_ranking_widget_template_list(__('Date'), $Arr, $Data); ?>

		<?php $Arr = array('entry_thumbnails'); ?>
		<?php original_post_ranking_widget_template_list(__('Thumbnail'), $Arr, $Data); ?>

		<?php $Arr = array('entry_category'); ?>
		<?php original_post_ranking_widget_template_list(__('Category'), $Arr, $Data); ?>

		<?php $Arr = array('entry_excerpt', 'excerpt_length'); ?>
		<?php original_post_ranking_widget_template_list(__('Excerpt'), $Arr, $Data); ?>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
		</p>

		<p class="submit reset">
			<span class="description"><?php _e('Would initialize?', 'original_post_ranking_widget'); ?></span>
			<input type="button" class="button-secondary" value="<?php _e('Reset'); ?>" />
		</p>

	</form>
</div>
<?php
}



// template list
function original_post_ranking_widget_template_list($Title, $List, $Data) {

	$Default = original_post_ranking_widget_get();

	$Contents = '';

	$Contents .= '<h3>'.$Title.'</h3>';
	$Contents .= '<table class="form-table">';
	$Contents .= '<tbody">';

	foreach($List as $name) {
		
		$Contents .= '<tr>';
		$Contents .= '<th><br /><label for="'.$name.'">'.__($name, 'original_post_ranking_widget').'</label></th>';

		$Val = '';
		if(!empty($Data[$name])) {
			$Val = $Data[$name];
		}
		if($name == 'content_length') {
			$Contents .= '<td><br /><input type="text" name="'.$name.'" id="'.$name.'" class="regular-text" value="'.$Val.'"></td>';
		} else {
			$Contents .= '<td><br /><textarea rows="3" cols="50" name="'.$name.'" id="'.$name.'" class="large-text">'.$Val.'</textarea></td>';
		}
		$Contents .= '<td class="example">'.__('Example', 'original_post_ranking_widget').'<br /><div class="code">'.nl2br(htmlspecialchars($Default[$name])).'</div></td>';
		$Contents .= '</tr>';
		
	}

	$Contents .= '</tbody>';
	$Contents .= '</table>';
	
	echo $Contents;

}



// post list get datas
function original_post_ranking_widget_get($Data = array()) {

	$NewData = array();

	if(!empty($Data)) {
		foreach($Data as $name => $val) {
			$NewData[$name] = stripslashes($val);
		}
	} else {

		$NewData["before_ranking_loop"] = '<div class="original_post_ranking_widget_roop" id="rank_%s">';
		$NewData["after_ranking_loop"] = '</div>';

		$NewData["entry_title"] = '<p class="entry-title"><a href="%1$s">%2$s</a></p>';

		$NewData["entry_date"] = '<time class="entry-date" datetime="%1$s" pubdate></time>';

		$NewData["entry_thumbnails"] = '<p class="entry-thumbnails">'."\n".'<a href="%1$s"><img src="%2$s" alt="%3$s" /></a>'."\n".'</p>';

		$NewData["entry_date"] = '<time class="entry-date" datetime="%1$s" pubdate>%2$s</time>';

		$NewData["entry_category"] = '<ul class="entry-categories">'."\n".'%s'."\n".'</ul>';

		$NewData["entry_excerpt"] = '<div class="entry-excerpt">%s</div>';
		$NewData["excerpt_length"] = 200;

	}

	return $NewData;

}




// original_post_ranking_widget_register
function original_post_ranking_widget_register() {
	register_widget("original_post_ranking_widget_cls");
}
add_action('widgets_init', "original_post_ranking_widget_register", 99);



// original_post_ranking_widget_class
class original_post_ranking_widget_cls extends WP_Widget {

	function original_post_ranking_widget_cls() {
		$widget_ops = array( 'classname' => 'original_post_ranking', 'description' => __('Widget to create a ranking of the original', 'original_post_ranking_widget') );
		$this->WP_Widget( false, __('Original Post Ranking', 'original_post_ranking_widget'), $widget_ops );
	}

	function widget( $args, $instance ) {
		
		$Data = original_post_ranking_widget_get(get_option(ORIGINAL_POST_RANKING_WIDGETRECORD_NAME));
		$title = apply_filters( 'widget_title', $instance['title']);
		extract( $args, EXTR_SKIP );
		
		echo $before_widget;

		if(!empty($title)) {
			echo $before_title;
			echo $title;
			echo $after_title;
		}
		
		echo sprintf($Data["before_ranking_loop"], $widget_id);

		if(!empty($instance["ranking"]) && is_array($instance["ranking"])) {
			foreach($instance["ranking"] as $post_id) {
				$post = get_post($post_id);
				
				if(!empty($post) and $post->post_type == 'post' and $post->post_status == 'publish') {
					
					// title
					if(!empty($instance["entry_title"]) && !empty($Data["entry_title"])) {
						echo sprintf($Data["entry_title"], esc_url(get_permalink($post->ID)), apply_filters( 'the_title', $post->post_title));
					}

					// thumbnail
					if(!empty($instance["thumbnail"]) && !empty($Data["entry_thumbnails"]) && has_post_thumbnail($post->ID)) {
						$Thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
						echo sprintf($Data["entry_thumbnails"], esc_url(get_permalink($post->ID)), $Thumbnail[0], $post->post_title);
					}
					
					// date
					if(!empty($instance["date"]) && !empty($Data["entry_date"])) {
						printf($Data["entry_date"], esc_attr( apply_filters('get_the_date', mysql2date('c', $post->post_date), "c") ), esc_html( apply_filters('get_the_date', mysql2date(get_option('date_format'), $post->post_date), get_option('date_format')) ));
					}

					// excerpt
					if(!empty($instance["excerpt"]) && !empty($Data["entry_excerpt"]) && has_excerpt($post->ID)) {
						$Ex = $excerpt = mb_substr($post->post_excerpt, 0, $Data["excerpt_length"]);
						echo sprintf($Data["entry_excerpt"], apply_filters('the_excerpt', $Ex));
					}

					// category
					if(!empty($instance["category"]) && !empty($Data["entry_category"])) {
						$Categories = get_the_category($post->ID);
						if(!empty($Categories)) {
							foreach($Categories as $cat) {
								$Category = sprintf('<li><a href="%1$s">%2$s</a></li>', esc_url(get_permalink($post->ID)), esc_html($cat->cat_name));
							}
							echo sprintf($Data["entry_category"], $Category);
						}
					}
					
				}
			}
		}
		echo $Data["after_ranking_loop"];
		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['entry_title'] = (int) $new_instance['entry_title'];
		$instance['thumbnail'] = (int) $new_instance['thumbnail'];
		$instance['category'] = (int) $new_instance['category'];
		$instance['date'] = (int) $new_instance['date'];
		$instance['excerpt'] = (int) $new_instance['excerpt'];
		$instance['mptl'] = (int) $new_instance['mptl'];

		//delete
		if($new_instance['delete'] != "") {
			$PostNo = (int) $new_instance['delete'];
			unset($new_instance['ranking'][$PostNo]);
			
			$count = 0;
			$Ranking = array();
			foreach($new_instance['ranking'] as $key => $val) {
				$Ranking[$count] = $val;
				$count++;
			}
			unset($new_instance["ranking"]);
			$new_instance["ranking"] = $Ranking;
		}

		$instance["ranking"] = $new_instance['ranking'];
		
		// add
		if(!empty($new_instance['post_no'])) {
			$PostNo = (int) $new_instance['post_no'];
			if(!empty($PostNo)) {
				$instance["ranking"][] = (int) $new_instance['post_no'];
				$instance["created"] = (int) $new_instance['post_no'];
			}
		}

		return $instance;
	}

	function form( $instance ) {
		wp_enqueue_script('original-post-ranking-widget', ORIGINAL_POST_RANKING_WIDGETPLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.js', array(), ORIGINAL_POST_RANKING_WIDGET_VER);
		wp_enqueue_style('original-post-ranking-widget', ORIGINAL_POST_RANKING_WIDGETPLUGIN_DIR.dirname(plugin_basename(__FILE__)).'.css', array(), ORIGINAL_POST_RANKING_WIDGET_VER);

		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$entry_title = isset( $instance['entry_title'] ) ? absint( $instance['entry_title'] ) : 0;
		$thumbnail = isset( $instance['thumbnail'] ) ? absint( $instance['thumbnail'] ) : 0;
		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$date = isset( $instance['date'] ) ? absint( $instance['date'] ) : 0;
		$excerpt = isset( $instance['excerpt'] ) ? absint( $instance['excerpt'] ) : 0;
		$mptl = isset( $instance['mptl'] ) ? absint( $instance['mptl'] ) : 0;
		$ranking = isset( $instance['ranking'] ) ? ( $instance['ranking'] ) : array();
		$created = isset( $instance['created'] ) ? absint( $instance['created'] ) : 0;
?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<?php $Show = array(__('Hide'), __('Show')); ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'entry_title' ) ); ?>"><?php _e( 'Post' ); ?><?php _e( 'Title' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'entry_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'entry_title' ) ); ?>">
					<?php foreach($Show as $key => $val) : ?>
						<?php $Selected = ''; ?>
						<?php if($key == esc_attr( $entry_title )) { $Selected = ' selected="selected"'; } ?>
						<option value="<?php echo $key; ?>"<?php echo $Selected; ?>><?php echo $val; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>"><?php _e( 'Thumbnail' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>">
					<?php foreach($Show as $key => $val) : ?>
						<?php $Selected = ''; ?>
						<?php if($key == esc_attr( $thumbnail )) { $Selected = ' selected="selected"'; } ?>
						<option value="<?php echo $key; ?>"<?php echo $Selected; ?>><?php echo $val; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Category' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
					<?php foreach($Show as $key => $val) : ?>
						<?php $Selected = ''; ?>
						<?php if($key == esc_attr( $category )) { $Selected = ' selected="selected"'; } ?>
						<option value="<?php echo $key; ?>"<?php echo $Selected; ?>><?php echo $val; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'date' ) ); ?>"><?php _e( 'Date' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'date' ) ); ?>">
					<?php foreach($Show as $key => $val) : ?>
						<?php $Selected = ''; ?>
						<?php if($key == esc_attr( $date )) { $Selected = ' selected="selected"'; } ?>
						<option value="<?php echo $key; ?>"<?php echo $Selected; ?>><?php echo $val; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'excerpt' ) ); ?>"><?php _e( 'Excerpt' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'excerpt' ) ); ?>">
					<?php foreach($Show as $key => $val) : ?>
						<?php $Selected = ''; ?>
						<?php if($key == esc_attr( $excerpt )) { $Selected = ' selected="selected"'; } ?>
						<option value="<?php echo $key; ?>"<?php echo $Selected; ?>><?php echo $val; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<div class="original-post-ranking-widget-ranklists">

				<?php if(!empty($ranking) && is_array($ranking)) : ?>
					<div class="list">
						<?php _e('Ranking', 'original_post_ranking_widget'); ?>:
						<?php $RankCount = 1; ?>
						<?php foreach($ranking as $key => $post_id) : ?>
							<?php $anim_cls = ''; ?>
							<?php if($created == $post_id) : ?>
								<?php $anim_cls = 'Created'; ?>
							<?php endif; ?>
							<div class="ranking-list <?php echo $anim_cls; ?>">
								<div class="alignright">
									<a href="javascript:void(0)" class="id-remove-button" rel="<?php echo $key; ?>"><?php _e('Delete'); ?></a>
								</div>
								<div class="alignleft">
									No: <span class="no"><?php echo $RankCount; ?></span>
									<input type="text" size="3" name="<?php echo esc_attr($this->get_field_name("ranking")); ?>[]" value="<?php echo esc_attr($post_id); ?>" />
								</div>
								<br class="clear" />
							</div>
							<?php $RankCount++; ?>
						<?php endforeach; ?>
						<input type="hidden" class="delete" name="<?php echo esc_attr($this->get_field_name('delete')); ?>" value="" />
					</div>
				<?php endif; ?>

				<div class="add wp-hidden-children">
					<h4><a class="add-toggle" href="javascript:void(0)">+ <?php _e('Add post ID', 'original_post_ranking_widget'); ?></a></h4>
					<p class="wp-hidden-child">
						<label for="<?php echo esc_attr( $this->get_field_id( 'add' ) ); ?>"><?php _e( 'ID', 'original-ranking-widget' ); ?>:</label>
						<input type="text" size="3" name="<?php echo esc_attr( $this->get_field_name( 'post_no' ) ); ?>" />
						<input type="button" class="button id-add-button" value="<?php _e( 'Add' ); ?>" />
					</p>
				</div>

			</div>

<script type="text/javascript">
jQuery(document).ready( function($) {
	var $CreateList = $('body.widgets-php .original-post-ranking-widget-ranklists .list .ranking-list.Created');
	$CreateList.animate({ "background-color": "#FCFCFC" });
	$CreateList.toggleClass('Created');
});
</script>

		<?php
	}

}





?>