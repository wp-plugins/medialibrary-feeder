<?php
/*
Plugin Name: MediaLibrary Feeder
Plugin URI: http://wordpress.org/plugins/medialibrary-feeder/
Version: 2.4
Description: Output as feed the media library. Generate a podcast for iTunes Store.
Author: Katsushi Kawamori
Author URI: http://gallerylink.nyanko.org/medialink/medialibrary-feeder/
Domain Path: /languages
*/

/*  Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

	load_plugin_textdomain('medialibraryfeeder', false, basename( dirname( __FILE__ ) ) . '/languages' );

	define("MEDIALIBRARYFEEDER_PLUGIN_BASE_FILE", plugin_basename(__FILE__));
	define("MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR", dirname(__FILE__));
	define("MEDIALIBRARYFEEDER_PLUGIN_URL", plugins_url($path='',$scheme=null).'/medialibrary-feeder');

	require_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/req/MediaLibraryFeederRegist.php' );
	$medialibraryfeederregist = new MediaLibraryFeederRegist();
	add_action('admin_init', array($medialibraryfeederregist, 'register_settings'));
	unset($medialibraryfeederregist);

	add_action('wp_head', wp_enqueue_script('jquery'));
	add_action('wp_head', wp_enqueue_style('medialibrary-feeder', MEDIALIBRARYFEEDER_PLUGIN_URL.'/css/medialibrary-feeder.css'));

	require_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/req/MediaLibraryFeederAdmin.php' );
	$medialibraryfeederadmin = new MediaLibraryFeederAdmin();
	add_action('admin_menu', array($medialibraryfeederadmin, 'plugin_menu'));
	add_filter('attachment_fields_to_edit', array($medialibraryfeederadmin, 'add_attachment_medialibraryfeeder_field'), 10, 2 );
	add_filter('attachment_fields_to_save', array($medialibraryfeederadmin, 'attachment_field_medialibraryfeeder_save'), 10, 2 );
	add_filter('plugin_action_links', array($medialibraryfeederadmin, 'settings_link'), 10, 2 );
	add_filter('manage_media_columns', array($medialibraryfeederadmin, 'media_columns_medialibraryfeeder'));
	add_action('manage_media_custom_column', array($medialibraryfeederadmin, 'media_custom_columns_medialibraryfeeder'), 10, 2);
	unset($medialibraryfeederadmin);

	include_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/inc/MediaLibraryFeeder.php' );
	$medialibraryfeeder = new MediaLibraryFeeder();
	$medialibraryfeeder->generate_feed();
	add_action( 'wp_head',  array($medialibraryfeeder, 'add_feedlink') );
	add_shortcode( 'mlfeed', array($medialibraryfeeder, 'feed_shortcode_func') );
	unset($medialibraryfeeder);

	require_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/req/MediaLibraryFeederWidgetItem.php' );
	add_action('widgets_init', create_function('', 'return register_widget("MediaLibraryFeederWidgetItem");'));

	require_once( MEDIALIBRARYFEEDER_PLUGIN_BASE_DIR.'/req/MediaLibraryFeederQuickTag.php' );
	$medialibraryfeederquicktag = new MediaLibraryFeederQuickTag();
	add_action('media_buttons', array($medialibraryfeederquicktag, 'add_quicktag_select'));
	add_action('admin_print_footer_scripts', array($medialibraryfeederquicktag, 'add_quicktag_button_js'));
	unset($medialibraryfeederquicktag);

?>