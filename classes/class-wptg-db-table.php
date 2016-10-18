<?php

if ( ! defined( 'WPINC' ) ) {
	 die;
}

class WPTG_DB_Tablew
{
	private $db;

	function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = "wptg_tables";
		$this->db_version = "1.0";
	}

	public static function get_instance(){
		static $instance = null;
		if($instance == null){
			$instance = new WPTG_DB_Tablew();
		}
		return $instance;
	}

	public function create_table(){
		$current_version = get_option('wptg_db_table_version');
		if($current_version && $current_version == $this->db_version && $this->db->get_var("SHOW TABLES LIKE '$this->table_name'") == $this->table_name){
			return;
		}

		$sql = "
			CREATE TABLE $this->table_name (
				id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
				name TINYTEXT NOT NULL,
				rows MEDIUMINT(9) NOT NULL,
				cols MEDIUMINT(9) NOT NULL,
				tvalues LONGTEXT NOT NULL,
				UNIQUE KEY id (id)
			);
		";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		update_option('wptg_db_table_version', $this->db_version);
	}

	public function add($name, $rows, $cols, $tvalues){
		$name 		= wp_strip_all_tags(stripslashes_deep($name));
		$rows 		= intval(stripslashes_deep($rows));
		$cols 		= intval(stripslashes_deep($cols));
		$tvalues 	= $this->serialize(stripslashes_deep($tvalues));

		$result = $this->db->insert( $this->table_name, array('name' => $name, 'rows' => $rows, 'cols' => $cols, 'tvalues' => $tvalues ) );
		if($result)
			return $this->db->insert_id;
		return false;
	}

	public function update($id, $name, $rows, $cols, $tvalues){
		$name 		= wp_strip_all_tags(stripslashes_deep($name));
		$rows 		= intval(stripslashes_deep($rows));
		$cols 		= intval(stripslashes_deep($cols));
		$tvalues 	= $this->serialize(stripslashes_deep($tvalues));
		  
		return $this->db->update( $this->table_name, array('name' => $name, 'rows' => $rows, 'cols' => $cols, 'tvalues' => $tvalues ), array( 'id' => $id ) );
	}

	public function drop_table() {
		$query = "DROP TABLE $this->table_name";
		return $this->db->query($query);
	}

	public function delete($id){
		if(is_array($id))
			$id = sprintf('(%s)', implode(',', $id));
		else {
			$id = sprintf('(%d)', $id);
		}

		$query = "DELETE FROM $this->table_name WHERE id IN $id";
		return $this->db->query($query);
	}

	 public function get($id_tabla='', $id=''){
	 
		 
		 $query = "SELECT * FROM wptg_tables_detail WHERE 1";
		 
		 if (!empty($id_tabla))
      $query .= " AND id_tabla='$id_tabla'";
	  if (!empty($id))
      $query .= " AND id='$id'";
	  
	   $query .= " ORDER BY id DESC ";
	 //echo  $query;
		return $this->db->get_results( $query, ARRAY_A );
		
	} 
	public function get2($id=''){
		
		if( is_array($id) ){
			$id = sprintf('(%s)', implode(',', $id));
		}
		else {
			$id = sprintf('(%d)', $id);
		}
		$row = $this->db->get_row("SELECT * FROM $this->table_name WHERE id IN $id", ARRAY_A);
		if($row){
			$row['tvalues'] = $this->unserialize($row['tvalues']);
		}
		return $row;
		
		} 
	public function get3($id_tabla=''){
		 $query = "SELECT * FROM wptg_tables_header WHERE 1";		 
		 if (!empty($id_tabla))
      $query .= " AND id_tabla='$id_tabla'";
	 
	   
		return $this->db->get_results( $query, ARRAY_A );
		
	} 
 
	public function get_page_items($curr_page, $per_page){
		$start = (($curr_page-1)*$per_page);
		$query = "SELECT * FROM $this->table_name ORDER BY id DESC LIMIT $start, $per_page";
		return $this->db->get_results( $query, ARRAY_A );
	}
	 
  public function delete_row($id){
		$table_name = 'wptg_tables_detail';
		$query = "DELETE FROM $table_name WHERE id=$id";
		return $this->db->query($query);
	}
	public function add_upload($id_tabla, $rows, $ruta, $nombre, $naturaleza='', $objeto='', $disponibilidad='', $estatus='', $name='', $cols='0', $tvalues=''){
		$table_name = 'wptg_tables_detail';
	
		$id_tabla 		= intval(stripslashes_deep($id_tabla));
		$rows 		= intval(stripslashes_deep($rows));
		$nombre 		= wp_strip_all_tags(stripslashes_deep($nombre));
		$creado 		= 'now()';
		

		$result = $this->db->insert( $table_name , array('name' => $name, 'rows' => $rows, 'cols' => $cols, 'tvalues' => $tvalues, 'naturaleza' => $naturaleza, 'objeto' => $objeto, 'disponibilidad' => $disponibilidad, 'estatus' => $estatus, 'ruta' => $ruta, 'nombre' => $nombre, 'id_tabla' => $id_tabla ) );
		 
		if($result)
			return $this->db->insert_id;
		return false;
	} 
	public function update_upload($id_tabla, $rows, $ruta, $nombre, $naturaleza='', $objeto='', $disponibilidad='', $estatus='', $id='', $name='', $cols='0', $tvalues=''){
		$table_name = 'wptg_tables_detail';
		$id_tabla 		= intval(stripslashes_deep($id_tabla));
		$rows 		= intval(stripslashes_deep($rows));
		$nombre 		= wp_strip_all_tags(stripslashes_deep($nombre));
		  
		return $this->db->update( $table_name , array('name' => $name, 'rows' => $rows, 'cols' => $cols, 'tvalues' => $tvalues, 'naturaleza' => $naturaleza, 'objeto' => $objeto, 'disponibilidad' => $disponibilidad, 'estatus' => $estatus, 'ruta' => $ruta, 'nombre' => $nombre, 'id_tabla' => $id_tabla ), array( 'id' => $id ) );
		 return $this->db->update( $this->table_name, array('name' => $name, 'rows' => $rows, 'cols' => $cols, 'tvalues' => $tvalues ), array( 'id' => $id ) );
	}
	public function update_header($id_tabla='', $name1='', $name2='', $name3='', $name4='', $name5='', $name6='', $name7=''){
		$table_name = 'wptg_tables_header';
		$id_tabla 		= intval(stripslashes_deep($id_tabla)); 
		  
		return $this->db->update( $table_name , array('name1' => $name1, 'name2' => $name2, 'name3' => $name3, 'name4' => $name4, 'name5' => $name5, 'name6' => $name6, 'name7' => $name7 ), array( 'id_tabla' => $id_tabla ) );
		
	}
	public function add_header($id_tabla='', $name1='', $name2='', $name3='', $name4='', $name5='', $name6='', $name7=''){
		$table_name = 'wptg_tables_header';
	
		$id_tabla 		= intval(stripslashes_deep($id_tabla)); 
		 
		$result = $this->db->insert( $table_name , array('name1' => $name1, 'name2' => $name2, 'name3' => $name3, 'name4' => $name4, 'name5' => $name5, 'name6' => $name6, 'name7' => $name7, 'id_tabla' => $id_tabla ) );
		if($result)
			return $this->db->insert_id;
		return false;
	} 
	public function get_upload($id_tabla){
		
			  $query = "SELECT *
						  FROM `wptg_upload`
						  WHERE 1";
										
		if (!empty($id_tabla))
      				$query .= " AND  wptg_upload.id_tabla='$id_tabla'";
		return $this->db->get_results( $query, ARRAY_A );
	}

	public function get_count(){
		$count = $this->db->get_var("SELECT COUNT(*) FROM $this->table_name");
		return isset($count)?$count:0;
	}

	private function serialize($item){
		return base64_encode(serialize($item));
	}

	private function unserialize($item){
		return unserialize(base64_decode($item));
	}
}

?>