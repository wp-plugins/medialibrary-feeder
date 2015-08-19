<?php
/**
 * MediaLibrary Feeder
 * 
 * @package    MediaLibraryFeeder
 * @subpackage MediaLibraryFeeder Widget
    Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* ==================================================
 * Widget
 * @since	1.12
 */
class MediaLibraryFeederWidgetItem extends WP_Widget {

	function __construct() {
		parent::__construct(
			'MediaLibraryFeederWidgetItem', // Base ID
			__( 'MediaLibraryFeederRssFeed' ), // Name
			array( 'description' => __( 'Entries of RSS feed from MediaLibrary Feeder.', 'medialibraryfeeder'), ) // Args
		);
	}

	function widget($args, $instance) {

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		if ($title) {
			echo $before_widget;
			echo $before_title . $title . $after_title;

			$feedwidget_tbl = get_option( 'medialibraryfeeder_feedwidget' );
			foreach ( $feedwidget_tbl as $feedtitle => $xmlurl ) {
				$checkbox[$feedtitle] = apply_filters('widget_checkbox', $instance[$feedtitle]);
				$iconurl = $medialibraryfeeder_settings[$feedtitle]['iconurl'];
				if ($checkbox[$feedtitle]) {
					?>
					<div>
					<a href="<?php echo $xmlurl; ?>">
					<img src="<?php echo $iconurl; ?>" align="middle"><?php echo $feedtitle; ?>
					</a>
					</div>
					<?php
				}
			}
			echo $after_widget;
		}

	}
	
	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$feedwidget_tbl = get_option( 'medialibraryfeeder_feedwidget' );
		foreach ( $feedwidget_tbl as $feedtitle => $xmlurl ) {
			$instance[$feedtitle] = strip_tags($new_instance[$feedtitle]);
		}
		return $instance;

	}
	
	function form($instance) {

		if (isset($instance['title'])) {
			$title = esc_attr($instance['title']);
		} else {
			$title = NULL;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<table>
		<?php

		$feedwidget_tbl = get_option( 'medialibraryfeeder_feedwidget' );
		foreach ( $feedwidget_tbl as $feedtitle => $xmlurl ) {
			if (isset($instance[$feedtitle])) {
				$checkbox[$feedtitle] = esc_attr($instance[$feedtitle]);
			} else {
				$checkbox[$feedtitle] = NULL;
			}
			?>
			<tr>
			<td align="left" valign="middle" nowrap>
				<label for="<?php echo $this->get_field_id($feedtitle); ?> ">
				<input class="widefat" id="<?php echo $this->get_field_id($feedtitle); ?>" name="<?php echo $this->get_field_name($feedtitle); ?>" type="checkbox"<?php checked($feedtitle, $checkbox[$feedtitle]); ?> value="<?php echo $feedtitle; ?>" />
				<?php echo $feedtitle; ?></label>
			</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php

	}
}

?>