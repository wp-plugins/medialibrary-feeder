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
	    <li><a href="#tabs-3"><?php _e('Advanced')._e('Settings'); ?></a></li>
	    <li><a href="#tabs-4"><?php _e('Caution:'); ?></a></li>

	<!--
		<li><a href="#tabs-5">FAQ</a></li>
	 -->
	  </ul>


	  <div id="tabs-1">
		<div class="wrap">
			<h2><?php _e('Settings'); ?></h2>

			<form method="post" action="<?php echo $scriptname; ?>">

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
							    <input type="checkbox" class="group_medialibraryfeeder" name="medialibraryfeeder_applys[<?php echo $attachment->ID; ?>]" value="true" <?php if ( $apply === 'true' ) { echo 'checked'; }?>>
							</td>
							<td align="left" valign="middle">
								<select name="medialibraryfeeder_titles[<?php echo $attachment->ID; ?>]">
								<?php
										foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
											if( is_array($value1) ) {
												?><option value="<?php echo $key1; ?>"<?php if($feedtitle === $key1){echo ' selected';}?>><?php echo $key1; ?></option>
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

			</form>

		</div>
	  </div>

	  <div id="tabs-2">
		<div class="wrap">
		<h2><?php _e('Registration of feed', 'medialibraryfeeder'); ?></h2>

			<form method="post" action="<?php echo $scriptname.'&#tabs-2'; ?>">

			<p>
			<input type="hidden" name="medialibraryfeeder_settings_pagemax" value="<?php echo $pagemax; ?>">
			<div><?php _e('Feed Title', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_title_new" value=""></div>
			<div><?php _e('Feed Description', 'medialibraryfeeder'); ?>:</div><textarea name="medialibraryfeeder_settings_titles_description_new" rows="2" cols="80" value=""></textarea>
			<div><?php _e('Number of feeds of the latest to publish', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_rssmax_new" value="" size="3" /></div>
			<div><?php _e('Icon Url', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_iconurl_new" value="" size="100"/></div>
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
			<td><?php _e('Feed URL', 'medialibraryfeeder'); ?></td>
			</tr>
			<?php
			$wp_uploads = wp_upload_dir();
			$wp_upload_url = $wp_uploads['baseurl'];
			foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
				if( is_array($value1) ) {
					?><tr><td>
					<?php echo $key1; ?></td>
					<?php
					foreach ( $value1 as $key2 => $value2 ) {
						if ( $key2 === 'iconurl' ) {
							?><td><img src = "<?php echo $value2; ?>"></td>
							<?php
						} elseif ( $key2 === 'description' || $key2 === 'rssmax' ){
							?><td><?php echo $value2; ?></td>
							<?php
						}
					}
					?>
					<td>
					<input type="checkbox" name="medialibraryfeeder_settings_delete_title[]" value="<?php echo $key1; ?>">
					</td>
					<?php
					$xmlurl = $wp_upload_url.'/'.md5($key1).'.xml';
					?>
					<td><input type="text" readonly="readonly" size=100 value="<?php echo $xmlurl; ?>"></td>
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

			</form>

		</div>
	  </div>

	  <div id="tabs-3">
		<div class="wrap">
		<h2><?php _e('Advanced')._e('Settings'); ?></h2>

			<?php
			$select_title = $_POST['medialibraryfeeder_settings_select_title'];
			if( empty($select_title) ) {
				$key1count = 0; 
				foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
					if( is_array($value1) ) {
						++$key1count;
						if ( $key1count == 1 ) {
							$select_title = $key1;
						}
					}
				}
			}
			?>

			<form method="post" action="<?php echo $scriptname.'&#tabs-3'; ?>">

			<p><code>&lt;title&gt;</code><?php _e('Feed Title', 'medialibraryfeeder'); ?>:<select name="medialibraryfeeder_settings_select_title">
			<?php
			foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
				if( is_array($value1) ) {
					if ( $select_title === $key1 ) {
						?><option value="<?php echo $key1; ?>" selected><?php echo $key1; ?></option><?php
					} else {
						?><option value="<?php echo $key1; ?>"><?php echo $key1; ?></option><?php
					}
				}
			}
			?>
			</select>
			<input type="submit" name="Submit" value="<?php _e('Select') ?>" />
			</p>
			<hr>

			<input type="hidden" name="medialibraryfeeder_settings_pagemax" value="<?php echo $pagemax; ?>">
			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			<?php
			$itunes_categories = array(
'Arts' => '<itunes:category text="Arts" />',
'Arts - Design' => '<itunes:category text="Arts"><itunes:category text="Design" /></itunes:category>',
'Arts - Fashion & Beauty' => '<itunes:category text="Arts"><itunes:category text="Fashion & Beauty" /></itunes:category>',
'Arts - Food' => '<itunes:category text="Arts"><itunes:category text="Food" /></itunes:category>',
'Arts - Literature' => '<itunes:category text="Arts"><itunes:category text="Literature" /></itunes:category>',
'Arts - Performing Arts' => '<itunes:category text="Arts"><itunes:category text="Performing Arts" /></itunes:category>',
'Arts - Visual Arts' => '<itunes:category text="Arts"><itunes:category text="Visual Arts" /></itunes:category>',
'Business' => '<itunes:category text="Business" />',
'Business - Business News' => '<itunes:category text="Business"><itunes:category text="Business News" /></itunes:category>',
'Business - Careers' => '<itunes:category text="Business"><itunes:category text="Careers" /></itunes:category>',
'Business - Investing' => '<itunes:category text="Business"><itunes:category text="Investing" /></itunes:category>',
'Business - Management & Marketing' => '<itunes:category text="Business"><itunes:category text="Management & Marketing" /></itunes:category>',
'Business - Shopping' => '<itunes:category text="Business"><itunes:category text="Shopping" /></itunes:category>',
'Comedy' => '<itunes:category text="Comedy" />',
'Education' => '<itunes:category text="Education" />',
'Education - Education' => '<itunes:category text="Education"><itunes:category text="Education" /></itunes:category>',
'Education - Education Technology' => '<itunes:category text="Education"><itunes:category text="Education Technology" /></itunes:category>',
'Education - Higher Education' => '<itunes:category text="Education"><itunes:category text="Higher Education" /></itunes:category>',
'Education - K-12' => '<itunes:category text="Education"><itunes:category text="K-12" /></itunes:category>',
'Education - Language Courses' => '<itunes:category text="Education"><itunes:category text="Language Courses" /></itunes:category>',
'Education - Training' => '<itunes:category text="Education"><itunes:category text="Training" /></itunes:category>',
'Games & Hobbies' => '<itunes:category text="Games & Hobbies" />',
'Games & Hobbies - Automotive' => '<itunes:category text="Games & Hobbies"><itunes:category text="Automotive" /></itunes:category>',
'Games & Hobbies - Aviation' => '<itunes:category text="Games & Hobbies"><itunes:category text="Aviation" /></itunes:category>',
'Games & Hobbies - Hobbies' => '<itunes:category text="Games & Hobbies"><itunes:category text="Hobbies" /></itunes:category>',
'Games & Hobbies - Other Games' => '<itunes:category text="Games & Hobbies"><itunes:category text="Other Games" /></itunes:category>',
'Games & Hobbies - Video Games' => '<itunes:category text="Games & Hobbies"><itunes:category text="Video Games" /></itunes:category>',
'Government & Organizations' => '<itunes:category text="Government & Organizations" />',
'Government & Organizations - Local' => '<itunes:category text="Government & Organizations"><itunes:category text="Local" /></itunes:category>',
'Government & Organizations - National' => '<itunes:category text="Government & Organizations"><itunes:category text="National" /></itunes:category>',
'Government & Organizations - Non-Profit' => '<itunes:category text="Government & Organizations"><itunes:category text="Non-Profit" /></itunes:category>',
'Government & Organizations - Regional' => '<itunes:category text="Government & Organizations"><itunes:category text="Regional" /></itunes:category>',
'Health' => '<itunes:category text="Health" />',
'Health - Alternative Health' => '<itunes:category text="Health"><itunes:category text="Alternative Health" /></itunes:category>',
'Health - Fitness & Nutrition' => '<itunes:category text="Health"><itunes:category text="Fitness & Nutrition" /></itunes:category>',
'Health - Self-Help' => '<itunes:category text="Health"><itunes:category text="Self-Help" /></itunes:category>',
'Health - Sexuality' => '<itunes:category text="Health"><itunes:category text="Sexuality" /></itunes:category>',
'Kids & Family' => '<itunes:category text="Kids & Family" />',
'Music' => '<itunes:category text="Music" />',
'News & Politics' => '<itunes:category text="News & Politics" />',
'Religion & Spirituality' => '<itunes:category text="Religion & Spirituality" />',
'Religion & Spirituality - Buddhism' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Buddhism" /></itunes:category>',
'Religion & Spirituality - Christianity' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Christianity" /></itunes:category>',
'Religion & Spirituality - Hinduism' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Hinduism" /></itunes:category>',
'Religion & Spirituality - Islam' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Islam" /></itunes:category>',
'Religion & Spirituality - Judaism' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Judaism" /></itunes:category>',
'Religion & Spirituality - Other' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Other" /></itunes:category>',
'Religion & Spirituality - Spirituality' => '<itunes:category text="Religion & Spirituality"><itunes:category text="Spirituality" /></itunes:category>',
'Science & Medicine' => '<itunes:category text="Science & Medicine" />',
'Science & Medicine - Medicine' => '<itunes:category text="Science & Medicine"><itunes:category text="Medicine" /></itunes:category>',
'Science & Medicine - Natural Sciences' => '<itunes:category text="Science & Medicine"><itunes:category text="Natural Sciences" /></itunes:category>',
'Science & Medicine - Social Sciences' => '<itunes:category text="Science & Medicine"><itunes:category text="Social Sciences" /></itunes:category>',
'Society & Culture' => '<itunes:category text="Society & Culture" />',
'Society & Culture - History' => '<itunes:category text="Society & Culture"><itunes:category text="History" /></itunes:category>',
'Society & Culture - Personal Journals' => '<itunes:category text="Society & Culture"><itunes:category text="Personal Journals" /></itunes:category>',
'Society & Culture - Philosophy' => '<itunes:category text="Society & Culture"><itunes:category text="Philosophy" /></itunes:category>',
'Society & Culture - Places & Travel' => '<itunes:category text="Society & Culture"><itunes:category text="Places & Travel" /></itunes:category>',
'Sports & Recreation' => '<itunes:category text="Sports & Recreation" />',
'Sports & Recreation - Amateur' => '<itunes:category text="Sports & Recreation"><itunes:category text="Amateur" /></itunes:category>',
'Sports & Recreation - College & High School' => '<itunes:category text="Sports & Recreation"><itunes:category text="College & High School" /></itunes:category>',
'Sports & Recreation - Outdoor' => '<itunes:category text="Sports & Recreation"><itunes:category text="Outdoor" /></itunes:category>',
'Sports & Recreation - Professional' => '<itunes:category text="Sports & Recreation"><itunes:category text="Professional" /></itunes:category>',
'Technology' => '<itunes:category text="Technology" />',
'Technology - Gadgets' => '<itunes:category text="Technology"><itunes:category text="Gadgets" /></itunes:category>',
'Technology - Tech News' => '<itunes:category text="Technology"><itunes:category text="Tech News" /></itunes:category>',
'Technology - Podcasting' => '<itunes:category text="Technology"><itunes:category text="Podcasting" /></itunes:category>',
'Technology - Software How-To' => '<itunes:category text="Technology"><itunes:category text="Software How-To" /></itunes:category>',
'TV & Film' => '<itunes:category text="TV & Film" />'
				);

			foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
				if( is_array($value1) ) {
					if ( $select_title === $key1 ) {
						?><input type="hidden" name="medialibraryfeeder_settings_titles_title" value="<?php echo $key1; ?>">
						<?php
					}
					foreach ( $value1 as $key2 => $value2 ) {
						if ( $select_title === $key1 ) {
							switch ($key2) {
								case 'description':
									?><p><div><code>&lt;description&gt;</code><?php _e('Feed Description', 'medialibraryfeeder'); ?>:</div>
									<textarea name="medialibraryfeeder_settings_titles_description" rows="2" cols="80"><?php echo $value2; ?></textarea></p>
									<?php
									break;
								case 'rssmax':
									?><p><div><?php _e('Number of feeds of the latest to publish', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_rssmax" value="<?php echo $value2; ?>" size="3" /></div></p>

									<?php
									break;
								case 'iconurl':
									?><p><div><?php _e('Icon Url', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_iconurl" value="<?php echo $value2; ?>" size="100"/></div></p>
									<?php
									break;
								case 'ttl':
									?><p><div><code>&lt;ttl&gt;</code><?php _e('Stands for time to live. It is a number of minutes.', 'medialibraryfeeder'); ?>:<input type="text" name="medialibraryfeeder_settings_titles_ttl" value="<?php echo $value2; ?>" size="3" /></div></p>
									<?php
									break;
								case 'copyright':
									?><p><div><code>&lt;copyright&gt;</code>Copyright:<input type="text" name="medialibraryfeeder_settings_titles_copyright" value="<?php echo $value2; ?>" /></div></p>
									<?php
									break;
								case 'itunes_author':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#authorId" target="_blank"><code>&lt;itunes:author&gt;</code></a>:<input type="text" name="medialibraryfeeder_settings_titles_itunes_author" value="<?php echo $value2; ?>" /></div></p>
									<?php
									break;
								case 'itunes_block':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#block" target="_blank"><code>&lt;itunes:block&gt;</code></a>
									<select name="medialibraryfeeder_settings_titles_itunes_block">
									<option value='no' <?php if($value2 === 'no'){echo 'selected';} ?>>no</option>
									<option value='yes' <?php if($value2 === 'yes'){echo 'selected';} ?>>yes</option>
									</select>
									</div></p>
									<?php
									break;
								case 'itunes_category_1':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#category" target="_blank"><code>&lt;itunes:category&gt;</code></a>
									<?php
									?>
									<select name="medialibraryfeeder_settings_titles_itunes_category_1">
									<option value=''><?php echo __('Select').'1'; ?></option>
									<?php
									foreach ( $itunes_categories as $category_name => $category_tag ) {
										?>
										<option value='<?php echo $category_tag; ?>' <?php if( stripslashes($value2) === $category_tag ){echo 'selected';} ?>><?php _e($category_name, 'medialibraryfeeder'); ?></option>
										<?php
									}
									?>
									</select>
									<?php
									break;
								case 'itunes_category_2':
									?>
									<select name="medialibraryfeeder_settings_titles_itunes_category_2">
									<option value=''><?php echo __('Select').'2'; ?></option>
									<?php
									foreach ( $itunes_categories as $category_name => $category_tag ) {
										?>
										<option value='<?php echo $category_tag; ?>' <?php if( stripslashes($value2) === $category_tag ){echo 'selected';} ?>><?php _e($category_name, 'medialibraryfeeder'); ?></option>
										<?php
									}
									?>
									</select>
									<?php
									break;
								case 'itunes_category_3':
									?>
									<select name="medialibraryfeeder_settings_titles_itunes_category_3">
									<option value=''><?php echo __('Select').'3'; ?></option>
									<?php
									foreach ( $itunes_categories as $category_name => $category_tag ) {
										?>
										<option value='<?php echo $category_tag; ?>' <?php if( stripslashes($value2) === $category_tag ){echo 'selected';} ?>><?php _e($category_name, 'medialibraryfeeder'); ?></option>
										<?php
									}
									?>
									</select></div>
									</p>
									<?php
									break;
								case 'itunes_image':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#image" target="_blank"><code>&lt;itunes:image&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_image" value="<?php echo $value2; ?>" size="100"/></div></p>
									<?php
									break;
								case 'itunes_explicit':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#explicit" target="_blank"><code>&lt;itunes:explicit&gt;</code></a>
									<select name="medialibraryfeeder_settings_titles_itunes_explicit">
									<option value='no' <?php if($value2 === 'no'){echo 'selected';} ?>>no</option>
									<option value='yes' <?php if($value2 === 'yes'){echo 'selected';} ?>>yes</option>
									<option value='clean' <?php if($value2 === 'clean'){echo 'selected';} ?>>clean</option>
									</select>
									</div></p>
									<?php
									break;
								case 'itunes_complete':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#complete" target="_blank"><code>&lt;itunes:complete&gt;</code></a>
									<select name="medialibraryfeeder_settings_titles_itunes_complete">
									<option value='no' <?php if($value2 === 'no'){echo 'selected';} ?>>no</option>
									<option value='yes' <?php if($value2 === 'yes'){echo 'selected';} ?>>yes</option>
									</select>
									</div></p>
									<?php
									break;
								case 'itunes_newfeedurl':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#newfeed" target="_blank"><code>&lt;itunes:new-feed-url&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_newfeedurl" value="<?php echo $value2; ?>" size="100"/></div></p>
									<?php
									break;
								case 'itunes_name':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#owner" target="_blank"><code>&lt;itunes:name&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_name" value="<?php echo $value2; ?>" /></div></p>
									<?php
									break;
								case 'itunes_email':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#owner" target="_blank"><code>&lt;itunes:email&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_email" value="<?php echo $value2; ?>" size="40"></div></p>
									<?php
									break;
								case 'itunes_subtitle':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#subtitle" target="_blank"><code>&lt;itunes:subtitle&gt;</code></a>
									<input type="text" name="medialibraryfeeder_settings_titles_itunes_subtitle" value="<?php echo $value2; ?>" size="40"></div></p>
									<?php
									break;
								case 'itunes_summary':
									?><p><div><a href="http://www.apple.com/itunes/podcasts/specs.html#summary" target="_blank"><code>&lt;itunes:summary&gt;</code></a>:</div>
									<textarea name="medialibraryfeeder_settings_titles_itunes_summary" rows="2" cols="80"><?php echo $value2; ?></textarea></p>
									<?php
									break;
							}
						}
					}
				}
			}
			?>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="tabs-4">
		<div class="wrap">
			<h2><?php _e('Caution:') ?></h2>
			<li><h3><?php _e('Meta-box of MediaLibrary Feeder will be added to [Edit Media]. Please do apply it. Choose a feed title. Input ituned option.', 'medialibraryfeeder'); ?></h3></li>
			<img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/images/editmedia.png'; ?>">
			<li><h3><?php _e('Widget of MediaLibrary Feeder will be added to [Widgets]. Please enter the title, put a check in the feed you want to use.', 'medialibraryfeeder'); ?></h3></li>
			<img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/images/widget.png'; ?>">
			<li><h3><?php _e('Icon can be used include the following.', 'medialibraryfeeder'); ?></h3></li>
			<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/rssfeeds.png'; ?>"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/rssfeeds.png'; ?>" size="100" /></div>
			<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/podcast.png'; ?>"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/podcast.png'; ?>" size="100" /></div>
		</div>
	  </div>

	<!--
	  <div id="tabs-5">
		<div class="wrap">
		<h2>FAQ</h2>

		</div>
	  </div>
	-->

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

		$post_title_new = $_POST['medialibraryfeeder_settings_titles_title_new'];
		$post_description_new = $_POST['medialibraryfeeder_settings_titles_description_new'];
		$post_rssmax_new = intval($_POST['medialibraryfeeder_settings_titles_rssmax_new']);
		$post_iconurl_new = $_POST['medialibraryfeeder_settings_titles_iconurl_new'];
		$post_title = $_POST['medialibraryfeeder_settings_titles_title'];
		$post_description = $_POST['medialibraryfeeder_settings_titles_description'];
		$post_rssmax = intval($_POST['medialibraryfeeder_settings_titles_rssmax']);
		$post_iconurl = $_POST['medialibraryfeeder_settings_titles_iconurl'];
		$post_ttl = intval($_POST['medialibraryfeeder_settings_titles_ttl']);
		$post_copyright = $_POST['medialibraryfeeder_settings_titles_copyright'];
		$post_itunes_author = $_POST['medialibraryfeeder_settings_titles_itunes_author'];
		$post_itunes_block = $_POST['medialibraryfeeder_settings_titles_itunes_block'];
		$post_itunes_category_1 = $_POST['medialibraryfeeder_settings_titles_itunes_category_1'];
		$post_itunes_category_2 = $_POST['medialibraryfeeder_settings_titles_itunes_category_2'];
		$post_itunes_category_3 = $_POST['medialibraryfeeder_settings_titles_itunes_category_3'];
		$post_itunes_image = $_POST['medialibraryfeeder_settings_titles_itunes_image'];
		$post_itunes_explicit = $_POST['medialibraryfeeder_settings_titles_itunes_explicit'];
		$post_itunes_complete = $_POST['medialibraryfeeder_settings_titles_itunes_complete'];
		$post_itunes_newfeedurl = $_POST['medialibraryfeeder_settings_titles_itunes_newfeedurl'];
		$post_itunes_name = $_POST['medialibraryfeeder_settings_titles_itunes_name'];
		$post_itunes_email = $_POST['medialibraryfeeder_settings_titles_itunes_email'];
		$post_itunes_subtitle = $_POST['medialibraryfeeder_settings_titles_itunes_subtitle'];
		$post_itunes_summary = $_POST['medialibraryfeeder_settings_titles_itunes_summary'];

		$delete_titles = $_POST['medialibraryfeeder_settings_delete_title'];

		$titles = FALSE;
		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
			if( is_array($value1) ) {
				foreach ( $value1 as $key2 => $value2 ) {
					$settings_tbl[$key1][$key2] = $value2;
				}
				if ( !empty($post_title) ){
					$settings_tbl[$post_title][description] = $post_description;
					$settings_tbl[$post_title][rssmax] = $post_rssmax;
					$settings_tbl[$post_title][iconurl] = $post_iconurl;
					$settings_tbl[$post_title][ttl] = $post_ttl;
					$settings_tbl[$post_title][copyright] = $post_copyright;
					$settings_tbl[$post_title][itunes_author] = $post_itunes_author;
					$settings_tbl[$post_title][itunes_block] = $post_itunes_block;
					$settings_tbl[$post_title][itunes_category_1] = $post_itunes_category_1;
					$settings_tbl[$post_title][itunes_category_2] = $post_itunes_category_2;
					$settings_tbl[$post_title][itunes_category_3] = $post_itunes_category_3;
					$settings_tbl[$post_title][itunes_image] = $post_itunes_image;
					$settings_tbl[$post_title][itunes_explicit] = $post_itunes_explicit;
					$settings_tbl[$post_title][itunes_complete] = $post_itunes_complete;
					$settings_tbl[$post_title][itunes_newfeedurl] = $post_itunes_newfeedurl;
					$settings_tbl[$post_title][itunes_name] = $post_itunes_name;
					$settings_tbl[$post_title][itunes_email] = $post_itunes_email;
					$settings_tbl[$post_title][itunes_subtitle] = $post_itunes_subtitle;
					$settings_tbl[$post_title][itunes_summary] = $post_itunes_summary;
				}
				$titles = TRUE;
			}
		}

		if ( !empty($post_title_new) && !empty($post_description_new) && !empty($post_rssmax_new) && !empty($post_iconurl_new) ){
			$settings_tbl[$post_title_new][description] = $post_description_new;
			$settings_tbl[$post_title_new][rssmax] = $post_rssmax_new;
			$settings_tbl[$post_title_new][iconurl] = $post_iconurl_new;

			$blog_description =  get_bloginfo ( 'description' );
			$blogusers = get_users();
			$copyright = $blogusers[0]->display_name;
			$itunes_author = $copyright;
			$itunes_name = $copyright;
			$itunes_email = $blogusers[0]->user_email;

			$settings_tbl[$post_title_new][ttl] = 60;
			$settings_tbl[$post_title_new][copyright] = $copyright;
			$settings_tbl[$post_title_new][itunes_author] = $itunes_author;
			$settings_tbl[$post_title_new][itunes_block] = 'no';
			$settings_tbl[$post_title_new][itunes_category_1] = '';
			$settings_tbl[$post_title_new][itunes_category_2] = '';
			$settings_tbl[$post_title_new][itunes_category_3] = '';
			$settings_tbl[$post_title_new][itunes_image] = '';
			$settings_tbl[$post_title_new][itunes_explicit] = 'no';
			$settings_tbl[$post_title_new][itunes_complete] = 'no';
			$settings_tbl[$post_title_new][itunes_newfeedurl] = '';
			$settings_tbl[$post_title_new][itunes_name] = $itunes_name;
			$settings_tbl[$post_title_new][itunes_email] = $itunes_email;
			$settings_tbl[$post_title_new][itunes_subtitle] = '';
			$settings_tbl[$post_title_new][itunes_summary] = $blog_description;
		}

		if ( $titles ) {
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

		// for delete post meta
		$medialibraryfeeder_arr = array(
									'medialibraryfeeder_apply',
									'medialibraryfeeder_title',
									'medialibraryfeeder_itunes_author',
									'medialibraryfeeder_itunes_block',
									'medialibraryfeeder_itunes_image',
									'medialibraryfeeder_itunes_explicit',
									'medialibraryfeeder_itunes_isClosedCaptioned',
									'medialibraryfeeder_itunes_order',
									'medialibraryfeeder_itunes_subtitle',
									'medialibraryfeeder_itunes_summary'
								);

		if ( !empty($medialibraryfeeder_applys) ) {
			foreach ( $medialibraryfeeder_applys as $key => $value ) {
				if ( $value === 'true' ) {
			    	update_post_meta( $key, 'medialibraryfeeder_apply', $value );
				} else {
					foreach ( $medialibraryfeeder_arr as $medialibrary_meta ) {
						delete_post_meta( $key, $medialibrary_meta );
					}
				}
			}
		}

		if ( !empty($medialibraryfeeder_titles) ) {
			foreach ( $medialibraryfeeder_titles as $key => $value ) {
				if ( !empty($value) && get_post_meta( $key, 'medialibraryfeeder_apply', true  ) === 'true' ) {
			    	update_post_meta( $key, 'medialibraryfeeder_title', $value );
				}
			}
		}

		$args = array(
			'post_type' => 'attachment',
			'numberposts' => -1,
			'post_parent' => $post->ID
			); 
		$attachments = get_posts($args);
		foreach ( $attachments as $attachment ) {
			$feedtitles[$attachment->ID] = get_post_meta( $attachment->ID, "medialibraryfeeder_title", true );
		}
		if ( !empty($delete_titles) ) {
			foreach ( $delete_titles as $delete_title ) {
				foreach ( $feedtitles as $key => $value ) {
					if ( $delete_title === $value ) {
						foreach ( $medialibraryfeeder_arr as $medialibrary_meta ) {
							delete_post_meta( $key, $medialibrary_meta );
						}
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

		echo '<h3>MediaLibrary Feeder</h3>';

	    // checkbox
	    $apply = get_post_meta( $post->ID, 'medialibraryfeeder_apply', true );
	    $form_fields["medialibraryfeeder_apply"]["label"] = __('Apply');
	    $form_fields["medialibraryfeeder_apply"]["input"] = "html";
	    $form_fields["medialibraryfeeder_apply"]["html"]  = "<input type='checkbox' name='attachments[{$post->ID}][medialibraryfeeder_apply]' value='true'";
	    $form_fields["medialibraryfeeder_apply"]["html"] .= ( $apply === 'true' )? " checked":"";
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

		$ext = end(explode( '.', wp_get_attachment_url($post->ID) ));
		if ( $ext === 'm4a' || $ext === 'mp3' || $ext === 'mov' || $ext === 'mp4' || $ext === 'm4v' || $ext === 'pdf' || $ext === 'epub' ) {
			// text
			$blogusers = get_users($post->ID);
			$author_name = $blogusers[0]->display_name;
			$itunes_author = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_author', true );
			if ( empty($itunes_author) ) { $itunes_author = $author_name; }
		    $form_fields["medialibraryfeeder_itunes_author"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#authorId" target="_blank"><code>&lt;itunes:author&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_author"]["input"] = "text";
			$form_fields["medialibraryfeeder_itunes_author"]["value"] = $itunes_author;

			// select
			$itunes_block = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_block', true );
			$form_fields["medialibraryfeeder_itunes_block"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#block" target="_blank"><code>&lt;itunes:block&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_block"]["input"] = "html";
			$form_fields["medialibraryfeeder_itunes_block"]["html"]  = "<select name='attachments[{$post->ID}][medialibraryfeeder_itunes_block]' id='attachments[{$post->ID}][medialibraryfeeder_itunes_block]'>\n";
			$form_fields["medialibraryfeeder_itunes_block"]["html"] .= ( $itunes_block == 'no' )? "<option value='no' selected>no</option>\n":"<option value='no'>no</option>\n";
			$form_fields["medialibraryfeeder_itunes_block"]["html"] .= ( $itunes_block == 'yes' )? "<option value='yes' selected>yes</option>\n":"<option value='yes'>yes</option>\n";
			$form_fields["medialibraryfeeder_itunes_block"]["html"] .= "</select>\n";

			// text
		    $itunes_image = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_image', true );
		    $form_fields["medialibraryfeeder_itunes_image"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#image" target="_blank"><code>&lt;itunes:image&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_image"]["input"] = "html";
		    $form_fields["medialibraryfeeder_itunes_image"]["html"]  = "<input type='text' class='text' id='attachments-{$post->ID}-medialibraryfeeder_itunes_image' name='attachments[{$post->ID}][medialibraryfeeder_itunes_image]' value='$itunes_image' size='80' />\n";

			// select
			$itunes_explicit = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_explicit', true );
			$form_fields["medialibraryfeeder_itunes_explicit"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#explicit" target="_blank"><code>&lt;itunes:explicit&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_explicit"]["input"] = "html";
			$form_fields["medialibraryfeeder_itunes_explicit"]["html"]  = "<select name='attachments[{$post->ID}][medialibraryfeeder_itunes_explicit]' id='attachments[{$post->ID}][medialibraryfeeder_itunes_explicit]'>\n";
			$form_fields["medialibraryfeeder_itunes_explicit"]["html"] .= ( $itunes_explicit == 'no' )? "<option value='no' selected>no</option>\n":"<option value='no'>no</option>\n";
			$form_fields["medialibraryfeeder_itunes_explicit"]["html"] .= ( $itunes_explicit == 'yes' )? "<option value='yes' selected>yes</option>\n":"<option value='yes'>yes</option>\n";
			$form_fields["medialibraryfeeder_itunes_explicit"]["html"] .= ( $itunes_explicit == 'clean' )? "<option value='clean' selected>clean</option>\n":"<option value='clean'>clean</option>\n";
			$form_fields["medialibraryfeeder_itunes_explicit"]["html"] .= "</select>\n";

			// select
			$itunes_isClosedCaptioned = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_isClosedCaptioned', true );
			$form_fields["medialibraryfeeder_itunes_isClosedCaptioned"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#isClosedCaptioned" target="_blank"><code>&lt;itunes:isClosedCaptioned&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_isClosedCaptioned"]["input"] = "html";
			$form_fields["medialibraryfeeder_itunes_isClosedCaptioned"]["html"]  = "<select name='attachments[{$post->ID}][medialibraryfeeder_itunes_isClosedCaptioned]' id='attachments[{$post->ID}][medialibraryfeeder_itunes_isClosedCaptioned]'>\n";
			$form_fields["medialibraryfeeder_itunes_isClosedCaptioned"]["html"] .= ( $itunes_isClosedCaptioned == 'no' )? "<option value='no' selected>no</option>\n":"<option value='no'>no</option>\n";
			$form_fields["medialibraryfeeder_itunes_isClosedCaptioned"]["html"] .= ( $itunes_isClosedCaptioned == 'yes' )? "<option value='yes' selected>yes</option>\n":"<option value='yes'>yes</option>\n";
			$form_fields["medialibraryfeeder_itunes_isClosedCaptioned"]["html"] .= "</select>\n";

			// text
		    $form_fields["medialibraryfeeder_itunes_order"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#order" target="_blank"><code>&lt;itunes:order&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_order"]["input"] = "text";
			$form_fields["medialibraryfeeder_itunes_order"]["value"] = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_order', true );

			// text
		    $itunes_subtitle = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_subtitle', true );
		    $form_fields["medialibraryfeeder_itunes_subtitle"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#subtitle" target="_blank"><code>&lt;itunes:subtitle&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_subtitle"]["input"] = "html";
		    $form_fields["medialibraryfeeder_itunes_subtitle"]["html"]  = "<input type='text' class='text' id='attachments-{$post->ID}-medialibraryfeeder_itunes_subtitle' name='attachments[{$post->ID}][medialibraryfeeder_itunes_subtitle]' value='$itunes_subtitle' size='80' />\n";

			// textarea
			$itunes_summary = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_summary', true );
			$form_fields["medialibraryfeeder_itunes_summary"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#summary" target="_blank"><code>&lt;itunes:summary&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_summary"]["input"] = "html";
			$form_fields["medialibraryfeeder_itunes_summary"]["html"] = "<textarea id='attachments-{$post->ID}-medialibraryfeeder_itunes_summary' name='attachments[{$post->ID}][medialibraryfeeder_itunes_summary]' rows='4' cols='80'>$itunes_summary</textarea>\n";
		}

	    return $form_fields;

	}

	/* ==================================================
	 * Update wp_postmeta table.
	 * @since	1.0
	 */
	function attachment_field_medialibraryfeeder_save( $post, $attachment ) {

		$medialibraryfeeder_arr = array(
									'medialibraryfeeder_apply',
									'medialibraryfeeder_title',
									'medialibraryfeeder_itunes_author',
									'medialibraryfeeder_itunes_block',
									'medialibraryfeeder_itunes_image',
									'medialibraryfeeder_itunes_explicit',
									'medialibraryfeeder_itunes_isClosedCaptioned',
									'medialibraryfeeder_itunes_order',
									'medialibraryfeeder_itunes_subtitle',
									'medialibraryfeeder_itunes_summary'
								);
		foreach ( $medialibraryfeeder_arr as $key ) {
			if( isset( $attachment[$key] ) ) {
				if ( $attachment[medialibraryfeeder_apply] === 'true' ) {
		    		update_post_meta( $post['ID'], $key, $attachment[$key] );
				} else {
					delete_post_meta( $post['ID'], $key );
				}
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
			if ($medialibraryfeeder_apply[0] === 'true'){
				echo '<div>'.__('Apply').'</div>';
				echo __('Feed Title', 'medialibraryfeeder').':&nbsp&nbsp&nbsp'.$medialibraryfeeder_title[0];
			} else {
				_e('None');
			}
	    }
	}

}

?>