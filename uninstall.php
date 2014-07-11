<?php

	//if uninstall not called from WordPress exit
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	    exit();

	// Delete feeds
	$medialibraryfeeder_settings = get_option('medialibraryfeeder_settings');
	$wp_uploads = wp_upload_dir();
	$wp_upload_path = $wp_uploads['basedir'];
	foreach ( $xmlitems as $feedtitle => $xmlitem ) {
		$xmlfile = $wp_upload_path.'/'.md5($feedtitle).'.xml';
		if ( file_exists($xmlfile)){
			unlink($xmlfile);
		}
	}

	$option_names = array(
						'medialibraryfeeder_settings',
						'medialibraryfeeder_feedwidget'
					);

	$args = array(
				'post_type' => 'attachment',
				'numberposts' => -1
			);
	$allposts = get_posts($args);

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

	// For Single site
	if ( !is_multisite() ) {
		foreach( $option_names as $option_name ) {
		    delete_option( $option_name );
		}
		foreach( $allposts as $postinfo ) {
			foreach ( $medialibraryfeeder_arr as $key ) {
				delete_post_meta( $postinfo->ID, $key );
			}
		}
	} else {
	// For Multisite
	    // For regular options.
	    global $wpdb;
	    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	    $original_blog_id = get_current_blog_id();
	    foreach ( $blog_ids as $blog_id ) {
	        switch_to_blog( $blog_id );
			foreach( $option_names as $option_name ) {
			    delete_option( $option_name );
			}
			foreach( $allposts as $postinfo ) {
				foreach ( $medialibraryfeeder_arr as $key ) {
					delete_post_meta( $postinfo->ID, $key );
				}
			}
	    }
	    switch_to_blog( $original_blog_id );

	    // For site options.
		foreach( $option_names as $option_name ) {
		    delete_site_option( $option_name );  
		}
		foreach( $allposts as $postinfo ) {
			delete_post_meta( $postinfo->ID, 'medialibraryfeeder_apply' );
			delete_post_meta( $postinfo->ID, 'medialibraryfeeder_title' );
		}
	}

?>
