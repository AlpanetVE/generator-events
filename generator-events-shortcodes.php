<?php
//Events
/* TEMPORALMENTE BORRADO PARA USAR CLASE
add_shortcode( 'alpage_event_shortchode', 'alpage_event_shortchode_func' );

function alpage_event_shortchode_func( $atts ) { // New function parameter $content is added!
   extract( shortcode_atts( array(
	  'title' => 'Title',	
	  'no_of_post' => '8',
	  'event_look' => 'simple',
   ), $atts ) );

   global $post;
   $i = 0;
   $result = $src = $date = $Ftime = $Ttime = $map = $ln_mon = '';
   
   if($event_look == 'simple'){
	   $result .= '<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">';	
		$today = date('m/d/Y');
		
		$argrs = array(
			'post_type' => 'rockon_event',
			'meta_key'  => 'rockon_event_sysdate',
			'meta_query' => array(
				array(
					'key' => 'rockon_event_sysdate',
					'value' => $today,
					'compare' => '>='
				)
			),
			'orderby'   => 'meta_value',
			'order'     => 'ASC',
			'posts_per_page' => $no_of_post
		);

		
		$GeneratorEvents = new GeneratorEvents();
		$EventArray = $GeneratorEvents-> get_itemsEvent(1,6);

		
		if(!empty($EventArray)):

			foreach ($EventArray as $key => $value) {			


			$result .= '<div class="col-lg-6 col-md-6 col-sm-6">
			<div class="rock_main_event">';


				$thumb_w = '550';
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


				$loc =  get_post_meta( $post->ID, 'rockon_event_sysloaction', true );
				$map =  get_post_meta( $post->ID, 'rockon_event_syscomma', true );

				global $rockon_data;
				if(isset($rockon_data['rockon_language']))
					setlocale(LC_TIME, $rockon_data['rockon_language']);
				$ln_mon = strftime("%B",strtotime($date));


				if (!empty($image)){	
					$result .='<div class="rock_main_event_image">
				   		<img src="'.esc_url($image).'" alt="" />
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
				<h2><a href="'.esc_url(get_the_permalink($post->ID)).'">'.$name.'</a></h2>
				<div class="blog_entry_meta">
				  <ul>
					<li><a href=""><i class="fa fa-clock-o"></i> '.esc_attr($Ftime).' - '.esc_attr($Ttime).'</a></li>
					<li><a href="'.esc_url('https://maps.google.com/maps?q='.$map).'" target="_blank"><i class="fa fa-map-marker"></i> '.__(esc_attr($siteName)).'</a></li>
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
		endif;
	   $result .= '</div></div>';	
   }
   return $result;   
}*/


?>