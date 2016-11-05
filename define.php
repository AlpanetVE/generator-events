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

}