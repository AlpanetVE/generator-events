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
	$EventArraBeforeMore= $GeneratorEvents->get_itemsEvent($newStart,$per_page,null,null,$name_link, 'before', 'se.`date` desc');


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


if ($_POST['action']=='getEventsBefores') {
	$resultAjax	= '';
	$moreEvent 	= '';
	$html 		= '';
	$result 	= '';
	$start		= $_POST['start'];
	$newStart	= $start+1;
	$per_page 	= 4;
	$i = 0;

	$EventArraBefore	= $GeneratorEvents->get_itemsEvent($start,$per_page,null,null,null, 'before', 'se.`date` desc');
	$EventArraBeforeMore= $GeneratorEvents->get_itemsEvent($newStart,$per_page,null,null,null, 'before', 'se.`date` desc');

	if (!empty($EventArraBefore)) {
		if (empty($EventArraBeforeMore)) {
			$moreEvent='false';
		}
		foreach ($EventArraBefore as $key => $value) {


			$result .= '<div class="col-lg-6 col-md-6 col-sm-6">
			<div class="rock_main_event">';


				$thumb_w = '500';
				$thumb_h = '200';
				if ($value['poster']){
					$src = ALPAGE_URL_UPLOADS.$value['poster'];

				}else{
					$src = ALPAGE_URL.'images/no_image.jpg';
				}


				$image = aq_resize($src, $thumb_w, $thumb_h, true);


				$date = $value['date'];



				$Ftime = strtoupper(date("g:i a",strtotime($value['opening_hour'])));
				$Ttime = strtoupper(date("g:i a",strtotime($value['closed_hour'])));


				$name =  $value['name'];
				$desc =  $value['description'];
				$siteName = $value['name_site'];

				$name_link =  $value['name_link'];


				global $rockon_data;
				if(isset($rockon_data['rockon_language']))
					setlocale(LC_TIME, $rockon_data['rockon_language']);
				$ln_mon = strftime("%B",strtotime($date));


				if (!empty($image)){
					$result .='<div>
				   		<a href="'.ALPAGE_URL_EVENT.'?nameEvent='.$value['name_link'].'"><img src="'.esc_url($image).'" alt="" /></a>
				   		<div class="rock_main_event_image_overlay">
						</div>
					  </div>';
				}

			  $result .='<div class="rock_main_event_detail">
				<div class="rock_event_date">
				   <div class="event_date">
					<h1>'.date('d',strtotime($date)).'</h1>
					<p>'.esc_attr($ln_mon).'</p>
				  </div>
				</div>
				<h2><a href="'.ALPAGE_URL_EVENT.'?nameEvent='.$value['name_link'].'">'.$name.'</a></h2>
				<div class="blog_entry_meta">
				  <ul>
					<li><a href=""><i class="fa fa-clock-o"></i> '.esc_attr($Ftime).' - '.esc_attr($Ttime).'</a></li>
					<li><a href="'.ALPAGE_URL_SITE.'?nameSite='.$value['site_link'].'" target="_blank"><i class="fa fa-map-marker"></i> '.__(esc_attr($siteName)).'</a></li>
				  </ul>
				</div>
				<p>'.esc_attr($desc).'</p>
			  </div>
			</div>
		  </div>';
		  $i++;
			if($i%2 == 0){
				$result .= '<div class="clearfix"></div>';
			}

		}

		$resultAjax = 'true';
		$html 		= $result;
	}





	echo json_encode ( array (
        "result" => $resultAjax,
        "start" => $newStart,
        "html" => $html,
        "moreEvent" => $moreEvent
    ));
}

?>