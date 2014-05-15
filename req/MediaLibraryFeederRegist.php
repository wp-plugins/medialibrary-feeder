<?php
/**
 * MediaLibrary Feeder
 * 
 * @package    MediaLibrary Feeder
 * @subpackage MediaLibraryFeederRegist registered in the database
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

class MediaLibraryFeederRegist {

	/* ==================================================
	 * Settings register
	 * @since	1.0
	 */
	function register_settings(){

		$blog_title =  get_bloginfo ( 'name' );
		$blog_description =  get_bloginfo ( 'description' );
		$iconurl = MEDIALIBRARYFEEDER_PLUGIN_URL.'/medialibrary-feeder/icon/rssfeeds.png';
		if ( !get_option('medialibraryfeeder_settings') ) {
			$settings_tbl = array(
							'pagemax' => 20,
							$blog_title => array(
										'description' => $blog_description,
										'rssmax' => 10,
										'iconurl' => $iconurl
									)
							);
			update_option( 'medialibraryfeeder_settings', $settings_tbl );
		}

	}

}

?>