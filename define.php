<?php
//Prevent directly browsing to the file
if (function_exists('plugin_dir_url')) 
{
	// Prohibit direct script loading.
	defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
	define( 'ALPAGE_BASENAME','generatorEvents');
	// Define certain plugin variables as constants.
	define( 'ALPAGE_ABSPATH', plugin_dir_path( __FILE__ ) );
	
	define( 'ALPAGE_URL', get_site_url(). '/wp-content/plugins/generator-events/' );
	define( 'ALPAGE_URL_UPLOADS', get_site_url(). '/wp-content/uploads/'.ALPAGE_BASENAME.'/' );
	define( 'ALPAGE_PATH_UPLOADS',ABSPATH . 'wp-content/uploads/'.ALPAGE_BASENAME.'/' );
	
	define( 'ALPAGE_MINIMUM_WP_VERSION', '3.7' );


	define( 'ALPAGE_URL_EVENT', get_site_url(). '/event/' );
	define( 'ALPAGE_URL_EVENTS', get_site_url(). '/events/' );

	define( 'ALPAGE_URL_SITE', get_site_url(). '/site/' );
	define( 'ALPAGE_URL_SITES', get_site_url(). '/sites/' );


	//---------------------------------------------
	define( 'STAR_ABSPATH', ALPAGE_ABSPATH);
	define( 'STAR_URL', ALPAGE_URL);

}