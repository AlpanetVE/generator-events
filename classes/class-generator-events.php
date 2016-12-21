<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class GeneratorEvents {
	/**
	 * GeneratorEvent version.
	 *
	 * Increases whenever a new plugin version is released.
	 *
	 * @since 1.0.0
	 * @const string
	 */
	const version = '1.0.0';

	/**
	 * GeneratorEvent internal plugin version ("options scheme" version).
	 *
	 * Increases whenever the scheme for the plugin options changes, or on a plugin update.
	 *
	 * @since 1.0.0
	 * @const int
	 */
	const db_version = 32;

	/**
	 * GeneratorEvent "table scheme" (data format structure) version.
	 *
	 * Increases whenever the scheme for a $table changes,
	 * used to be able to update plugin options and table scheme independently.
	 *
	 * @since 1.0.0
	 * @const int
	 */
	const table_scheme_version = 3;


	/**
	 * Instance of the controller.
	 *
	 * @since 1.0.0
	 * @var GeneratorEvent_*_Controller
	 */
	public static $controller;

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
		$this->table_user_event_comment = $wpdb->prefix."alpage_user_event_comment";
		$this->table_user_event = $wpdb->prefix."alpage_user_event";
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
        $table_site_fun 			= $objGeneratorEvents->table_sites;
		$table_site_event 			= $objGeneratorEvents->table_events;
		$table_user_event_comment 	= $objGeneratorEvents->table_user_event_comment;
		$table_user_event 			= $objGeneratorEvents->table_user_event;

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
			`closed_hour` time DEFAULT NULL,
			`name_link` varchar(255) DEFAULT NULL,
			`rating` int(1) DEFAULT NULL)";

			

		$sql[] = "CREATE TABLE IF NOT EXISTS `{$table_user_event_comment}` (
			`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`user_id` bigint(20) UNSIGNED NOT NULL,
			`id_event` int(11) UNSIGNED NOT NULL,
			`comment` varchar(255) NOT NULL,
			`img_link` varchar(100) DEFAULT NULL,
			`video_link` varchar(100) DEFAULT NULL)";

		$sql[] = "CREATE TABLE IF NOT EXISTS `{$table_user_event}` (
			`user_id` bigint(20) UNSIGNED NOT NULL,
			`id_event` int(11) UNSIGNED NOT NULL,
			`rating` int(1) UNSIGNED NOT NULL)";

        $sql[] = "ALTER TABLE `{$table_user_event}`
 				ADD PRIMARY KEY (`user_id`,`id_event`)";

        foreach($sql as $sk => $sv){
			$wpdb->query($sv);
		}

		mkdir(ALPAGE_PATH_UPLOADS, 0777);

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

	public function get_itemsEvent($curr_page, $per_page=null, $idEvent=null, $name_link=null){
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
					sf.name as name_site,
					sf.id as siteid,
					sf.addres,
					sf.latitude,
					sf.longitude,
					sf.environment,
					sf.opening_hour,
					sf.closed_hour
					FROM
					$this->table_events AS se
					Inner Join $this->table_sites AS sf ON sf.id = se.id_site_fun where 1";

					if (!empty($idEvent)) {
						$query.=" and se.id='$idEvent'";
					}
					if (!empty($name_link)) {
						$query.=" and se.name_link='$name_link'";
					}

					$query.="  ORDER BY se.id DESC";

					if (!empty($per_page)) {
						$query.=" LIMIT $start, $per_page";
					}

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
	public function deleteEvent($id){
		global $wpdb;

		if(is_array($id))
			$id = sprintf('(%s)', implode(',', $id));
		else {
			$id = sprintf('(%d)', $id);
		}

		$query = "DELETE FROM $this->table_events WHERE id IN $id";
		return $wpdb->query($query);
	}
	public function getSite($id=''){

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
	public function addEvent(){
		global $wpdb;
		$data=$_POST['GeForm'];
		if(is_array($data)){
			$posterNameFile 	= $this->uploadFile();
			$name 				= isset($data['name']) ? $data['name'] : '';

			$name_link = str_replace(' ', '-', $name_link);
			$name_link = preg_replace("/[^a-zA-Z0-9.]/", "", $name_link);

			$results = $wpdb->insert($this->table_events, array(
				'id_site_fun'   =>  isset($data['siteid']) ? $data['siteid'] : '',
				'name'	  		=>	isset($name) ? $name : '',
				'poster'  		=>	isset($posterNameFile) ? $posterNameFile : '',
				'date'  		=>	isset($data['date']) ? $data['date'] : '',
				'clothing_type' =>	isset($data['clothing_type']) ? $data['clothing_type'] : '',
				'ticket_selling'=>	isset($data['ticket_selling']) ? $data['ticket_selling'] : '',
				'description'	=>	isset($data['description']) ? $data['description'] : '',
				'opening_hour' 	=>	isset($data['opening_hour']) ? $data['opening_hour'] : '',
				'closed_hour'	=>	isset($data['closed_hour']) ? $data['closed_hour'] : '',
				'name_link'	=>	isset($name_link) ? $name_link : ''
			));
			return $results;
		}
		return false;
	}
	/**
	 * Upload file
	 * Return array with [name] file saved
	 * @public
	 */
	public function uploadFile(){

		if (!isset($_FILES['poster']['name']) || empty($_FILES['poster']['name']) || ($_FILES[uploadedfile][size] >20000000)) {
			return '';
		}

		$target_path 	= ALPAGE_PATH_UPLOADS;
		$nameFile 		= $this->sanear_string(basename( date("d-m-Y H:i:s").$_FILES['poster']['name']));
		$target_path = $target_path . $nameFile;

		if(!move_uploaded_file($_FILES['poster']['tmp_name'], $target_path)) {
			$nameFile='';
		}
		return $nameFile;
	}

	function sanear_string($string)
	{

	    $string = trim($string);

	    $string = str_replace(
	        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
	        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
	        $string
	    );

	    $string = str_replace(
	        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
	        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
	        $string
	    );

	    $string = str_replace(
	        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
	        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
	        $string
	    );

	    $string = str_replace(
	        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
	        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
	        $string
	    );

	    $string = str_replace(
	        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
	        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
	        $string
	    );

	    $string = str_replace(
	        array('ñ', 'Ñ', 'ç', 'Ç'),
	        array('n', 'N', 'c', 'C',),
	        $string
	    );

	    //Esta parte se encarga de eliminar cualquier caracter extraño
	    $string = str_replace(
	        array("\\", "¨", "º", "-", "~",
	             "", "@", "|", "!",
	             "·", "$", "%", "&", "/",
	             "(", ")", "?", "'", "¡",
	             "¿", "[", "^", "<code>", "]",
	             "+", "}", "{", "¨", "´",
	             ">", "< ", ";", ",", ":",
	             " "),
	        '',
	        $string
	    );

	    return $string;
	}

	public function editSite($id=null){
		global $wpdb;
		$wpdb->flush();
		$id = !is_null($id) ? $id : $_POST['id_site'];

		$posterNameFile 	= $this->uploadFile();
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

	public function editEvent($id=null){
		global $wpdb;
		$wpdb->flush();
		$id = !is_null($id) ? $id : $_POST['id_site'];

		if (!empty($id)) {
			$data=$_POST['GeForm'];
			$posterNameFile = $this->uploadFile();
			$id_site_fun 	= isset($data['siteid']) ? $data['siteid'] : '';
			$name 			= isset($data['name']) ? $data['name'] : '';
			$poster 		= isset($posterNameFile) ? $posterNameFile : '';
			$date 			= isset($data['date']) ? $data['date'] : '';
			$clothing_type 	= isset($data['clothing_type']) ? $data['clothing_type'] : '';
			$ticket_selling = isset($data['ticket_selling']) ? $data['ticket_selling'] : '';
			$description 	= isset($data['description']) ? $data['description'] : '';
			$opening_hour 	= isset($data['opening_hour']) ? $data['opening_hour'] : '';
			$closed_hour 	= isset($data['closed_hour']) ? $data['closed_hour'] : '';


			$sql   = "UPDATE `$this->table_events` SET
			id_site_fun 	= '$id_site_fun',
			name 			= '$name',
			poster 			= '$poster',
			`date` 			= '$date',
			clothing_type 	= '$clothing_type',
			ticket_selling 	= '$ticket_selling',
			description 	= '$description',
			closed_hour 	= '$closed_hour',
			opening_hour 	= '$opening_hour' ";

			if (!empty($poster)) {
				$sql.=" ,poster = '$poster'";
			}

			$sql.="  WHERE ID = {$id}";
			return $wpdb->query($sql);
		}
		return false;
	}

	public static function run() {
		self::$controller =self::load_controller();
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

	public function get_rating($id){
		$query= "SELECT rating FROM $this->table_events where id = $id;";
		return $this->db->get_row( $query, ARRAY_A );
	}

}
