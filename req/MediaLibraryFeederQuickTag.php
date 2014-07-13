<?php
/**
 * MediaLibrary Feeder
 * 
 * @package    MediaLibrary Feeder
 * @subpackage MediaLibrary Feeder Add quicktag
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
 * 
 * @since	2.2
 */
class MediaLibraryFeederQuickTag {

	function add_quicktag_select(){

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		$shortcode_titles = NULL;
		foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
			if( is_array($value1) ) {
				$shortcode_titles .= '<option value="[mlfeed feed=&#39;'.$key1.'&#39;]">'.$key1.'</option>';
			}
		}

$quicktag_add_select = <<<QUICKTAGADDSELECT
<select id="medialibraryfeeder_select">
	<option value="">MediaLibrary Feeder</option>
	{$shortcode_titles}
</select>
QUICKTAGADDSELECT;
		echo $quicktag_add_select;

	}

	function add_quicktag_button_js() {

$quicktag_add_js = <<<QUICKTAGADDJS

<!-- BEGIN: MediaLibrary Feeder -->
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#medialibraryfeeder_select").change(function() {
			send_to_editor(jQuery("#medialibraryfeeder_select :selected").val());
			return false;
		});
	});
</script>
<!-- END: MediaLibrary Feeder -->

QUICKTAGADDJS;
		echo $quicktag_add_js;

	}

}

?>