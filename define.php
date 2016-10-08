<?php
//Prevent directly browsing to the file
if (function_exists('plugin_dir_url')) 
{
	// Prohibit direct script loading.
	defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

	// Define certain plugin variables as constants.
	define( 'ALPAGE_ABSPATH', plugin_dir_path( __FILE__ ) );
	define( 'ALPAGE_MINIMUM_WP_VERSION', '3.7' );

}