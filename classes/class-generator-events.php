<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

require 'cloudinary/Cloudinary.php';
require 'cloudinary/Uploader.php';
require 'cloudinary/Api.php';

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
	//	$this->uploadDirectory=ALPAGE_PATH_UPLOADS;
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
			`closed_hour` time DEFAULT NULL,
			`name_link` varchar(255) DEFAULT NULL)";

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
			`video_link` varchar(100) DEFAULT NULL,
			`date_time` datetime DEFAULT NULL,
			`status` int(11) NOT NULL)";

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

	public function process_data($values,$files){
		switch ($values['ge_tipo']) {
			case 'ge_comment':
				$this->sendComment($values,$files);
				break;

			default:
				# code...
				break;
		}
	}



public function sendCloudinary($file){
	\Cloudinary::config(array(
	        "cloud_name" => "darwin123",
	        "api_key" => "893138379283575",
	        "api_secret" => "ZuvV_dn10UdSkKyC52KRKO9a3FQ"
	      ));
		//		var_dump($file);
				//$ruta='../../'.$file;
//$ruta=getcwd().'../../'.$file;

$ruta=ALPAGE_ABSPATH.$file;
				//$ruta=ALPAGE_URL.$file;
				 $respuesta=\Cloudinary\Uploader::upload($ruta);

			return $respuesta;
}

public function sendComment($values,$files){
$imgLink='';
$comment='';
if(!empty($values['comment'])){
$comment=(string)$values['comment'];
}


$user = wp_get_current_user();
$id_event=$values['event_id'];
$url=$values['url'];


// var_dump($_COOKIE['ruta_foto_temp']);
// die();

if(isset($_COOKIE['ruta_foto_temp'])){
$res=$this->sendCloudinary($_COOKIE['ruta_foto_temp']);
$imgLink=$res['url'];
unlink(ALPAGE_ABSPATH.$_COOKIE['ruta_foto_temp']);
setcookie("ruta_foto_temp", "", time() - 3600,'/');
}


$resp=$this->db->query( $this->db->prepare(
	"
		INSERT INTO $this->table_user_event_comment
		(user_id, id_event, comment, img_link, date_time, status)
		VALUES ( %d, %d, %s, %s,now(), 1 )
	",
  array(
        $user->ID,
        $id_event,
        $comment,
				$imgLink
       )
    ));

	//if($resp)	{

		wp_redirect($url);
		exit;
		//$url = home_url( '/notification' );
	//}


	}

public function setRankingEvent($id_event){
	$sql= "SELECT count(*) as resul, sum(rating) as total
	FROM $this->table_user_event where id_event=$id_event; ";
	$data=$this->db->get_results( $sql, ARRAY_A );

	$promedio = $data[0]['total'] / $data[0]['resul'];
	$promRank = ceil($promedio);


$sqlUpdate="UPDATE $this->table_events SET rating = $promRank WHERE id = $id_event ";


$results=$this->db->query($sqlUpdate);


//var_dump($results);
$cons="SELECT id_site_fun FROM $this->table_events WHERE id=$id_event;";

$id_site=$this->db->get_var($cons);

return $this->setRankingSite($id_site);

}



public function setRankingSite($id_site){
$sql="SELECT count(*) as resul, sum(rating) as total FROM $this->table_events where id_site_fun=$id_site;";
$data=$this->db->get_results( $sql, ARRAY_A );

$promedio = $data[0]['total'] / $data[0]['resul'];
$promRank = ceil($promedio);

$results=$this->db->update($this->table_sites,array(
			'rating' =>$promRank),
				array(
					'id'=>$id_site
				));

	return $results;
}

	public function get_page_itemsSites($curr_page, $per_page){
		$start = (($curr_page-1)*$per_page);
		$query = "SELECT * FROM $this->table_sites ORDER BY id DESC LIMIT $start, $per_page";
		return $this->db->get_results( $query, ARRAY_A );
	}

	public function get_itemsSite($curr_page=null, $per_page=null,$order=null, $id=null, $name_link=null){
		$start = (($curr_page-1)*$per_page);

		$query = "SELECT
		sf.id,
		sf.`name`,
		sf.addres,
		sf.latitude,
		sf.longitude,
		sf.rating,
		sf.environment,
		sf.opening_hour,
		sf.closed_hour,
		sf.name_link
		FROM
		$this->table_sites AS sf
		WHERE 1";

		if (!empty($id)) {
			$query.=" and sf.id='$id'";
		}
		if (!empty($name_link)) {
			$query.=" and sf.name_link='$name_link'";
		}

		if (!empty($per_page)) {
			$query.=" LIMIT $start, $per_page";
		}

		return $this->db->get_results( $query, ARRAY_A );
	}

	public function get_itemsEvent($curr_page=null, $per_page=null, $idEvent=null, $name_link=null, $name_link_site=null, $date=null, $order=null){
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
					se.name_link,
					sf.name as name_site,
					sf.id as siteid,
					sf.addres,
					sf.latitude,
					sf.longitude,
					sf.environment,
					sf.opening_hour,
					sf.closed_hour,
					sf.name_link as site_link
					FROM
					$this->table_events AS se
					Inner Join $this->table_sites AS sf ON sf.id = se.id_site_fun where 1";

					if (!empty($idEvent)) {
						$query.=" and se.id='$idEvent'";
					}
					if (!empty($name_link)) {
						$query.=" and se.name_link='$name_link'";
					}
					if (!empty($name_link_site)) {
						$query.=" and sf.name_link='$name_link_site'";
					}

					if ($date=='news') {
						$query.=" and se.`date` > SUBDATE(CURDATE(),1)";
					}
					if ($date=='before') {
						$query.=" and se.`date` <= SUBDATE(CURDATE(),1)";
					}



					if (!empty($order)) {
						$query.="  ORDER BY '$order'";
					}
					else{
						$query.="  ORDER BY se.id DESC";
					}


					if (!empty($per_page)) {
						$query.=" LIMIT $start, $per_page";
					}

		return $this->db->get_results( $query, ARRAY_A );
	}


//Traer comentarios por evento
	public function getComentsEvent($id){
		$user_table=$this->db->prefix."users";

	$sqlSelect="SELECT C.comment AS comentario, C.img_link as img, U.display_name AS nic, U.ID as id, C.date_time as fecha
 FROM $this->table_user_event_comment C
 INNER JOIN $user_table	 U ON C.user_id=U.ID
 WHERE C.status=1 AND C.id_event=$id ORDER BY C.date_time DESC";

//var_dump($sqlSelect);
 $comentarios_subidos=$this->db->get_results($sqlSelect);

	return $comentarios_subidos;
	}
//


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
	public function getSite($id='', $name_link=''){

		$query = "SELECT * FROM $this->table_sites where 1";
		if (!empty($id)) {
			$query.=" and id='$id'";
		}
		if (!empty($name_link)) {
			$query.=" and name_link='$name_link'";
		}
		return $this->db->get_results( $query, ARRAY_A );
	}

	public function addSite(){
		global $wpdb;
		$data=$_POST['GeForm'];
		$name 		= isset($data['name']) ? $data['name'] : '';
		$name_link 	= $this->createNameLink($name,'site');
		if(is_array($data)){
			$results = $wpdb->insert($this->table_sites, array(
				'name'    		=>  $name,
				'addres'	  	=>	isset($data['addres']) ? $data['addres'] : '',
				'latitude'  	=>	isset($data['latitude']) ? $data['latitude'] : '',
				'longitude' 	=>	isset($data['longitude']) ? $data['longitude'] : '',
				'environment'	=>	isset($data['environment']) ? $data['environment'] : '',
				'closed_hour'	=>	isset($data['closed_hour']) ? $data['closed_hour'] : '',
				'opening_hour' 	=>	isset($data['opening_hour']) ? $data['opening_hour'] : '',
				'name_link' 	=>	$name_link
			));
			return $results;
		}
		return false;
	}
	function remove_accent($str)
	{
	  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	  return str_replace($a, $b, $str);
	}


	public function createNameLink($name,$table){


		$name_link = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),array('', '-', ''), $this->remove_accent($name)));

		if ($table=='site') {
			$row = $this->getSite(null,$name_link);
		}elseif ($table=='event') {
			$row = $this->get_itemsEvent(null, null, null,$name_link);
		}

		if(!empty($row[0]) && !empty($name_link))
			return $this->createNameLink($name_link.'(1)',$table);

		return $name_link;
	}
	public function addEvent(){
		global $wpdb;
		$data=$_POST['GeForm'];
		if(is_array($data)){
			$posterNameFile 	= $this->uploadFile();
			$name 				= isset($data['name']) ? $data['name'] : '';

			$name_link = $this->createNameLink($name,'event');


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

	 public function uploadFotos($file){
		 if (!isset($file['qqfile']['name']) || empty($file['qqfile']['name']) || ($file[uploadedfile][size] >20000000)) {
			return '';
		}
//qqfile
		$target_path 	= ALPAGE_PATH_UPLOADS;
		$nameFile 		= $this->sanear_string(basename( date("d-m-Y H:i:s").$file['qqfile']['name']));
		$target_path = $target_path . $nameFile;
		//mkdir("", 0777);

		if(move_uploaded_file($file['qqfile']['tmp_name'], $target_path)) {
			$GLOBALS['ruta_foto_temp']=$target_path;
				return array('success'=> true, "uuid" => $uuid);
			//$nameFile='';
		}
		// var_dump($nameFile);
		// //var_dump();
		// die();
		return array('error'=> 'Could not save uploaded file.' .
				'The upload was cancelled, or server error encountered');

	 }

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

	public function get_rating_user($id_event,$user_id){
		$query= "SELECT rating FROM $this->table_user_event where user_id = $user_id and id_event =$id_event ;";

		return $this->db->get_row( $query, ARRAY_A );
	}
	public function redirect_user($dir) {
	    $return_url = esc_url( home_url($dir) );
	    wp_redirect( $return_url );
	    exit;
	}
}
