<?php
require_once('../../../wp-load.php');
global $wpdb;

$table_user_event = $wpdb->prefix."alpage_user_event";

$user = wp_get_current_user();




if(isset($_POST['api-select-test'])){

$id_event = $_POST['idevent'];
	$consulta= "SELECT count(*) as resul FROM $table_user_event where user_id = $user->ID and id_event = $id_event;";
	$res=$wpdb->get_var($consulta);

$rating=$_POST['api-select-test'];
if($res >= 1){

$results=$wpdb->update($table_user_event,array(
			'rating' =>$rating),
        array(
					'user_id'=>$user->ID,
					'id_event'=>$id_event
				));
echo $results;
} else {
	$results = $wpdb->insert($table_user_event, array(
					'user_id'    		=>  $user->ID,
					'id_event'	  	=>	$id_event,
					'rating' 	=>	$rating
				));
	echo $results;
     }
}else{

  echo 'Error en el POST';
}
