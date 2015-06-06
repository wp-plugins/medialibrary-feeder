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

		global $wpdb;
		$attachments = $wpdb->get_results("
						SELECT	ID, post_title, guid, post_date, post_content
						FROM	$wpdb->posts
						WHERE	post_type = 'attachment'
								ORDER BY post_date DESC
						");

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		$rsscount = array();
		$xmlitems = array();
		if ($attachments) {
			foreach ( $attachments as $attachment ) {
			    $feedtitle = get_post_meta( $attachment->ID, 'medialibraryfeeder_title', true);
				if ( !empty($feedtitle) ) {
					$rssmax = $medialibraryfeeder_settings[$feedtitle]['rssmax'];
					if( !isset($rsscount[$feedtitle]) ){ $rsscount[$feedtitle] = 0; }
					if (get_post_meta( $attachment->ID, 'medialibraryfeeder_apply', true ) && $rssmax > $rsscount[$feedtitle]) {
						$title = $attachment->post_title;
						$stamptime = mysql2date( DATE_RSS, $attachment->post_date );
						$exts = explode('.', $attachment->guid);
						$ext = end($exts);
						$ext2type = wp_ext2type($ext);
						$thumblink = NULL;
						$link_url = NULL;
						$file_size = NULL;
						$thumblink = wp_get_attachment_image( $attachment->ID, 'thumbnail', TRUE );
						$blogusers = get_users($attachment->ID);
						$author_name = $blogusers[0]->display_name;
						$blog_name = get_bloginfo('name');
						$length = NULL;
						if ( $ext2type === 'image' ) {
							$attachment_image_src = wp_get_attachment_image_src($attachment->ID, 'full');
							$link_url = $attachment_image_src[0];
						} else {
							$link_url = $attachment->guid;
							if ( $ext2type === 'audio' || $ext2type === 'video' ) {
								$attachment_metadata = get_post_meta($attachment->ID, '_wp_attachment_metadata', true);
								$file_size = $attachment_metadata['filesize'];
								$length = $attachment_metadata['length_formatted'];
							}
						}
						$img_url = '<a href="'.$link_url.'">'.$thumblink.'</a>';
						if( isset($xmlitems[$feedtitle]) ){
							$xmlitems[$feedtitle] .= "<item>\n";
						} else {
							$xmlitems[$feedtitle] = "<item>\n";
						}
						$xmlitems[$feedtitle] .= "<title>".$title."</title>\n";
						$xmlitems[$feedtitle] .= "<link>".$link_url."</link>\n";

						if( !empty($thumblink) ) {
							$xmlitems[$feedtitle] .= "<description><![CDATA[".$img_url."]]>".html_entity_decode(strip_tags($attachment->post_content))."</description>\n";
						} else {
							$xmlitems[$feedtitle] .= "<description>". html_entity_decode(strip_tags($attachment->post_content))."</description>\n";
						}
						if ( $ext === 'm4a' || $ext === 'mp3' || $ext === 'mov' || $ext === 'mp4' || $ext === 'm4v' || $ext === 'pdf' || $ext === 'epub' ) {
							$itunes_author = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_author', true );
							$itunes_subtitle = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_subtitle', true );
							$itunes_summary = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_summary', true );
							$itunes_image = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_image', true );
							$itunes_block = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_block', true );
							$itunes_explicit = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_explicit', true );
							$itunes_isClosedCaptioned = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_isClosedCaptioned', true );
							$itunes_order = get_post_meta( $attachment->ID, 'medialibraryfeeder_itunes_order', true );
							if ( empty($itunes_author) ) { $itunes_author = $author_name; }
							$xmlitems[$feedtitle] .= "<itunes:author>".$itunes_author."</itunes:author>\n";
							if ( !empty($itunes_subtitle) ) { $xmlitems[$feedtitle] .= "<itunes:subtitle>".$itunes_subtitle."</itunes:subtitle>\n"; }
							if ( !empty($itunes_summary) ) { $xmlitems[$feedtitle] .= "<itunes:summary>".$itunes_summary."</itunes:summary>\n"; }
							if ( !empty($itunes_image) ) { $xmlitems[$feedtitle] .= '<itunes:image href="'.$itunes_image.'"'." />\n"; }
							if ( !empty($itunes_block) ) { $xmlitems[$feedtitle] .= "<itunes:block>".$itunes_block."</itunes:block>\n"; }
							if ( !empty($itunes_explicit) ) { $xmlitems[$feedtitle] .= "<itunes:explicit>".$itunes_explicit."</itunes:explicit>\n"; }
							if ( !empty($itunes_isClosedCaptioned) ) { $xmlitems[$feedtitle] .= "<itunes:isClosedCaptioned>".$itunes_isClosedCaptioned."</itunes:isClosedCaptioned>\n"; }
							if ( !empty($itunes_order) ) { $xmlitems[$feedtitle] .= "<itunes:order>".$itunes_order."</itunes:order>\n"; }
							if ( !empty($length) ) { $xmlitems[$feedtitle] .= "<itunes:duration>".$length."</itunes:duration>\n"; }
						}
						$xmlitems[$feedtitle] .= "<guid>".$link_url."</guid>\n";
						$xmlitems[$feedtitle] .= "<dc:creator>".$blog_name."</dc:creator>\n";
						$xmlitems[$feedtitle] .= "<pubDate>".$stamptime."</pubDate>\n";
						if ( $ext2type === 'audio' || $ext2type === 'video' ){
							$xmlitems[$feedtitle] .= '<enclosure url="'.$link_url.'" length="'.$file_size.'" type="'.$this->mime_type($ext).'" />'."\n";
						}
						$xmlitems[$feedtitle] .= "</item>\n";
						++$rsscount[$feedtitle];
					}
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

		foreach ( $xmlitems as $feedtitle => $xmlitem ) {
			$xmlfile = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_DIR.'/'.md5($feedtitle).'.xml';
			$xml_begin = NULL;
			$xml_end = NULL;

			$homeurl = home_url();
			$feedlanguage = get_option('WPLANG');
			$stamptime = mysql2date( DATE_RSS, time() );
			$itunescategory = stripslashes($medialibraryfeeder_settings[$feedtitle]['itunes_category_1']);
			$itunescategory .= stripslashes($medialibraryfeeder_settings[$feedtitle]['itunes_category_2']);
			$itunescategory .= stripslashes($medialibraryfeeder_settings[$feedtitle]['itunes_category_3']);
			$itunescategory = str_replace( '&', '&amp;', $itunescategory );
			if ( !empty($medialibraryfeeder_settings[$feedtitle]['itunes_newfeedurl']) ) {
				$itunesnewfeedurl = '<itunes:new-feed-url>'.$medialibraryfeeder_settings[$feedtitle]['itunes_newfeedurl'].'</itunes:new-feed-url>';
			} else {
				$itunesnewfeedurl = NULL;
			}

//RSS Feed
$xml_begin = <<<XMLBEGIN
<?xml version="1.0" encoding="UTF-8"?>
<rss
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:content="http://purl.org/rss/1.0/modules/content/"
 xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd"
 version="2.0">
<channel>
<ttl>{$medialibraryfeeder_settings[$feedtitle]['ttl']}</ttl>
<title>{$feedtitle}</title>
<link>{$homeurl}</link>
<description>{$medialibraryfeeder_settings[$feedtitle]['description']}</description>
<language>$feedlanguage</language>
<lastBuildDate>$stamptime</lastBuildDate>
<copyright>{$medialibraryfeeder_settings[$feedtitle]['copyright']}</copyright>
<itunes:author>{$medialibraryfeeder_settings[$feedtitle]['itunes_author']}</itunes:author>
<itunes:block>{$medialibraryfeeder_settings[$feedtitle]['itunes_block']}</itunes:block>
{$itunescategory}
<itunes:image href="{$medialibraryfeeder_settings[$feedtitle]['itunes_image']}" />
<itunes:explicit>{$medialibraryfeeder_settings[$feedtitle]['itunes_explicit']}</itunes:explicit>
<itunes:complete>{$medialibraryfeeder_settings[$feedtitle]['itunes_complete']}</itunes:complete>
{$itunesnewfeedurl}
<itunes:owner>
<itunes:name>{$medialibraryfeeder_settings[$feedtitle]['itunes_name']}</itunes:name>
<itunes:email>{$medialibraryfeeder_settings[$feedtitle]['itunes_email']}</itunes:email>
</itunes:owner>
<itunes:subtitle>{$medialibraryfeeder_settings[$feedtitle]['itunes_subtitle']}</itunes:subtitle>
<itunes:summary>{$medialibraryfeeder_settings[$feedtitle]['itunes_summary']}</itunes:summary>
<generator>MediaLibrary Feeder</generator>

XMLBEGIN;

$xml_end = <<<XMLEND
</channel>
</rss>
XMLEND;
			$xml = $xml_begin.$xmlitem.$xml_end;
			if ( file_exists($xmlfile)){
				if ( !strpos(file_get_contents($xmlfile), $xml) ) {
					$fno = fopen($xmlfile, 'w');
						fwrite($fno, $xml);
					fclose($fno);
				}
			}else{
				if (is_writable(MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_DIR)) {
					$fno = fopen($xmlfile, 'w');
						fwrite($fno, $xml);
					fclose($fno);
					chmod($xmlfile, 0646);
				} else {
					_e('Could not create an RSS Feed. Please change to 777 or 757 to permissions of following directory.', 'medialibraryfeeder');
					echo '<div>'.MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_URL.'</div>';
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

		$feedlink = '<!-- Start MediaLibrary Feeder -->'."\n";
		$feedwidget_tbl = array();
		foreach ( $xmlitems as $feedtitle => $xmlitem ) {
			$xmlfile = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_DIR.'/'.md5($feedtitle).'.xml';
			$xmlurl = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_URL.'/'.md5($feedtitle).'.xml';
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

	/* ==================================================
	 * @param	string	$atts
	 * @return	string	$html
	 * @since	2.2
	 */
	function feed_shortcode_func( $atts, $html = NULL ) {

		$html = apply_filters( 'post_medialibraryfeed', '', $atts );

		extract(shortcode_atts(array(
    	    'feed' => '',
			'link' => ''
		), $atts));

		$permalink = TRUE;
		if ( $link === 'file' ) { $permalink = FALSE; }

		global $wpdb;
		$attachments = $wpdb->get_results("
						SELECT	ID, post_title, guid, post_date
						FROM	$wpdb->posts
						WHERE	post_type = 'attachment'
								ORDER BY post_date DESC
						");

		$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');

		wp_enqueue_style('medialibrary-feeder', MEDIALIBRARYFEEDER_PLUGIN_URL.'/css/medialibrary-feeder.css');

		$html = '<h2>'.$feed.'</h2>';
		$html .= '<div id="playlists-medialibraryfeeder">';

		if ($attachments) {
			$pagecount = 0;
			$page_feed = 0;
			$page = 1;
			foreach ( $attachments as $attachment ) {
			    $feedtitle = get_post_meta( $attachment->ID, 'medialibraryfeeder_title', true);
				if ( !empty($feedtitle) ) {
					if ( !empty($_GET[$feedtitle.'-p']) && $feed === $feedtitle ) {
						$page = $_GET[$feedtitle.'-p'];
					}
					$pagemax = $medialibraryfeeder_settings[$feedtitle]['rssmax'];
					$page_begin = $pagemax * ( $page - 1 ) ;
					$page_end = $pagemax * $page ;
					if (get_post_meta( $attachment->ID, 'medialibraryfeeder_apply', true ) && $feed === $feedtitle ) {
						if ( $page_end > $pagecount && $page_begin <= $pagecount ) {
							$title = $attachment->post_title;
							$stamptime = mysql2date( DATE_RSS, $attachment->post_date );
							$exts = explode('.', $attachment->guid);
							$ext = end($exts);
							$ext2type = wp_ext2type($ext);
							$thumblink = NULL;
							$link_url = NULL;
							$file_size = NULL;
							$length = NULL;
							$stamptime = $attachment->post_date;
							$attachment_metadata = get_post_meta($attachment->ID, '_wp_attachment_metadata', true);
							if ( isset( $attachment_metadata['filesize'] ) ) {
								$file_size = $attachment_metadata['filesize'];
							} else {
								$file_size = filesize( get_attached_file($attachment->ID) );
							}
							$metadata = '<div>'.$stamptime.'&nbsp;&nbsp;'.size_format($file_size);
							if ( $ext2type === 'audio' || $ext2type === 'video' ) {
								$length = $attachment_metadata['length_formatted'];
								$metadata .= '&nbsp;&nbsp;'.$length;
							}
							$metadata .= '</div>';
							$thumblink = wp_get_attachment_image( $attachment->ID, 'thumbnail', TRUE );
							$html .= '<li>'.wp_get_attachment_link( $attachment->ID, 'thumbnail', $permalink, FALSE , $thumblink.$title.$metadata ).'</li>';
							$page_encode = urlencode($feedtitle.'-p');
						}
						++$pagecount;
						$page_feed = ceil( $pagecount / $pagemax );
						$feedicontitle = $feedtitle;
					}
				}
			}
		}
		$html .= '</div><br clear="all">';

		$html .= '<div id="pagenation-medialibraryfeeder">';
		$permalink_url = get_permalink( get_the_ID() );
		if ( !empty($page_encode) ) {
			$permalink_url_next = add_query_arg( $page_encode, $page+1, $permalink_url );
			$permalink_url_prev = add_query_arg( $page_encode, $page-1, $permalink_url );
			$permalink_url_begin = add_query_arg( $page_encode, 1, $permalink_url );
			$permalink_url_end = add_query_arg( $page_encode, $page_feed, $permalink_url );
		}
		if ( $page == 1 && $page_feed > 1 ) {
			$html .= '<li>'.__('Pages:').$page.'&#47;'.$page_feed.'&nbsp;&nbsp;<a href="'.$permalink_url_next.'">&nbsp;&nbsp;&nbsp;&#62;&nbsp;&nbsp;&nbsp;</a><a href="'.$permalink_url_end.'">&nbsp;&nbsp;&nbsp;&#62;&#62;&nbsp;&nbsp;&nbsp;</a></li>';
		} else if ( $page > 1 && $pagecount % $page_end > 0 && $pagecount > $page_end ) {
			$html .= '<li><a href="'.$permalink_url_begin.'">&nbsp;&nbsp;&nbsp;&#60;&#60;&nbsp;&nbsp;&nbsp;</a><a href="'.$permalink_url_prev.'">&nbsp;&nbsp;&nbsp;&#60;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;'.__('Pages:').$page.'&#47;'.$page_feed;
			$html .= '&nbsp;&nbsp;<a href="'.$permalink_url_next.'">&nbsp;&nbsp;&nbsp;&#62;&nbsp;&nbsp;&nbsp;</a><a href="'.$permalink_url_end.'">&nbsp;&nbsp;&nbsp;&#62;&#62;&nbsp;&nbsp;&nbsp;</a></li>';
		} else if ( $page > 1 && $pagecount > $page_begin && $pagecount <= $page_end ) {
			$html .= '<li><a href="'.$permalink_url_begin.'">&nbsp;&nbsp;&nbsp;&#60;&#60;&nbsp;&nbsp;&nbsp;</a><a href="'.$permalink_url_prev.'">&nbsp;&nbsp;&nbsp;&#60;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;'.__('Pages:').$page.'&#47;'.$page_feed.'</li>';
		}
		$html .= '</div>';

		if ( isset($feedicontitle) ) {
			foreach ( $medialibraryfeeder_settings as $key1 => $value1 ) {
				if( is_array($value1) ) {
					if ( $key1 ===  $feedicontitle ){
						$xmlurl = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_URL.'/'.md5($key1).'.xml';
					}
				}
			}
			$iconurl = $medialibraryfeeder_settings[$feedicontitle]['iconurl'];
			$html .= '<div align="right"><a href="'.$xmlurl.'"><img src="'.$iconurl.'"></a></div>';
		}

		return $html;

	}

	/* ==================================================
	 * @param	string	$ext
	 * @param	int		$attach_id
	 * @param	array	$metadata
	 * @return	array	$imagethumburls(array), $mimetype(string), $length(string), $thumbnail_img_url(string), $stamptime(string), $file_size(string)
	 * @since	3.0
	 */
	function getmeta($ext, $attach_id, $metadata){

		$imagethumburls = array();
		$mimetype = NULL;
		$length = NULL;

		if(empty($metadata)){
			// for wp_read_audio_metadata and wp_read_video_metadata
			include_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		if ( wp_ext2type($ext) === 'image' ){
			if(empty($metadata)){
				$metadata = wp_get_attachment_metadata( $attach_id );
			}
			$imagethumburl_base = MEDIALIBRARYFEEDER_PLUGIN_UPLOAD_URL.'/'.rtrim($metadata['file'], wp_basename($metadata['file']));
			foreach ( $metadata as $key1 => $key2 ){
				if ( $key1 === 'sizes' ) {
					foreach ( $metadata[$key1] as $key2 => $key3 ){
						$imagethumburls[$key2] = $imagethumburl_base.$metadata['sizes'][$key2]['file'];
					}
				}
			}
		}else if ( wp_ext2type($ext) === 'video' ){
			if(empty($metadata)){
				$metadata = wp_read_video_metadata( get_attached_file($attach_id) );
			}
			if(array_key_exists ('fileformat', $metadata)){
				$mimetype = $metadata['fileformat'].'('.$metadata['mime_type'].')';
			}
			if(array_key_exists ('length_formatted', $metadata)){
				$length = $metadata['length_formatted'];
			}
		}else if ( wp_ext2type($ext) === 'audio' ){
			if(empty($metadata)){
				$metadata = wp_read_audio_metadata( get_attached_file($attach_id) );
			}
			if(array_key_exists ('fileformat', $metadata)){
				$mimetype = $metadata['fileformat'].'('.$metadata['mime_type'].')';
			}
			if(array_key_exists ('length_formatted', $metadata)){
				$length = $metadata['length_formatted'];
			}
		} else {
			$metadata = NULL;
			$filetype = wp_check_filetype( get_attached_file($attach_id) );
			$mimetype =  $filetype['ext'].'('.$filetype['type'].')';
		}

		$image_attr_thumbnail = wp_get_attachment_image_src($attach_id, 'thumbnail', true);
		$thumbnail_img_url = $image_attr_thumbnail[0];

		$stamptime = get_the_time( 'Y-n-j ', $attach_id ).get_the_time( 'G:i', $attach_id );
		if ( isset( $metadata['filesize'] ) ) {
			$file_size = $metadata['filesize'];
		} else {
			$file_size = @filesize( get_attached_file($attach_id) );
		}

		return array($imagethumburls, $mimetype, $length, $thumbnail_img_url, $stamptime, $file_size);

	}

}

?>