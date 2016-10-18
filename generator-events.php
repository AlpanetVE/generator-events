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

require_once("define.php");

if (is_admin() == true) {

	register_activation_hook( __FILE__, array( 'GeneratorEvents', 'plugin_activation' ) );
	register_deactivation_hook( __FILE__, array( 'GeneratorEvents', 'plugin_deactivation' ) );

	// Start up Generator Events on WordPress's "init" action hook.	
	require_once ALPAGE_ABSPATH . 'classes/class-generator-events.php';

	// Start up TablePress on WordPress's "init" action hook.
	add_action( 'init', array( 'GeneratorEvents', 'run' ) );

	//add_action( 'admin_menu', array( 'GeneratorEvents_Controller', 'alpage_menu' ) );
}

/**
 * PAGE VIEWS
 * @static
 */
function alpage_get_menu( ) {
    $current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : 'GeneratorSites';
    include('classes/class-view.php');
    switch ($current_page) {
        case 'GeneratorSites':	
        	include('views/backend/view-generatorsites.php');
        	echo sprintf('<div class="wrap">');
        	echo sprintf( '<h2>%s <a class="add-new-h2" href="%s">%s</a></h2>', __('Site', 'wptg-plugin'), admin_url('admin.php?page='.$page_slug.'&action=add'), __('Add New', 'wptg-plugin') );
        	
        	$ObjList = new GeneratorSiteList();
        	$ObjList ->show();
        	echo sprintf('</div>');

            break;
        case 'addSite': 		include('views/backend/view-addsite.php');
            break;
        case 'editSite': 		include('views/backend/view-editsite.php');
            break;
        case 'GeneratorEvents': include('views/backend/view-generatorevents.php');
            break;
    }
}