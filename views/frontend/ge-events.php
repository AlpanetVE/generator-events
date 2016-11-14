<?php
/**
 * WooCommerce Auth
 *
 * Handles wc-auth endpoint requests.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce/API
 * @since    2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GE_Event' ) ) :

class GE_Event {

	/**
	 * Setup class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		// Add query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Register auth endpoint
		add_action( 'init', array( __CLASS__, 'add_endpoint' ), 0 );

		// Handle auth requests
		add_action( 'parse_request', array( $this, 'handle_auth_requests' ), 0 );

		//add_action( 'template_redirect', array( $this, 'prefix_url_rewrite_templates2' ), 0  );
		//add_action( 'template_redirect', 'prefix_url_rewrite_templates' );
	}


	public function prefix_url_rewrite_templates2() {
	    if ( get_query_var( 'photos' ) && is_singular( 'movie' ) ) {
	        add_filter( 'template_include', function() {
	            return get_template_directory() . '/single-movie-image.php';
	        });
	    }
	 
	    if ( get_query_var( 'videos' ) && is_singular( 'movie' ) ) {
	        add_filter( 'template_include', function() {
	            return get_template_directory() . '/single-movie-video.php';
	        });
	    }
	}


	public function add_query_vars( $vars ) {
		$vars[] = 'wc-auth-version';
		$vars[] = 'wc-auth-route';
		$vars[] = 'seese';
		$vars[] = 'videos';
		$vars[] = 'photos';
		return $vars;
	}

	/**
	 * View
	 *
	 * @since 1.0.0
	 */
	public static function add_endpoint()
	{		
	    //add_rewrite_rule( '^people/generator-events-shortcodes.php$', '^people/seese2', 'top' );
	    global $wp_rewrite;
	    add_rewrite_tag('%name%','([^/]*)');
	    add_rewrite_rule('^Events/([^/]*)/?','index.php?page_id=12&name=$matches[1]','top');
	    $wp_rewrite->flush_rules();
	    
	}

/*
	static function add_endpoint() {
		add_rewrite_rule( '^wc-auth/v([1]{1})/(.*)?', 'index.php?wc-auth-version=$matches[1]&wc-auth-route=$matches[2]', 'top' );
	}*/


	/**
	 * Handle auth requests.
	 *
	 * @since 2.4.0
	 */
	public function handle_auth_requests() {

		global $wp;
		if ( array_key_exists( 'name', $wp->query_vars ) ) {
		        echo $this->alpage_events();
		    }
		return;
	}

	
 
	function alpage_events( ) { // New function parameter $content is added!
	   
	   $i = 0;
	   $result = $src = $date = $Ftime = $Ttime = $map = $ln_mon = '';
	   $event_look = 'simple';
	   if($event_look == 'simple'){
		   $result .= '<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">';	
			$today = date('m/d/Y');
			
					
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
					<h2><a href="'.null.'">'.$name.'</a></h2>
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
	}

}

endif;

return new GE_Event();
