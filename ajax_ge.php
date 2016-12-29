<?php
require_once('../../../wp-load.php');
require_once('define.php');
require_once('classes/class-generator-events.php');
global $wpdb;

$GeneratorEvents 	= new GeneratorEvents();



if ($_POST['action']=='getEventBefore') {
	$result		= '';
	$moreEvent 	= '';
	$html 		= '';
	$name_link  = $_POST['name_link'];
	$start		= $_POST['start'];
	$newStart	= $start+1;
	$per_page 	= 4;

	$EventArraBefore	= $GeneratorEvents->get_itemsEvent($start,$per_page,null,null,$name_link, 'before', 'se.`date` desc');
	$EventArraBeforeMore= $GeneratorEvents->get_itemsEvent($newStart,4,null,null,$name_link, 'before', 'se.`date` desc');


	ob_start();
	if (!empty($EventArraBefore)){
		if (empty($EventArraBeforeMore)) {
			$moreEvent='false';
		}
		foreach ($EventArraBefore as $key => $Event) {
			$thumb_w = '350';
			$thumb_h = '140';

			if ($Event['poster']){
				$src = ALPAGE_URL_UPLOADS.$Event['poster'];
			}else{
				$src = ALPAGE_URL.'images/no_image_350x140.jpg';
			}
			$image = aq_resize($src, $thumb_w, $thumb_h, true);
			?>
			<a href="<?php echo ALPAGE_URL_EVENT.'?nameEvent='.$Event['name_link'];?>">
				<div class="rock_main_event_image site-event-cont">				
					<img class="banner-event" src="<?php echo esc_url($image);?>" alt="" />				
					<div class="rock_main_event_image_overlay">
						<span><?php echo $Event['name']; ?></span>
					</div>
				</div>
			</a>
	<?php }

	$html = ob_get_clean();
	$result = 'true';
	}
	

	echo json_encode ( array (
        "result" => $result,
        "start" => $newStart,
        "html" => $html,
        "moreEvent" => $moreEvent
    ));
}
?>