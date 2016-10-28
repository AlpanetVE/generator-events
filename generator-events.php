<?php
/**
 * WordPress plugin "Generator Events" main file, responsible for initiating the plugin
 *
 * @package Generator Events
 * @author Alpanet
 * @version 1.0.0
 */

/*
Plugin Name: Generator Events
Plugin URI: https://alpanet.com.ve/
Description: Generator Events
Version: 1.0.0
Author: Alpanet
Author URI: https://alpanet.com.ve/
Author email: info@alpanet.com.ve
Text Domain: generator-events
License: GPL 2
Donate URI: https://alpanet.com.ve/
*/
/* ================================================================================ 
Copyright 2012-2016 Alpanet

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  ================================================================================ */
 
if (is_admin() == true) {
	require_once("define.php");
	require_once ALPAGE_ABSPATH . 'classes/class-generator-events.php';
	register_activation_hook( __FILE__, array( 'GeneratorEvents', 'plugin_activation' ) );
	register_deactivation_hook( __FILE__, array( 'GeneratorEvents', 'plugin_deactivation' ) );
	add_action( 'init', array( 'GeneratorEvents', 'run' ) );
	add_action( 'admin_init', 'plugin_admin_init' );
}


function plugin_admin_init() {
    wp_enqueue_script('datetimepicker',ALPAGE_URL.'views/backend/js/jquery.datetimepicker.full.min.js');
	wp_register_style( 'datetimepicker', ALPAGE_URL.'views/backend/css/datetimepicker.css' );
	wp_register_style( 'alpage_admin_style', ALPAGE_URL.'views/backend/css/style.css' );
}

/**
 * PAGE VIEWS
 * @static
 */
function alpage_get_menu( ) {
    $current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : 'GeneratorSites';

    if(isset($_REQUEST['action2']) && !empty($_REQUEST['action2']) && $_REQUEST['action2'] != -1 && $_REQUEST['action'] == -1)
        $_REQUEST['action'] = $_REQUEST['action2'];

    $action = isset($_REQUEST['action']) ? esc_html($_REQUEST['action']) : 'list';


    switch ($current_page) {
        case 'GeneratorSites':	
        	include('views/backend/view-generatorsites.php');
            $ObjList = new GeneratorSiteList();
            $ObjList ->doAction($action);
            break;
        case 'GeneratorEvents': 
            include('views/backend/view-generatorevents.php');
            $ObjList = new GeneratorEventList();
            $ObjList ->doAction($action);
            break;
    }
}