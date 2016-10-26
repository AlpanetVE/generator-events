0<?php

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
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`name` varchar(255) NOT NULL,
			`addres` varchar(255) DEFAULT NULL,
			`latitude` varchar(255) DEFAULT NULL,
			`longitude` varchar(255) DEFAULT NULL,
			`rating` int(1) DEFAULT NULL,
			`environment` varchar(255) DEFAULT NULL,
			`opening_hour` time DEFAULT NULL,
			`closed_hour` time DEFAULT NULL)";

		$sql[] = "CREATE TABLE IF NOT EXISTS `{$table_site_event}` (
			`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_site_fun` int(11) UNSIGNED NOT NULL,
			`name` varchar(255) NOT NULL,			
			`poster` varchar(100) DEFAULT NULL,
			`date` date NOT NULL,
			`clothing_type` varchar(100) DEFAULT NULL,
			`ticket_selling` longtext,
			`description` varchar(255) DEFAULT NULL,
			`opening_hour` time DEFAULT NULL,
			`closed_hour` time DEFAULT NULL)";

         
        foreach($sql as $sk => $sv){
			$wpdb->query($sv);
		}

		mkdir(ALPAGE_PATH_UPLOADS, 0700);

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
		}https://developer.wordpress.org/reference/functions/wp_enqueue_script/
		return $instance;
	}
	public function get_page_itemsSites($curr_page, $per_page){
		$start = (($curr_page-1)*$per_page);
		$query = "SELECT * FROM $this->table_sites ORDER BY id DESC LIMIT $start, $per_page";
		return $this->db->get_results( $query, ARRAY_A );
	}
	
	public function get_page_itemsEvent($curr_page, $per_page){
		$start = (($curr_page-1)*$per_page);
		$query = "SELECT
					se.id,
					se.id_site_fun,
					se.name,
					se.opening_hour,
					se.closed_hour,
					se.poster,
					se.`date`,
					se.clothing_type,
					se.ticket_selling,
					se.description,
					sf.name as name_site
					FROM
					$this->table_events AS se
					Inner Join $this->table_sites AS sf ON sf.id = se.id_site_fun ORDER BY id DESC LIMIT $start, $per_page";
		return $this->db->get_results( $query, ARRAY_A );
	}
	public function getCountSites(){
		$count = $this->db->get_var("SELECT COUNT(*) FROM $this->table_sites");
		return isset($count)?$count:0;
	}
	public function getCountEvents(){
		$count = $this->db->get_var("SELECT COUNT(*) FROM $this->table_events");
		return isset($count)?$count:0;
	}
	public function deleteSite($id){
		global $wpdb;		

		if(is_array($id))
			$id = sprintf('(%s)', implode(',', $id));
		else {
			$id = sprintf('(%d)', $id);
		}
        
		$query = "DELETE FROM $this->table_sites WHERE id IN $id";
		return $wpdb->query($query);
	}

	public function getSite($id){

		$query = "SELECT * FROM $this->table_sites where 1";
		if (!empty($id)) {
			$query.=" and id='$id'";
		}
		return $this->db->get_results( $query, ARRAY_A );
	}	

	public function addSite(){
		global $wpdb;
		$data=$_POST['GeForm'];
		if(is_array($data)){
			$results = $wpdb->insert($this->table_sites, array(
				'name'    		=>  isset($data['name']) ? $data['name'] : '',
				'addres'	  	=>	isset($data['addres']) ? $data['addres'] : '',
				'latitude'  	=>	isset($data['latitude']) ? $data['latitude'] : '',
				'longitude' 	=>	isset($data['longitude']) ? $data['longitude'] : '',
				'environment'	=>	isset($data['environment']) ? $data['environment'] : '',
				'closed_hour'	=>	isset($data['closed_hour']) ? $data['closed_hour'] : '',
				'opening_hour' 	=>	isset($data['opening_hour']) ? $data['opening_hour'] : ''
			));
			return $results;
		}
		return false;		
	}
	public function editSite($id=null){
		global $wpdb;
		$wpdb->flush();
		$id = !is_null($id) ? $id : $_POST['id_site'];

		if (!empty($id)) {
			$data=$_POST['GeForm'];
			
			$name 			= isset($data['name']) ? $data['name'] : '';
			$addres 		= isset($data['addres']) ? $data['addres'] : '';
			$latitude 		= isset($data['latitude']) ? $data['latitude'] : '';
			$longitude 		= isset($data['longitude']) ? $data['longitude'] : '';
			$environment 	= isset($data['environment']) ? $data['environment'] : '';
			$closed_hour 	= isset($data['closed_hour']) ? $data['closed_hour'] : '';
			$opening_hour 	= isset($data['opening_hour']) ? $data['opening_hour'] : '';

			$sql   = "UPDATE `$this->table_sites` SET  
			name = '$name',
			addres = '$addres',
			latitude = '$latitude',
			longitude = '$longitude',
			environment = '$environment',
			closed_hour = '$closed_hour',
			opening_hour = '$opening_hour' WHERE ID = {$id}";
			return $wpdb->query($sql);
		}
		return false;
	}

	public static function run() {
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
	public static function load_controller() {
		// Controller Base Class.
		 self::load_file( 'class-controller.php', 'classes' );
		 new GeneratorEvents_Controller();
	}



}