<?php
/**
 * MediaLibrary Feeder
 * 
 * @package    MediaLibrary Feeder
 * @subpackage MediaLibraryFeederAdmin Management screen
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

class MediaLibraryFeederAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = MEDIALIBRARYFEEDER_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=medialibraryfeeder').'">'.__( 'Settings').'</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_menu() {
		add_options_page( 'MediaLibrary Feeder Options', 'MediaLibrary Feeder', 'manage_options', 'medialibraryfeeder', array($this, 'plugin_options') );
	}


	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_options() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		wp_enqueue_style( 'jquery-ui-tabs', MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/css/jquery-ui.css' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-tabs-in', MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/js/jquery-ui-tabs-in.js' );
		wp_enqueue_script( 'jquery-check-selectall-in', MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/js/jquery-check-selectall-in.js' );

		if( !empty($_POST) ) { 
			$this->options_updated();
			$this->post_meta_updated();
		}
		$scriptname = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH).'?page=medialibraryfeeder';

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		$pagemax = $medialibraryfeeder_settings[pagemax];

		?>

		<div class="wrap">
		<h2>MediaLibrary Feeder</h2>

	<div id="tabs">
	  <ul>
		<li><a href="#tabs-1"><?php _e('Settings'); ?></a></li>
		<li><a href="#tabs-2"><?php _e('Registration of feed', 'medialibraryfeeder'); ?></a></li>
	    <li><a href="#tabs-3"><?php _e('Caution:'); ?></a></li>

	<!--
		<li><a href="#tabs-4">FAQ</a></li>
	 -->
	  </ul>

	<form method="post" action="<?php echo $scriptname; ?>">

	  <div id="tabs-1">
		<div class="wrap">
			<h2><?php _e('Settings'); ?></h2>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			<p>
			<div><?php _e('Number of files to show to this page', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_pagemax" value="<?php echo $pagemax; ?>" size="3" /></div>
			</p>

			<?php
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_status' => null,
				'post_parent' => $post->ID
				); 

			$attachments = get_posts($args);

			// pagenation
			foreach ( $attachments as $attachment ) {
				++$pageallcount;
			}
			if (!empty($_GET['p'])){
				$page = $_GET['p'];
			} else {
				$page = 1;
			}
			$count = 0;
			$pagebegin = (($page - 1) * $pagemax) + 1;
			$pageend = $page * $pagemax;
			$pagelast = ceil($pageallcount / $pagemax);

			?>
			<table class="wp-list-table widefat">
			<tbody>
				<tr><td width="60"></td><td></td><td></td><td></td>
				<td align="right">
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname);
				?>
				</td>
				</tr>
				<tr>
				<td align="center" valign="middle" width="60"><?php _e('File', 'medialibraryfeeder'); ?></td>
				<td align="left" valign="middle"><?php _e('Permalink and Filetype', 'medialibraryfeeder'); ?></td>
				<td align="left" valign="middle"><?php _e('Date/Time'); ?></td>
				<td align="left" valign="middle"><?php _e('Apply'); ?><div><input type="checkbox" id="group_medialibraryfeeder" class="checkAll"></div></td>
				<td align="left" valign="middle"><?php _e('Feed Title', 'medialibraryfeeder'); ?></td>
				</tr>
			<?php

			if ($attachments) {
				foreach ( $attachments as $attachment ) {
					++$count;
				    $apply = get_post_meta( $attachment->ID, 'medialibraryfeeder_apply', true );
					$feedtitle = get_post_meta( $attachment->ID, "medialibraryfeeder_title", true );
					if ( $pagebegin <= $count && $count <= $pageend ) {
						$title = $attachment->post_title;
						$link = $attachment->guid;
						$permalink = get_attachment_link($attachment->ID);
						$ext = end(explode('.', $attachment->guid));
						$date = $attachment->post_date;
						$thumblinks = wp_get_attachment_image_src( $attachment->ID, 'thumbnail', TRUE );
						$thumblink = '<img width="50" height="50" src="'.$thumblinks[0].'">';
					?>
						<tr>
							<td align="center" valign="middle" width="60"><a title="<?php _e('View');?>" href="<?php echo $link; ?>" target="_blank"><?php echo $thumblink; ?></a></td>
							<td align="left" valign="middle"><div><a style="color: #4682b4;" title="<?php _e('View');?>" href="<?php echo $permalink; ?>" target="_blank"><?php echo $title; ?></a></div><div><?php echo $ext; ?></div></td>
							<td align="left" valign="middle"><?php echo $date; ?></td>
							<td align="left" valign="middle">
							    <input type="hidden" class="group_medialibraryfeeder" name="medialibraryfeeder_applys[<?php echo $attachment->ID; ?>]" value="false">
							    <input type="checkbox" class="group_medialibraryfeeder" name="medialibraryfeeder_applys[<?php echo $attachment->ID; ?>]" value="true" <?php if ( $apply == true ) { echo 'checked'; }?>>
							</td>
							<td align="left" valign="middle">
								<select name="medialibraryfeeder_titles[<?php echo $attachment->ID; ?>]">
								<?php
										foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
											if( is_array($value1) ) {
												?><option value="<?php echo $key1; ?>"<?php if($feedtitle === $key1){echo 'selected';}?>><?php echo $key1; ?></option>
											<?php
											}
										}
								?>
								</select>
							</td>
						</tr>
					<?php
					} else {
					?>
					    <input type="hidden" name="medialibraryfeeder_applys[<?php echo $attachment->ID; ?>]" value="<?php echo $apply; ?>">
						<input type="hidden" name="medialibraryfeeder_titles[<?php echo $attachment->ID; ?>]" value="<?php echo $feedtitle; ?>" />
					<?php
					}
				}
			}
			?>
				<tr><td width="60"></td><td></td><td></td><td></td>
				<td align="right">
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname);
				?>
				</td>
				</tr>
			</tbody>
			</table>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

		</div>
	  </div>

	  <div id="tabs-2">
		<div class="wrap">
		<h2><?php _e('Registration of feed', 'medialibraryfeeder'); ?></h2>

			<p>
			<div><?php _e('Feed Title', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_title" value=""></div>
			<div><?php _e('Feed Description', 'medialibraryfeeder'); ?>:</div><textarea name="medialibraryfeeder_settings_titles_description" rows="2" cols="80" value=""></textarea>
			<div><?php _e('Number of feeds of the latest to publish', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_rssmax" value="" size="3" /></div>
			<div><?php _e('Icon Url', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_iconurl" value="" size="100"/></div>
			</p>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			<h2><?php _e('Feed registered', 'medialibraryfeeder'); ?></h2>
			<table class="wp-list-table widefat">
			<tbody>
			<tr>
			<td><?php _e('Feed Title', 'medialibraryfeeder'); ?></td>
			<td><?php _e('Feed Description', 'medialibraryfeeder'); ?></td>
			<td><?php _e('Number of feeds of the latest to publish', 'medialibraryfeeder'); ?></td>
			<td><?php _e('Icon', 'medialibraryfeeder'); ?></td>
			<td><?php _e('Delete'); ?></td>
			</tr>
			<?php
			foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
				if( is_array($value1) ) {
					?><tr><td>
					<?php echo $key1; ?></td>
					<?php
					foreach ( $value1 as $key2 => $value2 ) {
						?><td><?php
						if ( $key2 === 'iconurl' ) {
							?><img src = "<?php echo $value2; ?>">
							<?php
						} else {
							echo $value2;
						}
						?></td>
					<?php
					}
					?>
					<td>
					<input type="checkbox" name="medialibraryfeeder_settings_delete_title[]" value="<?php echo $key1; ?>">
					</td>
					</tr>
				<?php
				}
			}
			?>
			</tbody>
			</table>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

		</div>
	  </div>

	  <div id="tabs-3">
		<div class="wrap">
			<h2><?php _e('Caution:') ?></h2>
			<li><h3><?php _e('Meta-box of MediaLibrary Feeder will be added to [Edit Media]. Please do apply it, choose a feed title.', 'medialibraryfeeder'); ?></h3></li>
			<img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/images/editmedia.png'; ?>">
			<li><h3><?php _e('Widget of MediaLibrary Feeder will be added to [Widgets]. Please enter the title, put a check in the feed you want to use.', 'medialibraryfeeder'); ?></h3></li>
			<img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/images/widget.png'; ?>">
			<li><h3><?php _e('Icon can be used include the following.', 'medialibraryfeeder'); ?></h3></li>
			<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/rssfeeds.png'; ?>"><input type="text" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/rssfeeds.png'; ?>" size="100" /></div>
			<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/podcast.png'; ?>"><input type="text" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/podcast.png'; ?>" size="100" /></div>
		</div>
	  </div>

	<!--
	  <div id="tabs-4">
		<div class="wrap">
		<h2>FAQ</h2>

		</div>
	  </div>
	-->

	</form>
	</div>

		</div>
		<?php
	}

	/* ==================================================
	 * Pagenation
	 * @since	1.0
	 * string	$page
	 * string	$pagebegin
	 * string	$pageend
	 * string	$pagelast
	 * string	$scriptname
	 * return	$html
	 */
	function pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname){

			$pageprev = $page - 1;
			$pagenext = $page + 1;
			?>
<div class='tablenav-pages'>
<span class='pagination-links'>
<?php if ( $page <> 1 ){
		?><a title='<?php _e('Go to the first page'); ?>' href='<?php echo $scriptname; ?>'>&laquo;</a>
		<a title='<?php _e('Go to the previous page'); ?>' href='<?php echo $scriptname.'&p='.$pageprev ; ?>'>&lsaquo;</a>
<?php }	?>
<?php echo $page; ?> / <?php echo $pagelast; ?>
<?php if ( $page <> $pagelast ){
		?><a title='<?php _e('Go to the next page'); ?>' href='<?php echo $scriptname.'&p='.$pagenext ; ?>'>&rsaquo;</a>
		<a title='<?php _e('Go to the last page'); ?>' href='<?php echo $scriptname.'&p='.$pagelast; ?>'>&raquo;</a>
<?php }	?>
</span>
</div>
			<?php

	}

	/* ==================================================
	 * Update wp_options table.
	 * @since	1.0
	 */
	function options_updated(){

		$settings_tbl = array();

		$settings_tbl[pagemax] = intval($_POST['medialibraryfeeder_settings_pagemax']);

		$post_title = $_POST['medialibraryfeeder_settings_titles_title'];
		$post_description = $_POST['medialibraryfeeder_settings_titles_description'];
		$post_rssmax = intval($_POST['medialibraryfeeder_settings_titles_rssmax']);
		$post_iconurl = $_POST['medialibraryfeeder_settings_titles_iconurl'];
		$delete_titles = $_POST['medialibraryfeeder_settings_delete_title'];

		$titles = FALSE;
		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
			if( is_array($value1) ) {
				foreach ( $value1 as $key2 => $value2 ) {
					$settings_tbl[$key1][$key2] = $value2;
				}
				if ( !empty($post_title) && !empty($post_description) && !empty($post_rssmax) && !empty($post_iconurl) ){
					$settings_tbl[$post_title][description] = $post_description;
					$settings_tbl[$post_title][rssmax] = $post_rssmax;
					$settings_tbl[$post_title][iconurl] = $post_iconurl;
				}
				$titles = TRUE;
			}
		}

		if ( !$titles ) {
			if ( !empty($post_title) && !empty($post_description) && !empty($post_rssmax) && !empty($post_iconurl) ){
				$settings_tbl[$post_title][description] = $post_description;
				$settings_tbl[$post_title][rssmax] = $post_rssmax;
				$settings_tbl[$post_title][iconurl] = $post_iconurl;
			}
		} else {
			if ( !empty($delete_titles) ) {
				$wp_uploads = wp_upload_dir();
				$wp_upload_path = $wp_uploads['basedir'];
				foreach ( $settings_tbl as $key1 => $value1 ) {
					if( is_array($value1) ) {
						foreach ( $delete_titles as $delete_title ) {
							if ( $delete_title === $key1 ) {
								unset($settings_tbl[$key1]);
								$xmlfile = $wp_upload_path.'/'.md5($delete_title).'.xml';
								if ( file_exists($xmlfile)){
									unlink($xmlfile);
								}
							}
						}
					}
				}
			}
		}

		update_option( 'medialibraryfeeder_settings', $settings_tbl );

	}

	/* ==================================================
	 * Update wp_postmeta table for admin settings.
	 * @since	1.0
	 */
	function post_meta_updated() {

		$medialibraryfeeder_applys = $_POST['medialibraryfeeder_applys'];
		$medialibraryfeeder_titles = $_POST['medialibraryfeeder_titles'];

		$delete_titles = $_POST['medialibraryfeeder_settings_delete_title'];

		foreach ( $medialibraryfeeder_applys as $key => $value ) {
			if ( $value === 'true' ) {
		    	update_post_meta( $key, 'medialibraryfeeder_apply', $value );
			} else {
				delete_post_meta( $key, 'medialibraryfeeder_apply' );
				delete_post_meta( $key, 'medialibraryfeeder_title' );
			}
		}

		foreach ( $medialibraryfeeder_titles as $key => $value ) {
			if ( !empty($value) && get_post_meta( $key, 'medialibraryfeeder_apply', true  ) == true ) {
		    	update_post_meta( $key, 'medialibraryfeeder_title', $value );
			} else {
				delete_post_meta( $key, 'medialibraryfeeder_apply' );
				delete_post_meta( $key, 'medialibraryfeeder_title' );
			}
		}

		if ( !empty($delete_titles) ) {
			foreach ( $medialibraryfeeder_titles as $key => $value ) {
				foreach ( $delete_titles as $delete_title ) {
					if ( $delete_title === $value ) {
						delete_post_meta( $key, 'medialibraryfeeder_apply' );
						delete_post_meta( $key, 'medialibraryfeeder_title' );
					}
				}
			}
		}

	}

	/* ==================================================
	 * Custom box.
	 * @since	1.0
	 */
	function add_attachment_medialibraryfeeder_field( $form_fields, $post ) {

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		$feedtitle = get_post_meta($post->ID, "medialibraryfeeder_title", true);

		_e('MediaLibrary Feeder');

	    // checkbox
	    $apply = get_post_meta( $post->ID, 'medialibraryfeeder_apply', true );
	    $form_fields["medialibraryfeeder_apply"]["label"] = __('Apply');
	    $form_fields["medialibraryfeeder_apply"]["input"] = "html";
	    $form_fields["medialibraryfeeder_apply"]["html"]  = "<input type='checkbox' name='attachments[{$post->ID}][medialibraryfeeder_apply]' value='true'";
	    $form_fields["medialibraryfeeder_apply"]["html"] .= ( $apply == true )? " checked":"";
		$form_fields["medialibraryfeeder_apply"]["html"] .= ">\n";

	    // select
	    $title = get_post_meta( $post->ID, 'medialibraryfeeder_title', true );
	    $form_fields["medialibraryfeeder_title"]["label"] = __('Feed Title', 'medialibraryfeeder');
	    $form_fields["medialibraryfeeder_title"]["input"] = "html";
	    $form_fields["medialibraryfeeder_title"]["html"]  = "<select name='attachments[{$post->ID}][medialibraryfeeder_title]' id='attachments[{$post->ID}][medialibraryfeeder_title]'>\n";

		foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
			if( is_array($value1) ) {
				if($feedtitle === $key1){
					$selected = ' selected';
				} else {
					$selected = '';
				}
				$form_fields["medialibraryfeeder_title"]["html"] .=  '<option value="'.$key1.'"'.$selected.'>'.$key1.'</option>';
			}
		}
	    $form_fields["medialibraryfeeder_title"]["html"] .= "</select>\n";

	    return $form_fields;

	}

	/* ==================================================
	 * Update wp_postmeta table.
	 * @since	1.0
	 */
	function attachment_field_medialibraryfeeder_save( $post, $attachment ) {

		$medialibraryfeeder_arr = array('medialibraryfeeder_apply', 'medialibraryfeeder_title');
		foreach ( $medialibraryfeeder_arr as $key ) {
			if( isset( $attachment[$key] ) ) {
		    	update_post_meta( $post['ID'], $key, $attachment[$key] );
			} else {
				delete_post_meta( $post['ID'], $key );
			}
		}

		return $post;

	}

	/* ==================================================
	 * MediaLibrary columns menu
	 * @since	1.0
	 */
	function media_columns_medialibraryfeeder($columns){
	    $columns['column_medialibraryfeeder_apply'] = __('MediaLibrary Feeder');
	    return $columns;
	}

	/* ==================================================
	 * MediaLibrary columns
	 * @since	1.0
	 */
	function media_custom_columns_medialibraryfeeder($column_name, $post_id){
		if($column_name === 'column_medialibraryfeeder_apply'){
			$medialibraryfeeder_apply = get_post_meta( $post_id, 'medialibraryfeeder_apply' );
			$medialibraryfeeder_title = get_post_meta( $post_id, 'medialibraryfeeder_title' );
			if ($medialibraryfeeder_apply[0]){
				echo '<div>'.__('Apply').'</div>';
				echo __('Feed Title', 'medialibraryfeeder').':&nbsp&nbsp&nbsp'.$medialibraryfeeder_title[0];
			} else {
				_e('None');
			}
	    }
	}

}

?>