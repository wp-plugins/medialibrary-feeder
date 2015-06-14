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
			$links[] = '<a href="'.admin_url('admin.php?page=medialibraryfeeder').'">MediaLibrary Feeder</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_menu() {
		add_menu_page(
				'MediaLibrary Feeder',
				'MediaLibrary Feeder',
				'manage_options',
				'medialibraryfeeder',
				array($this, 'manage_page')
		);
		add_submenu_page(
				'medialibraryfeeder',
				__('Feeds Management', 'medialibraryfeeder'),
				__('Feeds Management', 'medialibraryfeeder'),
				'manage_options',
				'medialibraryfeeder-registration-feed',
				array($this, 'registration_feed')
		);
		add_submenu_page(
				'medialibraryfeeder',
				__('Advanced').__('Settings'),
				__('Advanced').__('Settings'),
				'manage_options',
				'medialibraryfeeder-advanced-settings',
				array($this, 'advanced_settings')
		);
		add_submenu_page(
				'medialibraryfeeder',
				__('Register the media to feeds', 'medialibraryfeeder'),
				__('Register the media to feeds', 'medialibraryfeeder'),
				'manage_options',
				'medialibraryfeeder-search-register',
				array($this, 'register_media_to_feeds')
		);
		add_submenu_page(
				'medialibraryfeeder',
				__('Other Notes', 'medialibraryfeeder'),
				__('Other Notes', 'medialibraryfeeder'),
				'manage_options',
				'medialibraryfeeder-other-notes',
				array($this, 'other_notes')
		);
	}

	/* ==================================================
	 * Add Css and Script
	 * @since	2.9
	 */
	function load_custom_wp_admin_style() {
		wp_enqueue_style( 'jquery-responsiveTabs', MEDIALIBRARYFEEDER_PLUGIN_URL.'/css/responsive-tabs.css' );
		wp_enqueue_style( 'jquery-responsiveTabs-style', MEDIALIBRARYFEEDER_PLUGIN_URL.'/css/style.css' );
		wp_enqueue_style( 'medialibraryfeeder-admin-style', MEDIALIBRARYFEEDER_PLUGIN_URL.'/css/medialibrary-feeder-admin.css' );
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-responsiveTabs', MEDIALIBRARYFEEDER_PLUGIN_URL.'/js/jquery.responsiveTabs.min.js' );
	}

	/* ==================================================
	 * Add Script on footer
	 * @since	2.9
	 */
	function load_custom_wp_admin_style2() {
		echo $this->add_jscss();
	}

	/* ==================================================
	 * Main
	 */
	function manage_page() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$plugin_datas = get_file_data( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/medialibraryfeeder.php', array('version' => 'Version') );
		$plugin_version = __('Version:').' '.$plugin_datas['version'];

		?>

		<div class="wrap">

		<h2 style="float: left;">MediaLibrary Feeder</h2>
		<div style="display: block; padding: 10px 10px;">
			<form method="post" style="float: left; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-registration-feed'); ?>">
				<input type="submit" class="button" value="<?php _e('Feeds Management', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: left; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-advanced-settings'); ?>" />
				<input type="submit" class="button" value="<?php echo __('Advanced').__('Settings'); ?>" />
			</form>
			<form method="post" style="float: left; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Register the media to feeds', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-other-notes'); ?>" />
				<input type="submit" class="button" value="<?php _e('Other Notes', 'medialibraryfeeder'); ?>" />
			</form>
		</div>
		<div style="clear: both;"></div>

		<h3><?php _e('Output as feed the media library. Generate a podcast for iTunes Store. It can be displayed to each feed using a shortcode.', 'medialibraryfeeder'); ?></h3>
		<h4 style="margin: 5px; padding: 5px;">
		<?php echo $plugin_version; ?> |
		<a style="text-decoration: none;" href="https://wordpress.org/support/plugin/medialibrary-feeder" target="_blank"><?php _e('Support Forums') ?></a> |
		<a style="text-decoration: none;" href="https://wordpress.org/support/view/plugin-reviews/medialibrary-feeder" target="_blank"><?php _e('Reviews', 'medialibraryfeeder') ?></a>
		</h4>

		<div style="width: 250px; height: 170px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', 'medialibraryfeeder'); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
<a style="margin: 5px; padding: 5px;" href='https://pledgie.com/campaigns/28307' target="_blank"><img alt='Click here to lend your support to: Various Plugins for WordPress and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/28307.png?skin_name=chrome' border='0' ></a>
		</div>

		</div>
		<?php
	}

	/* ==================================================
	 * Sub Menu
	 */
	function registration_feed() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/inc/MediaLibraryFeeder.php' );
		$medialibraryfeeder = new MediaLibraryFeeder();

		$submenu = 1;

		if( !empty($_POST) ) { 
			$this->options_updated($submenu);
			$this->post_meta_updated($submenu);
			$medialibraryfeeder->generate_feed();
		}

		$scriptname = admin_url('admin.php?page=medialibraryfeeder-registration-feed');

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		$pagemax = $medialibraryfeeder_settings['pagemax'];
		?>
		<div class="wrap">
		<h2>MediaLibrary Feeder <?php _e('Feeds Management', 'medialibraryfeeder'); ?>
			<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-other-notes'); ?>" />
				<input type="submit" class="button" value="<?php _e('Other Notes', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Register the media to feeds', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-advanced-settings'); ?>" />
				<input type="submit" class="button" value="<?php echo __('Advanced').__('Settings'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="medialibraryfeeder-loading"><img src="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL; ?>/css/loading.gif"></div>
		<div id="medialibraryfeeder-loading-container">

			<form method="post" action="<?php echo $scriptname; ?>">

			<input type="hidden" name="medialibraryfeeder_settings_pagemax" value="<?php echo $pagemax; ?>">
			<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
				<div style="display:block;padding:5px 0">
				<?php _e('Feed Title', 'medialibraryfeeder'); ?>
				<input type="text" name="medialibraryfeeder_settings_titles_title_new" value="">
				</div>
				<div style="display:block;padding:5px 0">
				<?php _e('Feed Description', 'medialibraryfeeder'); ?>
				<textarea name="medialibraryfeeder_settings_titles_description_new" style="width: 100%;" value=""></textarea>
				</div>
				<div style="display:block;padding:5px 0">
				<?php _e('Number of feeds of the latest to publish', 'medialibraryfeeder'); ?>
				<input type="text" name="medialibraryfeeder_settings_titles_rssmax_new" value="" size="3" />
				</div>
				<div style="display:block;padding:5px 0">
				<?php _e('Icon', 'medialibraryfeeder'); ?>
				<span style="margin-right: 1em;"></span>
				<input type="radio" name="medialibraryfeeder_settings_titles_iconurl_new" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds.png'; ?>"><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds.png'; ?>" align="middle">
				<span style="margin-right: 1em;"></span>
				<input type="radio" name="medialibraryfeeder_settings_titles_iconurl_new" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-20x20.png'; ?>"><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-20x20.png'; ?>" align="middle">
				<span style="margin-right: 1em;"></span>
				<input type="radio" name="medialibraryfeeder_settings_titles_iconurl_new" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-80x80.png'; ?>"><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-80x80.png'; ?>" align="middle">
				<span style="margin-right: 1em;"></span>
				<input type="radio" name="medialibraryfeeder_settings_titles_iconurl_new" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast.png'; ?>" checked><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast.png'; ?>" align="middle">
				<span style="margin-right: 1em;"></span>
				<input type="radio" name="medialibraryfeeder_settings_titles_iconurl_new" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-20x20.png'; ?>"><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-20x20.png'; ?>" align="middle">
				<span style="margin-right: 1em;"></span>
				<input type="radio" name="medialibraryfeeder_settings_titles_iconurl_new" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-80x80.png'; ?>"><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-80x80.png'; ?>" align="middle">
				</div>
				<div style="padding-top: 5px; padding-bottom: 5px;">
				  <input type="submit" class="button-primary button-large" name="FeedRegist" value="<?php _e('Registration of feed', 'medialibraryfeeder') ?>" />
				</div>
				<div style="clear:both"></div>
			</div>

			<h2><?php _e('Feed registered', 'medialibraryfeeder'); ?></h2>

			<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
			<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
			<?php echo __('Delete').__('Select'); ?> & <?php _e('Icon', 'medialibraryfeeder'); ?> & <?php _e('Feed', 'medialibraryfeeder'); ?>
			</div>
			<?php
			foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
				if( is_array($value1) ) {
					$title_html = NULL;
					$icon_html = NULL;
					$description_html = NULL;
					$rssmax_html = NULL;
					$xml_html = NULL;
					?>
					<input type="checkbox" name="medialibraryfeeder_settings_delete_title[]" value="<?php echo $key1; ?>" style="float: left; margin: 5px;">
					<?php
					$title_html = __('Feed Title', 'medialibraryfeeder').': '.$key1;
					foreach ( $value1 as $key2 => $value2 ) {
						if ( $key2 === 'iconurl' ) {
							$icon_html = '<img src = "'.$value2.'" style="float: left; margin: 5px;">';
						} elseif ( $key2 === 'description' ){
							$description_html = '<div>'.__('Feed Description', 'medialibraryfeeder').': '.$value2.'</div>';
						} elseif ( $key2 === 'rssmax' ){
							$rssmax_html = '<div>'.__('Number of feeds of the latest to publish', 'medialibraryfeeder').': '.$value2.'</div>';
						}
					}
					$xmlurl = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_URL.'/'.md5($key1).'.xml';
					$xml_html = '<div>'.__('Feed URL', 'medialibraryfeeder').': <a href="'.$xmlurl.'" target="_blank">'.$xmlurl.'</a></div>';
					echo $icon_html.'<div style="overflow: hidden;">'.$title_html.$description_html.$rssmax_html.$xml_html;
					?>
					<form method="post" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-advanced-settings'); ?>">
					<input type="hidden" name="medialibraryfeeder_settings_select_title" value="<?php echo $key1; ?>">
					<input type="submit" class="button" name="FeedSelect" value="<?php echo __('Advanced').__('Settings'); ?>" />
					</form>
					</div>
					<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;"></div>
					<div style="clear: both;"></div>
					<?php
				}
			}
			?>
			<?php echo __('Delete').__('Select'); ?> & <?php _e('Icon', 'medialibraryfeeder'); ?> & <?php _e('Feed', 'medialibraryfeeder'); ?>

			<div style="padding-top: 10px; padding-bottom: 5px;">
			  <input type="submit" class="button-primary button-large" name="FeedDelete" value="<?php _e('Delete Feeds', 'medialibraryfeeder') ?>" />
			</div>

			</div>

			</form>

		</div>
		</div>
		<?php
	}

	/* ==================================================
	 * Sub Menu
	 */
	function advanced_settings() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/inc/MediaLibraryFeeder.php' );
		$medialibraryfeeder = new MediaLibraryFeeder();

		$submenu = 2;
		if( !empty($_POST) ) {
			if( !empty($_POST['AdvancedSettingsSave']) ) {
				$this->options_updated($submenu);
				$this->post_meta_updated($submenu);
				$medialibraryfeeder->generate_feed();
			}
		}

		$scriptname = admin_url('admin.php?page=medialibraryfeeder-advanced-settings');

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		$pagemax = $medialibraryfeeder_settings['pagemax'];

		?>
		<div class="wrap">
		<h2>MediaLibrary Feeder <?php echo __('Advanced').__('Settings'); ?>
			<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-other-notes'); ?>" />
				<input type="submit" class="button" value="<?php _e('Other Notes', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Register the media to feeds', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-registration-feed'); ?>">
				<input type="submit" class="button" value="<?php _e('Feeds Management', 'medialibraryfeeder'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="medialibraryfeeder-loading"><img src="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL; ?>/css/loading.gif"></div>
		<div id="medialibraryfeeder-loading-container">

			<?php
			if(isset($_POST['medialibraryfeeder_settings_select_title'])){ $select_title = $_POST['medialibraryfeeder_settings_select_title']; }
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

			<form method="post" action="<?php echo $scriptname; ?>">

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
			<input type="submit" class="button" name="FeedSelect" value="<?php _e('Select') ?>" />
			</p>
			<hr>

			<div style="padding-top: 5px; padding-bottom: 5px;">
			  <input type="submit" class="button-primary button-large" name="AdvancedSettingsSave" value="<?php _e('Save Changes') ?>" />
			</div>

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
									?>
									<div style="display:block;padding:5px 0">
									<code>&lt;description&gt;</code><?php _e('Feed Description', 'medialibraryfeeder'); ?>
									<textarea name="medialibraryfeeder_settings_titles_description" style="width: 100%;"><?php echo $value2; ?></textarea>
									</div>
									<?php
									break;
								case 'rssmax':
									?>
									<div style="display:block;padding:5px 0">
									<?php _e('Number of feeds of the latest to publish', 'medialibraryfeeder'); ?><input type="text" name="medialibraryfeeder_settings_titles_rssmax" value="<?php echo $value2; ?>" size="3" />
									</div>
									<?php
									break;
								case 'iconurl':
									?>
									<div style="display:block;padding:5px 0">
									<?php _e('Icon Url', 'medialibraryfeeder'); ?><input type="text" name="medialibraryfeeder_settings_titles_iconurl" value="<?php echo $value2; ?>" style="width: 100%;"/>
									</div>
									<?php
									break;
								case 'ttl':
									?>
									<div style="display:block;padding:5px 0">
									<code>&lt;ttl&gt;</code><?php _e('Stands for time to live. It is a number of minutes.', 'medialibraryfeeder'); ?><input type="text" name="medialibraryfeeder_settings_titles_ttl" value="<?php echo $value2; ?>" size="3" /></div>
									<?php
									break;
								case 'copyright':
									?>
									<div style="display:block;padding:5px 0">
									<code>&lt;copyright&gt;</code>Copyright<input type="text" name="medialibraryfeeder_settings_titles_copyright" value="<?php echo $value2; ?>" /></div>
									<?php
									break;
								case 'itunes_author':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#authorId" target="_blank"><code>&lt;itunes:author&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_author" value="<?php echo $value2; ?>" /></div>
									<?php
									break;
								case 'itunes_block':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#block" target="_blank"><code>&lt;itunes:block&gt;</code></a>
									<select name="medialibraryfeeder_settings_titles_itunes_block">
									<option value='no' <?php if($value2 === 'no'){echo 'selected';} ?>>no</option>
									<option value='yes' <?php if($value2 === 'yes'){echo 'selected';} ?>>yes</option>
									</select>
									</div>
									<?php
									break;
								case 'itunes_category_1':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#category" target="_blank"><code>&lt;itunes:category&gt;</code></a>
									<?php
									?>
									<select style="width: 250px;" name="medialibraryfeeder_settings_titles_itunes_category_1">
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
									<select style="width: 250px;" name="medialibraryfeeder_settings_titles_itunes_category_2">
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
									<select style="width: 250px;" name="medialibraryfeeder_settings_titles_itunes_category_3">
									<option value=''><?php echo __('Select').'3'; ?></option>
									<?php
									foreach ( $itunes_categories as $category_name => $category_tag ) {
										?>
										<option value='<?php echo $category_tag; ?>' <?php if( stripslashes($value2) === $category_tag ){echo 'selected';} ?>><?php _e($category_name, 'medialibraryfeeder'); ?></option>
										<?php
									}
									?>
									</select>
									</div>
									<?php
									break;
								case 'itunes_image':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#image" target="_blank"><code>&lt;itunes:image&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_image" value="<?php echo $value2; ?>" style="width: 100%;"/>
									</div>
									<?php
									break;
								case 'itunes_explicit':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#explicit" target="_blank"><code>&lt;itunes:explicit&gt;</code></a>
									<select name="medialibraryfeeder_settings_titles_itunes_explicit">
									<option value='no' <?php if($value2 === 'no'){echo 'selected';} ?>>no</option>
									<option value='yes' <?php if($value2 === 'yes'){echo 'selected';} ?>>yes</option>
									<option value='clean' <?php if($value2 === 'clean'){echo 'selected';} ?>>clean</option>
									</select>
									</div>
									<?php
									break;
								case 'itunes_complete':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#complete" target="_blank"><code>&lt;itunes:complete&gt;</code></a>
									<select name="medialibraryfeeder_settings_titles_itunes_complete">
									<option value='no' <?php if($value2 === 'no'){echo 'selected';} ?>>no</option>
									<option value='yes' <?php if($value2 === 'yes'){echo 'selected';} ?>>yes</option>
									</select>
									</div>
									<?php
									break;
								case 'itunes_newfeedurl':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#newfeed" target="_blank"><code>&lt;itunes:new-feed-url&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_newfeedurl" value="<?php echo $value2; ?>" style="width: 100%;" />
									</div>
									<?php
									break;
								case 'itunes_name':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#owner" target="_blank"><code>&lt;itunes:name&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_name" value="<?php echo $value2; ?>" />
									</div>
									<?php
									break;
								case 'itunes_email':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#owner" target="_blank"><code>&lt;itunes:email&gt;</code></a><input type="text" name="medialibraryfeeder_settings_titles_itunes_email" value="<?php echo $value2; ?>">
									</div>
									<?php
									break;
								case 'itunes_subtitle':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#subtitle" target="_blank"><code>&lt;itunes:subtitle&gt;</code></a>
									<input type="text" name="medialibraryfeeder_settings_titles_itunes_subtitle" value="<?php echo $value2; ?>"></div>
									<?php
									break;
								case 'itunes_summary':
									?>
									<div style="display:block;padding:5px 0">
									<a href="http://www.apple.com/itunes/podcasts/specs.html#summary" target="_blank"><code>&lt;itunes:summary&gt;</code></a>
									<textarea name="medialibraryfeeder_settings_titles_itunes_summary" style="width: 100%;" ><?php echo $value2; ?></textarea>
									</div>
									<?php
									break;
							}
						}
					}
				}
			}
			?>

			<div style="padding-top: 5px; padding-bottom: 5px;">
			  <input type="submit" class="button-primary button-large" name="AdvancedSettingsSave" value="<?php _e('Save Changes') ?>" />
			</div>

			</form>
		</div>
		</div>
		<?php
	}

	/* ==================================================
	 * Sub Menu
	 */
	function register_media_to_feeds() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/inc/MediaLibraryFeeder.php' );
		$medialibraryfeeder = new MediaLibraryFeeder();

		$submenu = 3;
		if( !empty($_POST) ) { 
			if( !empty($_POST['ShowToPage']) ) { 
				$this->options_updated($submenu);
			}
			if( !empty($_POST['UpdateFeed']) ) { 
				$this->post_meta_updated($submenu);
				$medialibraryfeeder->generate_feed();
				echo '<div class="updated"><ul><li>'.__('Feeds was updated.', 'medialibraryfeeder').'</li></ul></div>';
			}
		}
		$mimefilter = NULL;
		if( !empty($_GET['mime']) ) {
			$mimefilter = $_GET['mime'];
		}
		if( !empty($_POST['mime']) ) {
			$mimefilter = $_POST['mime'];
		}

		$scriptname = admin_url('admin.php?page=medialibraryfeeder-search-register');

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		$pagemax = $medialibraryfeeder_settings['pagemax'];

		?>

		<div class="wrap">

		<h2>MediaLibrary Feeder <?php _e('Register the media to feeds', 'medialibraryfeeder'); ?>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-other-notes'); ?>" />
				<input type="submit" class="button" value="<?php _e('Other Notes', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-advanced-settings'); ?>" />
				<input type="submit" class="button" value="<?php echo __('Advanced').__('Settings'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-registration-feed'); ?>">
				<input type="submit" class="button" value="<?php _e('Feeds Management', 'medialibraryfeeder'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="medialibraryfeeder-loading"><img src="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL; ?>/css/loading.gif"></div>
		<div id="medialibraryfeeder-loading-container">

			<form method="post" action="<?php echo $scriptname; ?>">

			<?php

			global $wpdb;
			$postmimetype = NULL;
			if ( !empty($mimefilter) ) {
				$postmimetype = "and post_mime_type = '".$mimefilter."'";
			}
			$attachments = $wpdb->get_results("
							SELECT	ID, post_title, guid, post_date
							FROM	$wpdb->posts
							WHERE	post_type = 'attachment'
									$postmimetype
									ORDER BY post_date DESC
							");

			?>

				<div style="float:left;"><?php _e('Number of items per page:'); ?><input type="text" name="medialibraryfeeder_settings_pagemax" value="<?php echo $pagemax; ?>" size="3" /></div>
				<input type="submit" class="button" name="ShowToPage" value="<?php _e('Save') ?>" />
				<div style="clear: both;"></div>
				<div>
					<select name="mime" style="width: 180px;">
					<option value=""><?php echo esc_attr( __( 'All Mime types', 'medialibraryfeeder' ) ); ?></option>
					<?php
					foreach ( wp_get_mime_types() as $exts => $mime ) {
						?>
						<option value="<?php echo esc_attr($mime); ?>"<?php if ($mimefilter === $mime) echo ' selected';?>><?php echo esc_attr($mime); ?></option>
						<?php
					}
					?>
					</select>
					<input type="submit" class="button" value="<?php _e('Filter'); ?>">
				</div>

			<div style="clear: both;"></div>
			<?php

			$pageallcount = 0;
			// pagenation
			foreach ( $attachments as $attachment ) {
				++$pageallcount;
			}
			if (!empty($_GET['p'])){
				$page = $_GET['p'];
			} else if (!empty($_POST['p'])){
				$page = $_POST['p'];
			} else {
				$page = 1;
			}
			$count = 0;
			$pagebegin = (($page - 1) * $pagemax) + 1;
			$pageend = $page * $pagemax;
			$pagelast = ceil($pageallcount / $pagemax);

			if ( $pageallcount > 0 ) {
				if ( $pagelast > 1 ) {
					$this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $mimefilter);
				}
				?>
				<div style="padding-top: 5px; padding-bottom: 5px;">
				<input type="submit" class="button-primary button-large" name="UpdateFeed" value="<?php _e('Update Feeds', 'medialibraryfeeder'); ?>" />
				</div>
				<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
				<input type="checkbox" id="group_medialibraryfeeder" class="medialibraryfeeder-admin-checkAll"><?php _e('Select all'); ?>
				</div>
				<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
				<?php _e('Select'); ?> & <?php _e('Thumbnail'); ?> & <?php _e('Metadata'); ?> & <?php _e('Feed', 'medialibraryfeeder'); ?>
				</div>
				<?php
				foreach ( $attachments as $attachment ) {
					++$count;
				    $apply = get_post_meta( $attachment->ID, 'medialibraryfeeder_apply', true );
					$feedtitle = get_post_meta( $attachment->ID, "medialibraryfeeder_title", true );
					if ( $pagebegin <= $count && $count <= $pageend ) {
						$attach_id = $attachment->ID;

						$title = $attachment->post_title;
						$url_attach = wp_get_attachment_url( $attach_id );
						$exts = explode('.', $url_attach);
						$ext = end($exts);

						$metadata = NULL;
						list($imagethumburls, $mimetype, $length, $thumbnail_img_url, $stamptime, $file_size) = $medialibraryfeeder->getmeta($ext, $attach_id, $metadata);

						$input_html = NULL;
					    $input_html = '<input type="hidden" class="group_medialibraryfeeder" name="medialibraryfeeder_applys['.$attach_id.']" value="false">';
						$input_html .= '<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">';
						$applycheck = NULL;
						if ( $apply === 'true' ) { $applycheck = 'checked'; }
						$input_html .= '<input type="checkbox" class="group_medialibraryfeeder" name="medialibraryfeeder_applys['.$attach_id.']" value="true" style="float: left; margin: 5px;" '.$applycheck.' >';
						$input_html .= '<img width="40" height="40" src="'.$thumbnail_img_url.'" style="float: left; margin: 5px;">';
						$input_html .= '<div style="overflow: hidden;">';
						$input_html .= '<div>'.__('Title').': '.$title.'</div>';
						$input_html .= '<div>'.__('Permalink:').' <a href="'.get_attachment_link($attach_id).'" target="_blank" style="text-decoration: none; word-break: break-all;">'.get_attachment_link($attach_id).'</a></div>';
						$input_html .= '<div>URL: <a href="'.$url_attach.'" target="_blank" style="text-decoration: none; word-break: break-all;">'.$url_attach.'</a></div>';
						$url_attachs = explode('/', $url_attach);
						$input_html .= '<div>'.__('File name:').' '.end($url_attachs).'</div>';

						$input_html .= '<div>'.__('Date/Time').': '.$stamptime.'</div>';
						if ( wp_ext2type($ext) === 'image' ) {
							$input_html .= '<div>'.__('Images').': ';
							foreach ( $imagethumburls as $thumbsize => $imagethumburl ) {
								$input_html .= '[<a href="'.$imagethumburl.'" target="_blank" style="text-decoration: none; word-break: break-all;">'.$thumbsize.'</a>]';
							}
							$input_html .= '</div>';
						} else {
							$input_html .= '<div>'.__('File type:').' '.$mimetype.'</div>';
							$input_html .= '<div>'.__('File size:').' '.size_format($file_size).'</div>';
							if ( wp_ext2type($ext) === 'video' || wp_ext2type($ext) === 'audio' ) {
								$input_html .= '<div>'.__('Length:').' '.$length.'</div>';
							}
						}
						$input_html .= '<div>'.__('Feed Title', 'medialibraryfeeder').': <select name="medialibraryfeeder_titles['.$attach_id.']">';
						$feedurl = NULL;
						foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
							if( is_array($value1) ) {
								$feedtitleselect = NULL;
								if($feedtitle === $key1){
									$feedtitleselect = ' selected';
									$feedurl = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_URL.'/'.md5($feedtitle).'.xml';
								}
								$input_html .= '<option value="'.$key1.'"'.$feedtitleselect.'>'.$key1.'</option>';
							}
						}
						$input_html .= '</select></div>';
						if (!empty($feedurl)) {
							$input_html .= '<div>'.__('Feed URL', 'medialibraryfeeder').': <a href="'.$feedurl.'" target="_blank" style="text-decoration: none; word-break: break-all;">'.$feedurl.'</a></div>';
						}
						$input_html .= "</div></div>\n";

						echo $input_html;
					} else {
					?>
					    <input type="hidden" name="medialibraryfeeder_applys[<?php echo $attachment->ID; ?>]" value="<?php echo $apply; ?>">
						<input type="hidden" name="medialibraryfeeder_titles[<?php echo $attachment->ID; ?>]" value="<?php echo $feedtitle; ?>" />
					<?php
					}
				}
				unset($medialibraryfeeder);

				?>
				<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
				<?php _e('Select'); ?> & <?php _e('Thumbnail'); ?> & <?php _e('Metadata'); ?> & <?php _e('Feed', 'medialibraryfeeder'); ?>
				</div>
				<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
				<input type="checkbox" id="group_medialibraryfeeder" class="medialibraryfeeder-admin-checkAll"><?php _e('Select all'); ?>
				</div>
				<?php
				if ( $pagelast > 1 ) {
					$this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $mimefilter);
				}
			}
			?>
			<div style="padding-top: 5px; padding-bottom: 5px;">
			<input type="submit" class="button-primary button-large" name="UpdateFeed" value="<?php _e('Update Feeds', 'medialibraryfeeder'); ?>" />
			</div>
			</form>
		</div>
		</div>
	<?php
	}

	/* ==================================================
	 * Sub Menu
	 */
	function other_notes() {

		?>
		<div class="wrap">
		<h2>MediaLibrary Feeder <?php _e('Other Notes', 'medialibraryfeeder'); ?>
			<form method="post" style="float: right;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-search-register'); ?>" />
				<input type="submit" class="button" value="<?php _e('Register the media to feeds', 'medialibraryfeeder'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-advanced-settings'); ?>" />
				<input type="submit" class="button" value="<?php echo __('Advanced').__('Settings'); ?>" />
			</form>
			<form method="post" style="float: right; margin-right: 1em;" action="<?php echo admin_url('admin.php?page=medialibraryfeeder-registration-feed'); ?>">
				<input type="submit" class="button" value="<?php _e('Feeds Management', 'medialibraryfeeder'); ?>" />
			</form>
		</h2>
		<div style="clear: both;"></div>

		<div id="medialibraryfeeder-admin-tabs">
		  <ul>
		    <li><a href="#medialibraryfeeder-admin-tabs-1"><?php _e('Caution:'); ?></a></li>
			<li><a href="#medialibraryfeeder-admin-tabs-2"><?php _e('Shortcode',  'medialibraryfeeder'); ?></a></li>

		<!--
			<li><a href="#medialibraryfeeder-admin-tabs-3">FAQ</a></li>
		 -->
		  </ul>

		  <div id="medialibraryfeeder-admin-tabs-1" style="background-color: #ffffff;">
			<div class="wrap">
				<h2><?php _e('Caution:') ?></h2>
				<li><h3><?php _e('Meta-box of MediaLibrary Feeder will be added to [Edit Media]. Please do apply it. Choose a feed title. Input ituned option.', 'medialibraryfeeder'); ?></h3></li>
				<img style="width: 100%;" src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/images/editmedia.png'; ?>">
				<hr>
				<li><h3><?php _e('Widget of MediaLibrary Feeder will be added to [Widgets]. Please enter the title, put a check in the feed you want to use.', 'medialibraryfeeder'); ?></h3></li>
				<img style="width: 100%;" src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/images/widget.png'; ?>">
				<hr>
				<li><h3><?php _e('Icon can be used include the following.', 'medialibraryfeeder'); ?></h3></li>
				<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds.png'; ?>" align="middle"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds.png'; ?>" style="width: 80%;" /></div>
				<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast.png'; ?>" align="middle"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast.png'; ?>" style="width: 80%;" /></div>
				<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-20x20.png'; ?>" align="middle"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-20x20.png'; ?>" style="width: 80%;" /></div>
				<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-20x20.png'; ?>" align="middle"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-20x20.png'; ?>" style="width: 80%;" /></div>
				<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-80x80.png'; ?>" align="middle"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/rssfeeds-80x80.png'; ?>" style="width: 80%;" /></div>
				<div><img src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-80x80.png'; ?>" align="middle"><input type="text" readonly="readonly" value="<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/icon/podcast-80x80.png'; ?>" style="width: 80%;" /></div>
			</div>
		  </div>

		  <div id="medialibraryfeeder-admin-tabs-2" style="background-color: #ffffff;">
			<div class="wrap">
				<h2><?php _e('Shortcode',  'medialibraryfeeder'); ?></h2>
				<li><h3><?php _e('MediaLibrary Feeder is available to display feed to [Post] or [Page] by shortcode. Use as follows.', 'medialibraryfeeder'); ?></h3></li>
				<div style="display:block; padding:5px 0">
				<code>[mlfeed feed=&#39;<font color="red">feed</font>&#39; link=&#39;<font color="red">file</font>&#39;]</code>
				</div>
				<div style="display:block; padding:0px 25px; font-weight:bold;">Optinos</div>
				<div style="display:block; padding:0px 45px;">feed&nbsp&nbsp&nbsp<?php _e('Feed Title', 'medialibraryfeeder'); ?></div>
				<div style="display:block; padding:0px 45px 10px;">link&nbsp&nbsp&nbsp<?php _e('If specify the &quot;file&quot;, it is linked to each file. The initial value is a link to the permalink of the media.', 'medialibraryfeeder'); ?></div>
				<img style="width: 100%;" src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/images/quicktag2.png'; ?>">
				<hr>
				<li><h3><?php _e('Quick Tag of MediaLibrary Feeder will be added to ([Add New Post][Edit Post][Add New Page][Edit Page]). Please use it. Shortcode will be added.', 'medialibraryfeeder'); ?></h3></li>
				<img style="width: 100%;" src = "<?php echo MEDIALIBRARYFEEDER_PLUGIN_URL.'/images/quicktag1.png'; ?>">
				<hr>
				<li><h3><?php _e('In the case of an image, the feed can be cooperation with the following plugins.', 'medialibraryfeeder'); ?></h3></li>
				<div style="display:block; padding:0px 25px; font-weight:bold;"><a href="http://wordpress.org/plugins/boxers-and-swipers/" target="_blank">Boxers and Swipers</a></div>
			</div>
		  </div>


		<!--
		  <div id="medialibraryfeeder-admin-tabs-3">
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
	function pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $mimefilter){

		$pageprev = $page - 1;
		$pagenext = $page + 1;
		$scriptnamefirst = add_query_arg( array('p' => '1', 'mime' => $mimefilter ),  $scriptname);
		$scriptnameprev = add_query_arg( array('p' => $pageprev, 'mime' => $mimefilter ),  $scriptname);
		$scriptnamenext = add_query_arg( array('p' => $pagenext, 'mime' => $mimefilter ),  $scriptname);
		$scriptnamelast = add_query_arg( array('p' => $pagelast, 'mime' => $mimefilter ),  $scriptname);
		?>
		<div class="medialibraryfeeder-pages">
		<span class="medialibraryfeeder-links">
		<?php
		if ( $page <> 1 ){
			?><a title='<?php _e('Go to the first page'); ?>' href='<?php echo $scriptnamefirst; ?>'>&laquo;</a>
			<a title='<?php _e('Go to the previous page'); ?>' href='<?php echo $scriptnameprev; ?>'>&lsaquo;</a>
		<?php
		}
		echo $page; ?> / <?php echo $pagelast;
		?>
		<?php
		if ( $page <> $pagelast ){
			?><a title='<?php _e('Go to the next page'); ?>' href='<?php echo $scriptnamenext; ?>'>&rsaquo;</a>
			<a title='<?php _e('Go to the last page'); ?>' href='<?php echo $scriptnamelast; ?>'>&raquo;</a>
		<?php
		}
		?>
		</span>
		</div>
		<?php

	}

	/* ==================================================
	 * Update wp_options table.
	 * @param	string	$submenu
	 * @since	1.0
	 */
	function options_updated($submenu){

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		$settings_tbl = array();

		switch ($submenu) {
			case 1:
				$settings_tbl['pagemax'] = $medialibraryfeeder_settings['pagemax'];
				foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
					if( is_array($value1) ) {
						foreach ( $value1 as $key2 => $value2 ) {
							$settings_tbl[$key1][$key2] = $value2;
						}
					}
				}
				if(isset($_POST['medialibraryfeeder_settings_titles_title_new'])){ $post_title_new = $_POST['medialibraryfeeder_settings_titles_title_new']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_description_new'])){ $post_description_new = $_POST['medialibraryfeeder_settings_titles_description_new']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_rssmax_new'])){ $post_rssmax_new = intval($_POST['medialibraryfeeder_settings_titles_rssmax_new']); }
				if(isset($_POST['medialibraryfeeder_settings_titles_iconurl_new'])){ $post_iconurl_new = $_POST['medialibraryfeeder_settings_titles_iconurl_new']; }
				if(isset($_POST['medialibraryfeeder_settings_delete_title'])){ $delete_titles = $_POST['medialibraryfeeder_settings_delete_title']; }
				if( !empty($_POST['FeedRegist']) ) {
					if ( !empty($post_title_new) && !empty($post_description_new) && !empty($post_rssmax_new) && !empty($post_iconurl_new) ){
						$settings_tbl[$post_title_new]['description'] = $post_description_new;
						$settings_tbl[$post_title_new]['rssmax'] = $post_rssmax_new;
						$settings_tbl[$post_title_new]['iconurl'] = $post_iconurl_new;

						$blog_description =  get_bloginfo ( 'description' );
						$blogusers = get_users();
						$copyright = $blogusers[0]->display_name;
						$itunes_author = $copyright;
						$itunes_name = $copyright;
						$itunes_email = $blogusers[0]->user_email;

						$settings_tbl[$post_title_new]['ttl'] = 60;
						$settings_tbl[$post_title_new]['copyright'] = $copyright;
						$settings_tbl[$post_title_new]['itunes_author'] = $itunes_author;
						$settings_tbl[$post_title_new]['itunes_block'] = 'no';
						$settings_tbl[$post_title_new]['itunes_category_1'] = '';
						$settings_tbl[$post_title_new]['itunes_category_2'] = '';
						$settings_tbl[$post_title_new]['itunes_category_3'] = '';
						$settings_tbl[$post_title_new]['itunes_image'] = '';
						$settings_tbl[$post_title_new]['itunes_explicit'] = 'no';
						$settings_tbl[$post_title_new]['itunes_complete'] = 'no';
						$settings_tbl[$post_title_new]['itunes_newfeedurl'] = '';
						$settings_tbl[$post_title_new]['itunes_name'] = $itunes_name;
						$settings_tbl[$post_title_new]['itunes_email'] = $itunes_email;
						$settings_tbl[$post_title_new]['itunes_subtitle'] = '';
						$settings_tbl[$post_title_new]['itunes_summary'] = $blog_description;
						update_option( 'medialibraryfeeder_settings', $settings_tbl );
						echo '<div class="updated"><ul><li>'.__('Feeds was registered.', 'medialibraryfeeder').'</li></ul></div>';
					} else {
						echo '<div class="error"><ul><li>'.__('Input is not enough.', 'medialibraryfeeder').'</li></ul></div>';
					}
				}
				if( !empty($_POST['FeedDelete']) ) {
					if ( !empty($delete_titles) ) {
						foreach ( $settings_tbl as $key1 => $value1 ) {
							if( is_array($value1) ) {
								foreach ( $delete_titles as $delete_title ) {
									if ( $delete_title === $key1 ) {
										unset($settings_tbl[$key1]);
										$xmlfile = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_DIR.'/'.md5($delete_title).'.xml';
										if ( file_exists($xmlfile)){
											unlink($xmlfile);
										}
									}
								}
							}
						}
						update_option( 'medialibraryfeeder_settings', $settings_tbl );
						echo '<div class="updated"><ul><li>'.__('Feeds was deleted.', 'medialibraryfeeder').' --> '.$delete_title.'</li></ul></div>';
					}
				}
				break;
			case 2:
				if(isset($_POST['medialibraryfeeder_settings_titles_title'])){ $post_title = $_POST['medialibraryfeeder_settings_titles_title']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_description'])){ $post_description = $_POST['medialibraryfeeder_settings_titles_description']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_rssmax'])){ $post_rssmax = intval($_POST['medialibraryfeeder_settings_titles_rssmax']); }
				if(isset($_POST['medialibraryfeeder_settings_titles_iconurl'])){ $post_iconurl = $_POST['medialibraryfeeder_settings_titles_iconurl']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_ttl'])){ $post_ttl = intval($_POST['medialibraryfeeder_settings_titles_ttl']); }
				if(isset($_POST['medialibraryfeeder_settings_titles_copyright'])){ $post_copyright = $_POST['medialibraryfeeder_settings_titles_copyright']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_author'])){ $post_itunes_author = $_POST['medialibraryfeeder_settings_titles_itunes_author']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_block'])){ $post_itunes_block = $_POST['medialibraryfeeder_settings_titles_itunes_block']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_category_1'])){ $post_itunes_category_1 = $_POST['medialibraryfeeder_settings_titles_itunes_category_1']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_category_2'])){ $post_itunes_category_2 = $_POST['medialibraryfeeder_settings_titles_itunes_category_2']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_category_3'])){ $post_itunes_category_3 = $_POST['medialibraryfeeder_settings_titles_itunes_category_3']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_image'])){ $post_itunes_image = $_POST['medialibraryfeeder_settings_titles_itunes_image']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_explicit'])){ $post_itunes_explicit = $_POST['medialibraryfeeder_settings_titles_itunes_explicit']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_complete'])){ $post_itunes_complete = $_POST['medialibraryfeeder_settings_titles_itunes_complete']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_newfeedurl'])){ $post_itunes_newfeedurl = $_POST['medialibraryfeeder_settings_titles_itunes_newfeedurl']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_name'])){ $post_itunes_name = $_POST['medialibraryfeeder_settings_titles_itunes_name']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_email'])){ $post_itunes_email = $_POST['medialibraryfeeder_settings_titles_itunes_email']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_subtitle'])){ $post_itunes_subtitle = $_POST['medialibraryfeeder_settings_titles_itunes_subtitle']; }
				if(isset($_POST['medialibraryfeeder_settings_titles_itunes_summary'])){ $post_itunes_summary = $_POST['medialibraryfeeder_settings_titles_itunes_summary']; }
				$settings_tbl['pagemax'] = $medialibraryfeeder_settings['pagemax'];
				foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
					if( is_array($value1) ) {
						foreach ( $value1 as $key2 => $value2 ) {
							$settings_tbl[$key1][$key2] = $value2;
						}
						if ( !empty($post_title) ){
							$settings_tbl[$post_title]['description'] = $post_description;
							$settings_tbl[$post_title]['rssmax'] = $post_rssmax;
							$settings_tbl[$post_title]['iconurl'] = $post_iconurl;
							$settings_tbl[$post_title]['ttl'] = $post_ttl;
							$settings_tbl[$post_title]['copyright'] = $post_copyright;
							$settings_tbl[$post_title]['itunes_author'] = $post_itunes_author;
							$settings_tbl[$post_title]['itunes_block'] = $post_itunes_block;
							$settings_tbl[$post_title]['itunes_category_1'] = $post_itunes_category_1;
							$settings_tbl[$post_title]['itunes_category_2'] = $post_itunes_category_2;
							$settings_tbl[$post_title]['itunes_category_3'] = $post_itunes_category_3;
							$settings_tbl[$post_title]['itunes_image'] = $post_itunes_image;
							$settings_tbl[$post_title]['itunes_explicit'] = $post_itunes_explicit;
							$settings_tbl[$post_title]['itunes_complete'] = $post_itunes_complete;
							$settings_tbl[$post_title]['itunes_newfeedurl'] = $post_itunes_newfeedurl;
							$settings_tbl[$post_title]['itunes_name'] = $post_itunes_name;
							$settings_tbl[$post_title]['itunes_email'] = $post_itunes_email;
							$settings_tbl[$post_title]['itunes_subtitle'] = $post_itunes_subtitle;
							$settings_tbl[$post_title]['itunes_summary'] = $post_itunes_summary;
						}
					}
				}
				echo '<div class="updated"><ul><li>'.__('Settings').' --> '.__('Changes saved.').'</li></ul></div>';
				update_option( 'medialibraryfeeder_settings', $settings_tbl );
				break;
			case 3:
				if ( $medialibraryfeeder_settings['pagemax'] <> intval($_POST['medialibraryfeeder_settings_pagemax']) ) {
					$settings_tbl['pagemax'] = intval($_POST['medialibraryfeeder_settings_pagemax']);
					foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
						if( is_array($value1) ) {
							foreach ( $value1 as $key2 => $value2 ) {
								$settings_tbl[$key1][$key2] = $value2;
							}
						}
					}
					update_option( 'medialibraryfeeder_settings', $settings_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
		}

	}

	/* ==================================================
	 * Update wp_postmeta table for admin settings.
	 * @param	string	$submenu
	 * @since	1.0
	 */
	function post_meta_updated($submenu) {

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

		switch ($submenu) {
			case 1:
				if(isset($_POST['medialibraryfeeder_settings_delete_title'])){ $delete_titles = $_POST['medialibraryfeeder_settings_delete_title']; }
				global $wpdb;
				$attachments = $wpdb->get_results("
								SELECT	ID
								FROM	$wpdb->posts
								WHERE	post_type = 'attachment'
										ORDER BY post_date DESC
								");

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
				break;
			case 3:
				if(isset($_POST['medialibraryfeeder_applys'])){ $medialibraryfeeder_applys = $_POST['medialibraryfeeder_applys']; }
				if(isset($_POST['medialibraryfeeder_titles'])){ $medialibraryfeeder_titles = $_POST['medialibraryfeeder_titles']; }
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
				break;
		}

	}

	/* ==================================================
	 * Custom box.
	 * @since	1.0
	 */
	function add_attachment_medialibraryfeeder_field( $form_fields, $post ) {

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
		$feedtitle = get_post_meta($post->ID, "medialibraryfeeder_title", true);

		// Top
	    $form_fields["medialibraryfeeder_top"]["label"] = "";
	    $form_fields["medialibraryfeeder_top"]["input"] = "html";
		$form_fields["medialibraryfeeder_top"]["html"] = '<hr><h3><span style="font-weight:bold">MediaLibrary Feeder</span></h3>';

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

		$exts = explode( '.', wp_get_attachment_url($post->ID) );
		$ext = end($exts);
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
		    $form_fields["medialibraryfeeder_itunes_image"]["html"]  = "<input type='text' style='width: 100%;' id='attachments-{$post->ID}-medialibraryfeeder_itunes_image' name='attachments[{$post->ID}][medialibraryfeeder_itunes_image]' value='$itunes_image' />\n";

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
		    $form_fields["medialibraryfeeder_itunes_subtitle"]["html"]  = "<input type='text' style='width: 100%;' id='attachments-{$post->ID}-medialibraryfeeder_itunes_subtitle' name='attachments[{$post->ID}][medialibraryfeeder_itunes_subtitle]' value='$itunes_subtitle' size='80' />\n";

			// textarea
			$itunes_summary = get_post_meta( $post->ID, 'medialibraryfeeder_itunes_summary', true );
			$form_fields["medialibraryfeeder_itunes_summary"]["label"] = '<div align="left"><a href="http://www.apple.com/itunes/podcasts/specs.html#summary" target="_blank"><code>&lt;itunes:summary&gt;</code></a></div>';
			$form_fields["medialibraryfeeder_itunes_summary"]["input"] = "html";
			$form_fields["medialibraryfeeder_itunes_summary"]["html"] = "<textarea id='attachments-{$post->ID}-medialibraryfeeder_itunes_summary' name='attachments[{$post->ID}][medialibraryfeeder_itunes_summary]' style='width: 100%;'>$itunes_summary</textarea>\n";
		}

		// End
	    $form_fields["medialibraryfeeder_end"]["label"] = "";
	    $form_fields["medialibraryfeeder_end"]["input"] = "html";
		$form_fields["medialibraryfeeder_end"]["html"] = "<hr>";

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
			if ( !empty($medialibraryfeeder_apply) ){
				if ($medialibraryfeeder_apply[0] === 'true'){
					echo '<div>'.__('Apply').'</div>';
					echo __('Feed Title', 'medialibraryfeeder').':&nbsp&nbsp&nbsp'.$medialibraryfeeder_title[0];
				} else {
					_e('None');
				}
			} else {
				_e('None');
			}
	    }
	}

	/* ==================================================
	 * Add js css
	 * @since	2.9
	 */
	function add_jscss(){

// JS
$medialibraryfeeder_add_jscss = <<<MEDIALIBRARYFEEDER

<!-- BEGIN: MediaLibrary Feeder -->
<script type="text/javascript">
jQuery('#medialibraryfeeder-admin-tabs').responsiveTabs({
  startCollapsed: 'accordion'
});
</script>
<script type="text/javascript">
	jQuery(function(){
		jQuery('.medialibraryfeeder-admin-checkAll').on('change', function() {
			jQuery('.' + this.id).prop('checked', this.checked);
		});
	});
</script>
<script type="text/javascript">
window.addEventListener( "load", function(){
  jQuery("#medialibraryfeeder-loading").delay(2000).fadeOut();
  jQuery("#medialibraryfeeder-loading-container").delay(2000).fadeIn();
}, false );
</script>
<!-- END: MediaLibrary Feeder -->

MEDIALIBRARYFEEDER;

		return $medialibraryfeeder_add_jscss;

	}

}

?>