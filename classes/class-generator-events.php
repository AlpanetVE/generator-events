<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class GeneratorEvents {
	/**
	 * TablePress version.
	 *
	 * Increases whenever a new plugin version is released.
	 *
	 * @since 1.0.0
	 * @const string
	 */
	const version = '1.0.0';

	/**
	 * TablePress internal plugin version ("options scheme" version).
	 *
	 * Increases whenever the scheme for the plugin options changes, or on a plugin update.
	 *
	 * @since 1.0.0
	 * @const int
	 */
	const db_version = 32;

	/**
	 * TablePress "table scheme" (data format structure) version.
	 *
	 * Increases whenever the scheme for a $table changes,
	 * used to be able to update plugin options and table scheme independently.
	 *
	 * @since 1.0.0
	 * @const int
	 */
	const table_scheme_version = 3;

	/**
	 * Instance of the Options Model.
	 *
	 * @since 1.3.0
	 * @var TablePress_Options_Model
	 */
	public static $model_options;

	/**
	 * Instance of the Table Model.
	 *
	 * @since 1.3.0
	 * @var TablePress_Table_Model
	 */
	public static $model_table;

	/**
	 * Instance of the controller.
	 *
	 * @since 1.0.0
	 * @var TablePress_*_Controller
	 */
	public static $controller;

	/**
	 * Name of the Shortcode to show a TablePress table.
	 *
	 * Should only be modified through the filter hook 'tablepress_table_shortcode'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $shortcode = 'generator-event';

	/**
	 * Actions that have a view and admin menu or nav tab menu entry.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $view_actions = array();	

	private $db;

	function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->table_sites = $wpdb->prefix."alpage_site_fun";
		$this->table_events = $wpdb->prefix."alpage_site_event";
		$this->db_version = "1.0";
	}

	/* ACTIVATION 
      Only called when plugin is activated */
    function plugin_activation() 
	{
        global $wpdb;

		$sql = array();
		$objGeneratorEvents= new GeneratorEvents();
        //Only update database on version update
        $table_site_fun 	= $objGeneratorEvents->table_sites;
		$table_site_event 	= $objGeneratorEvents->table_events;

		$sql[] = "CREATE TABLE IF NOT EXISTS `{$table_site_fun}` (
			`id_fun_site` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`name` varchar(255) NOT NULL,
			`addres` varchar(255) DEFAULT NULL,
			`coordinates` varchar(255) DEFAULT NULL,
			`rating` int(1) DEFAULT NULL,
			`environment` varchar(255) DEFAULT NULL,
			`opening_hour` time DEFAULT NULL,
			`closed_hour` time DEFAULT NULL)";

		$sql[] = "CREATE TABLE IF NOT EXISTS `{$table_site_event}` (
			`id_site_event` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_site_fun` int(11) UNSIGNED NOT NULL,
			`name` varchar(255) NOT NULL,
			`opening_hour` time DEFAULT NULL,
			`closed_hour` time DEFAULT NULL,
			`poster` varchar(100) DEFAULT NULL,
			`date` date NOT NULL,
			`clothing_type` varchar(100) DEFAULT NULL,
			`ticket_selling` longtext,
			`description` varchar(255) DEFAULT NULL)";

         
        foreach($sql as $sk => $sv){
			$wpdb->query($sv);
		}

    }

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		//No actions needed yet
	}

	public static function get_instance(){
		static $instance = null;
		if($instance == null){
			$instance = new GeneratorEvents();
		}
		return $instance;
	}
	public function get_page_items($curr_page, $per_page){
		$start = (($curr_page-1)*$per_page);
		$query = "SELECT * FROM $this->table_name ORDER BY id DESC LIMIT $start, $per_page";
		return $this->db->get_results( $query, ARRAY_A );
	}
	public function getCountSites(){
		$count = $this->db->get_var("SELECT COUNT(*) FROM $this->table_sites");
		return isset($count)?$count:0;
	}

	public static function run() {
		$controller = 'admin';

		self::$controller = self::load_controller( $controller );
	}
	/**
	 * Load a file with require_once(), after running it through a filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file   Name of the PHP file with the class.
	 * @param string $folder Name of the folder with $class's $file.
	 */
	public static function load_file( $file, $folder ) {
		$full_path = ALPAGE_ABSPATH . $folder . '/' . $file;
		/**
		 * Filter the full path of a file that shall be loaded.
		 *
		 * @since 1.0.0
		 *
		 * @param string $full_path Full path of the file that shall be loaded.
		 * @param string $file      File name of the file that shall be loaded.
		 * @param string $folder    Folder name of the file that shall be loaded.
		 */
		$full_path = apply_filters( 'alpage_load_file_full_path', $full_path, $file, $folder );
		if ( $full_path ) {
			require_once $full_path;
		}
	}
	/**
	 * Create a new instance of the $controller, which is stored in the "controllers" subfolder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $controller Name of the controller.
	 * @return object Instance of the initialized controller.
	 */
	public static function load_controller( $controller ) {
		// Controller Base Class.
		 self::load_file( 'class-controller.php', 'classes' );
		 new GeneratorEvents_Controller();
	}
	/**
	 * Create a new instance of the $view, which is stored in the "views" subfolder, and set it up with $data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $view Name of the view to load.
	 * @param array  $data Optional. Parameters/PHP variables that shall be available to the view.
	 * @return object Instance of the initialized view, already set up, just needs to be rendered.
	 */
	public static function load_view( $view, array $data = array() ) {
		// View Base Class.
		self::load_file( 'class-view.php', 'classes' );
		// Make first letter uppercase for a better looking naming pattern.
		$ucview = ucfirst( $view );
		$the_view = self::load_class( "TablePress_{$ucview}_View", "view-{$view}.php", 'views' );
		$the_view->setup( $view, $data );
		return $the_view;
	}


}