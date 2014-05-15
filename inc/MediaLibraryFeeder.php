<?php
/**
 * MediaLibrary Feeder
 * 
 * @package    MediaLibrary Feeder
 * @subpackage MediaLibraryFeeder Main Functions
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

class MediaLibraryFeeder {

	public $feedlink;

	/* ==================================================
	 * Generate Feed Main
	 * @since	1.0
	 */
	function generate_feed(){

		$xmlitems = $this->scan_media();
		$this->rss_write($xmlitems);
		$this->feedlink = $this->feed_link($xmlitems);

	}

	/* ==================================================
	 * Media Search and Generate XML
	 * @return	array	$xmlitems
	 * @since	1.0
	 */
	function scan_media(){

		$args = array(
			'post_type' => 'attachment',
			'numberposts' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_status' => null,
			'post_parent' => $post->ID
			); 

		$attachments = get_posts($args);

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		$rsscount = array();
		$xmlitems = array();
		if ($attachments) {
			foreach ( $attachments as $attachment ) {
			    $feedtitle = get_post_meta( $attachment->ID, 'medialibraryfeeder_title', true);
				$rssmax = $medialibraryfeeder_settings[$feedtitle][rssmax];
				if (get_post_meta( $attachment->ID, 'medialibraryfeeder_apply', true ) && $rssmax > $rsscount[$feedtitle]) {
					$title = $attachment->post_title;
					$stamptime = mysql2date( DATE_RSS, $attachment->post_date );
					$ext = end(explode('.', $attachment->guid));
					$ext2type = wp_ext2type($ext);
					$thumblink = NULL;
					$link_url = NULL;
					$filesize = NULL;
					$thumblink = wp_get_attachment_image( $attachment->ID, 'thumbnail', TRUE );
					if ( $ext2type === 'image' ) {
						$attachment_image_src = wp_get_attachment_image_src($attachment->ID, 'full');
						$link_url = $attachment_image_src[0];
					} else {
						$link_url = $attachment->guid;
						if ( $ext2type === 'audio' || $ext2type === 'video' ) {
							$attachment_metadata = get_post_meta($attachment->ID, '_wp_attachment_metadata', true);
							$filesize = $attachment_metadata['filesize'];
						}
					}
					$img_url = '<a href="'.$link_url.'">'.$thumblink.'</a>';
					$xmlitems[$feedtitle] .= "<item>\n";
					$xmlitems[$feedtitle] .= "<title>".$title."</title>\n";
					$xmlitems[$feedtitle] .= "<link>".$link_url."</link>\n";
					if ( $ext2type === 'audio' || $ext2type === 'video' ){
						$xmlitems[$feedtitle] .= '<enclosure url="'.$link_url.'" length="'.$filesize.'" type="'.$this->mime_type($ext).'" />'."\n";
					}
					if( !empty($thumblink) ) {
						$xmlitems[$feedtitle] .= "<description><![CDATA[".$img_url."]]>".$attachment->post_content."</description>\n";
					} else {
						$xmlitems[$feedtitle] .= "<description>".$attachment->post_content."</description>\n";
					}
					$xmlitems[$feedtitle] .= "<pubDate>".$stamptime."</pubDate>\n";
					$xmlitems[$feedtitle] .= "</item>\n";
					++$rsscount[$feedtitle];
				}
			}
		}
		return $xmlitems;

	}


	/* ==================================================
	 * Write Feed
	 * @param	array	$xmlitems
	 * @since	1.0
	 */
	function rss_write( $xmlitems ) {

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		$wp_uploads = wp_upload_dir();
		$wp_upload_path = $wp_uploads['basedir'];
		$wp_upload_url = $wp_uploads['baseurl'];

		foreach ( $xmlitems as $feedtitle => $xmlitem ) {
			$xmlfile = $wp_upload_path.'/'.md5($feedtitle).'.xml';
			$xml_begin = NULL;
			$xml_end = NULL;

			$homeurl = home_url();
			$feedlanguage = WPLANG;

//RSS Feed
$xml_begin = <<<XMLBEGIN
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
<title>{$feedtitle}</title>
<link>{$homeurl}</link>
<description>{$medialibraryfeeder_settings[$feedtitle][description]}</description>
<language>$feedlanguage</language>
<generator>MediaLibrary Feeder</generator>

XMLBEGIN;

$xml_end = <<<XMLEND
</channel>
</rss>
XMLEND;
			$xml = $xml_begin.$xmlitem.$xml_end;
			if ( file_exists($xmlfile)){
				if ( !strpos(file_get_contents($xmlfile), $xmlitem) ) {
					$fno = fopen($xmlfile, 'w');
						fwrite($fno, $xml);
					fclose($fno);
				}
			}else{
				if (is_writable($wp_upload_path)) {
					$fno = fopen($xmlfile, 'w');
						fwrite($fno, $xml);
					fclose($fno);
					chmod($xmlfile, 0646);
				} else {
					_e('Could not create an RSS Feed. Please change to 777 or 757 to permissions of following directory.', 'medialibraryfeeder');
					echo '<div>'.$wp_upload_url.'</div>';
				}
			}
		}

	}

	/* ==================================================
	 * Generate FeedLink
	 * @param	array	$xmlitems
	 * @return	string	$feedlink
	 * @since	1.0
	 */
	function feed_link( $xmlitems ){

		$wp_uploads = wp_upload_dir();
		$wp_upload_path = $wp_uploads['basedir'];
		$wp_upload_url = $wp_uploads['baseurl'];

		$feedlink = '<!-- Start MediaLibrary Feeder -->'."\n";
		$feedwidget_tbl = array();
		foreach ( $xmlitems as $feedtitle => $xmlitem ) {
			$xmlfile = $wp_upload_path.'/'.md5($feedtitle).'.xml';
			$xmlurl = $wp_upload_url.'/'.md5($feedtitle).'.xml';
			if ( file_exists($xmlfile)){
				$feedlink .= '<link rel="alternate" type="application/rss+xml" href="'.$xmlurl.'" title="'.$feedtitle.'" />'."\n";
				$feedwidget_tbl[$feedtitle] = $xmlurl;
			}
		}
		$feedlink .= '<!-- End MediaLibrary Feeder -->'."\n";
		update_option( 'medialibraryfeeder_feedwidget', $feedwidget_tbl );

		return $feedlink;

	}

	/* ==================================================
	 * Add FeedLink
	 * @since	1.0
	 */
	function add_feedlink(){

		echo $this->feedlink;

	}

	/* ==================================================
	 * @param	string	$suffix
	 * @return	string	$mimetype
	 * @since	1.0
	 */
	function mime_type($suffix){

		$suffix = str_replace('.', '', $suffix);

		$mimes = wp_get_mime_types();

		foreach ($mimes as $ext => $mime) {
    		if ( preg_match("/".$ext."/i", $suffix) ) {
				$mimetype = $mime;
			}
		}

		return $mimetype;

	}

}

?>