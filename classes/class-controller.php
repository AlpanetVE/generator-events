<?php
/**
 * GeneratorEvent Base Controller with members and methods for all controllers
 *
 * @package GeneratorEvent
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
 
// Prohibit direct script loading. seeeee
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
		
/**
 * Base Controller class
 * @package GeneratorEvent
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class GeneratorEvents_Controller {
	public $parent_page = 'middle';

	/**
	 * Actions that have a view and admin menu or nav tab menu entry.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $view_actions = array();

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'alpage_menu' ) );
	}

	/**
	 * Add admin screens to the correct place in the admin menu.
	 *
	 * @since 1.0.0
	 */
	/**
     *  ALPAGE MENU
     *  Loads the menu item into the WP tools section and queues the actions for only this plugin */
    public function alpage_menu() { 

        $wpfront_caps_translator = 'wpfront_user_role_editor_alpage_translate_capability';
		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQXJ0d29yayIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyMy4yNXB4IiBoZWlnaHQ9IjIyLjM3NXB4IiB2aWV3Qm94PSIwIDAgMjMuMjUgMjIuMzc1IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyMy4yNSAyMi4zNzUiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxwYXRoIGZpbGw9IiM5Q0ExQTYiIGQ9Ik0xOC4wMTEsMS4xODhjLTEuOTk1LDAtMy42MTUsMS42MTgtMy42MTUsMy42MTRjMCwwLjA4NSwwLjAwOCwwLjE2NywwLjAxNiwwLjI1TDcuNzMzLDguMTg0QzcuMDg0LDcuNTY1LDYuMjA4LDcuMTgyLDUuMjQsNy4xODJjLTEuOTk2LDAtMy42MTUsMS42MTktMy42MTUsMy42MTRjMCwxLjk5NiwxLjYxOSwzLjYxMywzLjYxNSwzLjYxM2MwLjYyOSwwLDEuMjIyLTAuMTYyLDEuNzM3LTAuNDQ1bDIuODksMi40MzhjLTAuMTI2LDAuMzY4LTAuMTk4LDAuNzYzLTAuMTk4LDEuMTczYzAsMS45OTUsMS42MTgsMy42MTMsMy42MTQsMy42MTNjMS45OTUsMCwzLjYxNS0xLjYxOCwzLjYxNS0zLjYxM2MwLTEuOTk3LTEuNjItMy42MTQtMy42MTUtMy42MTRjLTAuNjMsMC0xLjIyMiwwLjE2Mi0xLjczNywwLjQ0M2wtMi44OS0yLjQzNWMwLjEyNi0wLjM2OCwwLjE5OC0wLjc2MywwLjE5OC0xLjE3M2MwLTAuMDg0LTAuMDA4LTAuMTY2LTAuMDEzLTAuMjVsNi42NzYtMy4xMzNjMC42NDgsMC42MTksMS41MjUsMS4wMDIsMi40OTUsMS4wMDJjMS45OTQsMCwzLjYxMy0xLjYxNywzLjYxMy0zLjYxM0MyMS42MjUsMi44MDYsMjAuMDA2LDEuMTg4LDE4LjAxMSwxLjE4OHoiLz48L3N2Zz4=';
        
		
		//Main Menu

		switch ( $this->parent_page ) {
			case 'top':
				$position = 3; // position of Dashboard + 1
				break;
			case 'bottom':
				$position = ( ++$GLOBALS['_wp_last_utility_menu'] );
				break;
			case 'middle':
			default:
				$position = ( ++$GLOBALS['_wp_last_object_menu'] );
				break;
		}
		
        $perms = 'export';
        $perms = apply_filters($wpfront_caps_translator, $perms);
        $main_menu = add_menu_page('Generator Events Plugin', 'GeneratorEvents', $perms, 'GeneratorSites', 'alpage_get_menu', $icon_svg,$position);

        $perms = 'export';
        $perms = apply_filters($wpfront_caps_translator, $perms);
		$lang_txt = __('Generate Sites', 'GeneratorEvents');
        $page_settings = add_submenu_page('GeneratorSites', $lang_txt, $lang_txt, $perms, 'GeneratorSites', 'alpage_get_menu');

        $perms = 'manage_options';
        $perms = apply_filters($wpfront_caps_translator, $perms);
		$lang_txt = __('Generate Events', 'GeneratorEvents');
        $page_packages = add_submenu_page('GeneratorSites', $lang_txt, $lang_txt, $perms, 'GeneratorEvents', 'alpage_get_menu');

    }
    
}