<?php
//Events
//TEMPORALMENTE BORRADO PARA USAR CLASE
add_shortcode( 'alpage_events_shortchode', 'alpage_events_shortchode_func' );

add_shortcode( 'alpage_detail_event_shortchode', 'alpage_detail_event_shortchode' );

add_shortcode( 'alpage_detail_site_shortchode', 'alpage_detail_site_shortchode' );

add_action( 'wp_enqueue_scripts', 'fine_uploader_scripts' );
/*------------------------------------------------------------------------*/
/*------------------------------------------------------------------------*/


require_once( ABSPATH . "wp-includes/pluggable.php" );

function registerFileFront(){
	wp_register_style('generateFrontEventCss', STAR_URL . 'css/generateFrontEvent.css', array(), '1', 'all');
	wp_register_script('generateFrontEventJs', STAR_URL . 'js/site-event.js',array('jquery'));

	wp_enqueue_style('generateFrontEventCss');
	wp_enqueue_script('generateFrontEventJs');


  wp_enqueue_script('script-fine-uploader');
  wp_enqueue_style('fine-uploader');
  wp_enqueue_style('custom-uploader');

}

function registerLightGallery(){
	wp_register_style('lightgallery', STAR_URL . 'css/lightgallery.min.css', array(), '1', 'all');
	wp_register_script('picturefull', "https://cdn.jsdelivr.net/picturefill/2.3.1/picturefill.min.js", '','');
  wp_register_script('lightgallery-js', STAR_URL . 'js/lightgallery.js', array('jquery'),'',true);
  wp_register_script('lg-fullscreen', STAR_URL . 'js/lg-fullscreen.js', array('jquery'),'',true);
  wp_register_script('lg-thumbnail', STAR_URL . 'js/lg-thumbnail.js', array('jquery'),'',true);
  wp_register_script('lg-video', STAR_URL . 'js/lg-video.js', array('jquery'),'',true);
  wp_register_script('lg-autoplay', STAR_URL . 'js/lg-autoplay.js', array('jquery'),'',true);
	wp_register_script('lg-zoom', STAR_URL . 'js/lg-zoom.js', array('jquery'),'',true);
  wp_register_script('lg-hash', STAR_URL . 'js/lg-hash.js', array('jquery'),'',true);
  wp_register_script('lg-pager', STAR_URL . 'js/lg-pager.js', array('jquery'),'',true);
	wp_register_script('mousewheel', STAR_URL . 'js/jquery.mousewheel.min.js', array('jquery'),'',true);


	wp_enqueue_style('lightgallery');
	wp_enqueue_script('picturefull');
	wp_enqueue_script('lightgallery-js');
	wp_enqueue_script('lg-fullscreen');
	wp_enqueue_script('lg-thumbnail');
	wp_enqueue_script('lg-video');
	wp_enqueue_script('lg-autoplay');
	wp_enqueue_script('lg-zoom');
	wp_enqueue_script('lg-hash');
	wp_enqueue_script('lg-pager');
	wp_enqueue_script('mousewheel');

}

function fine_uploader_scripts(){
	wp_register_style('fine-uploader',STAR_URL .'css/fine-uploader-new.css',array(),'1','all');
	wp_register_style('custom-uploader',STAR_URL .'css/custom-uploader.css',array(),'1','all');
	wp_register_script('script-fine-uploader', STAR_URL . 'js/fine-uploader.js');


	wp_enqueue_script('script-fine-uploader');
	wp_enqueue_style('fine-uploader');
	wp_enqueue_style('custom-uploader');

}


function function_rating() {

  wp_register_script('rating_1', STAR_URL . 'js/jquery.MetaData.js',array('jquery'));
  wp_register_script('rating_2', STAR_URL . 'js/jquery.rating.js',array('jquery'));
  wp_register_script('rating_3', STAR_URL . 'js/send_rating.js',array('jquery'));
  wp_register_style('rating_styles', STAR_URL . 'css/jquery.rating.css', array(), '1', 'all');
  wp_register_style('rating_styles_2', STAR_URL . 'css/rating.css', array(), '1', 'all');
  wp_enqueue_script('rating_1');
  wp_enqueue_script('rating_2');
  wp_enqueue_script('rating_3');
  wp_enqueue_style('rating_styles');
  wp_enqueue_style('rating_styles_2');


}
function rating( $atts ) {

	$args = shortcode_atts( array(
		'id_event' => ''
	), $atts  );

	$id_event=$args['id_event'];

	$user = wp_get_current_user();
	$GeneratorEvents = new GeneratorEvents();
	$respuesta = $GeneratorEvents-> get_rating_user($id_event,$user->ID);
	$res=$respuesta['rating'];
	$url_ajax=ALPAGE_URL.'ajax_vote.php';

	ob_start();
	?>
	<form id="form-star" name="api-select">
		<input type="radio" class="star required" name="api-select-test" value="1" <?php echo ($res==1) ?  "checked='checked'": "";?>/>
		<input type="radio" class="star" name="api-select-test" value="2" <?php echo ($res==2) ?  "checked='checked'": ""; ?>/>
		<input type="radio" class="star" name="api-select-test" value="3" <?php echo ($res==3) ?  "checked='checked'": ""; ?>/>
		<input type="radio" class="star" name="api-select-test" value="5" <?php echo ($res==4) ?  "checked='checked'": ""; ?>/>
		<input type="radio" class="star" name="api-select-test" value="4" <?php echo ($res==5) ?  "checked='checked'": ""; ?>/>
<?php if(is_user_logged_in()): ?>
    <input type="button" id="btn-vote" data-event="<?php echo $id_event ?>" data-url="<?php echo $url_ajax ?>" value="Vote"/>
<?php endif; ?>
    <span></span>
		<br/>
	</form>
	<?php
	$output = ob_get_clean();
	return $output;
}

/*------------------------------------------------------------------------*/
/*------------------------------------------------------------------------*/

function alpage_detail_site_shortchode( $atts ) { // New function parameter $content is added!
	$result	= '';

	extract( shortcode_atts( array(
	  'title' => 'Title',
	  'no_of_post' => '8',
	  'event_look' => 'simple',
	), $atts ) );

	registerFileFront();

	if (isset($_GET['nameSite']) && !empty($_GET['nameSite'])) {
		$name_link			= $_GET['nameSite'];
		$GeneratorEvents 	= new GeneratorEvents();
		$start = '1';
		$per_page 	= '4';

		$SiteArray			= $GeneratorEvents->get_itemsSite(null, null,null,null,$name_link);
		if (!empty($SiteArray[0])) {


		$value 				= $SiteArray[0];


		$EventArray 		= $GeneratorEvents->get_itemsEvent(null,null,null,null,$name_link, 'news', 'se.`date` asc');
		$EventArraBefore	= $GeneratorEvents->get_itemsEvent($start,$per_page,null,null,$name_link, 'before', 'se.`date` desc');

		if (empty($EventArray)) {
			//no hay futuros eventos
			//mostrar pasados eventos
		}

		if (empty($EventArraBefore)) {
			//no hay pasados eventos
			//no mostrar btn de eventos pasados
			//si no hay futuros eventos, mostrar los pasados eventos
		}

		if (empty($EventArray) && empty($EventArraBefore)) {
			//no hay ni pasados ni futuros eventos
		    //Proximamente, nuevos eventos
		}

		$Ftime = strtoupper(date("g:i a",strtotime($value['opening_hour'])));
		$Ttime = strtoupper(date("g:i a",strtotime($value['closed_hour'])));


		 ?>
		<script type="text/javascript">
			var x = document.getElementsByClassName("rock_heading");
			x[0].innerHTML = '<h1><?php echo $value['name'];?></h1>';


		jQuery(document).ready(function($) {
			<?php
			if (empty($EventArray) && !empty($EventArraBefore)) {
				echo "setTimeout(function(){ jQuery('#see-more-event').click() }, 1000);";
			}
			if (empty($EventArray) && empty($EventArraBefore)) {
				echo "jQuery('#will-be-event').show();
					jQuery('#site-event-main').hide();";
			}
			?>
		});

		</script>
		<?php
		ob_start();
		?>

		<div class="row row-centered center">
			<div id="will-be-event" class="col-xs-12 col-md-9 brightd site-event-main" style="display: none;">
  				<span>There aren't events yet.</span>
			</div>
			<div id="site-event-main" class="col-xs-12 col-md-9 brightd site-event-main">

				<?php if (!empty($EventArray)){ ?>
						<div class="title-site-event">
							Events
						</div>
						<?php
						foreach ($EventArray as $key => $Event) {
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
								<div class="site-event-cont rock_main_event_image">
									<img class="banner-event" src="<?php echo esc_url($image);?>" alt="" />
									<div class="rock_main_event_image_overlay">
										<span><?php echo $Event['name']; ?></span>
									</div>
								</div>
							</a>
					<?php }
					} 	?>

				<?php
				if (!empty($EventArraBefore)){
				?>
					<hr class="simple">
					<div id="cont-event-before">
						<!--here are the events-->
					</div>
					<input disabled="disabled" type="button" class="see-more btn-default" value="See before" id="see-more-event" data-name='<?php echo $name_link ;?>' data-start='<?php echo $start ;?>' data-action="getEventBefore">

				<?php } ?>
				<hr>

			</div>
			<div class="col-xs-12 col-md-3 ">
				<div class="tittle-right" >
					<?php echo $value['name'];?>
				</div>
				<div class="point-stars">
					<?php
					function_rating();
					echo rating ( $arrayName = array('id_event' =>  $value['id'] ) );
					?>
				</div>
				<?php

				if (!empty($Ftime)) {
					echo 'Open: '.$Ftime.'<br>';
				}
				if (!empty($Ttime)) {
					echo 'Close: '.$Ttime.'<br><br>';
				}
				if (!empty($value['clothing_type'])) {
					echo '<p> Clothing: '.$value['clothing_type'].'</p>';
				}
				if (!empty($value['ticket_selling'])) {
					echo '<p> Ticket selling: '.$value['ticket_selling'].'</p>';
				}
				if (!empty($value['addres'])) {
					echo '<p> Addres: '.$value['addres'].'</p>';
				}
				?>
			</div>
		</div>
		<?php



		}else{
			$SiteArray			= $GeneratorEvents->redirect_user('');
		}


		$result = ob_get_clean();
	}

	return $result;
}

function alpage_detail_event_shortchode( $atts ) { // New function parameter $content is added!
   extract( shortcode_atts( array(
	  'title' => 'Title',
	  'no_of_post' => '8',
	  'event_look' => 'simple',
   ), $atts ) );

	$result	= '';

	$GeneratorEvents = new GeneratorEvents();
	registerFileFront();
	registerLightGallery();

	if (isset($_GET['nameEvent']) && !empty($_GET['nameEvent'])) {

		$EventArray = $GeneratorEvents-> get_itemsEvent(null,null,null, $_GET['nameEvent']);
		$value = $EventArray[0];

		if (!empty($value)) {


		$user = wp_get_current_user();

		$comentariosArray= $GeneratorEvents->getComentsEvent($EventArray[0]['id']);
		$photosArray= $GeneratorEvents->getPhotosEvent($EventArray[0]['id']);

		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request)).'?nameEvent='.$_GET['nameEvent'] ;

		 ?>
		<script type="text/javascript">
			var x = document.getElementsByClassName("rock_heading");
			x[0].innerHTML = '<h1><?php echo $value['name'];?></h1>';
		</script>
		<script type="text/template" id="qq-template-manual-trigger">
         <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
             <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                 <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
             </div>
             <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                 <span class="qq-upload-drop-area-text-selector"></span>
             </div>
             <div class="buttons">
                 <div class="qq-upload-button-selector qq-upload-button">
                     <span class="glyphicon glyphicon-camera green-btn-drop"> </span>
                 </div>
                 <button type="button" id="trigger-upload" class="btn btn-primary green-btn-drop">
                     <span class="glyphicon glyphicon-upload"></span> Upload
                 </button>
             </div>
             <span class="qq-drop-processing-selector qq-drop-processing">
                 <span>Processing dropped files...</span>
                 <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
             </span>
             <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                 <li>
                     <div class="qq-progress-bar-container-selector">
                         <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                     </div>
                     <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                     <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                     <span class="qq-upload-file-selector qq-upload-file"></span>
                     <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                     <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                     <span class="qq-upload-size-selector qq-upload-size"></span>
                     <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                     <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                     <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
                     <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                 </li>
             </ul>

             <dialog class="qq-alert-dialog-selector">
                 <div class="qq-dialog-message-selector"></div>
                 <div class="qq-dialog-buttons">
                     <button type="button" class="qq-cancel-button-selector">Close</button>
                 </div>
             </dialog>

             <dialog class="qq-confirm-dialog-selector">
                 <div class="qq-dialog-message-selector"></div>
                 <div class="qq-dialog-buttons">
                     <button type="button" class="qq-cancel-button-selector">No</button>
                     <button type="button" class="qq-ok-button-selector">Yes</button>
                 </div>
             </dialog>

             <dialog class="qq-prompt-dialog-selector">
                 <div class="qq-dialog-message-selector"></div>
                 <input type="text">
                 <div class="qq-dialog-buttons">
                     <button type="button" class="qq-cancel-button-selector">Cancel</button>
                     <button type="button" class="qq-ok-button-selector">Ok</button>
                 </div>
             </dialog>
         </div>
     </script>
		 <?php

			ob_start();

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


			?>

			<div class="row row-centered center">
		  		<div class="col-xs-12 col-md-9 brightd">


		  		<?php
		  			if (!empty($image)){
					echo '<div class="text-center">
					   		<img class="banner-event" src="'.esc_url($image).'" alt="" />
					   		<div class="rock_main_event_image_overlay">
							</div>
						  </div>';
					}
					?>

					<?php if (!empty($desc)){
					echo '<p class="pmargin">'.esc_attr($desc).'</p>';
					}
					?>

					<hr>

<div class="col-xs-12 col-md-9 col-lg-12 ">

<?php  foreach($comentariosArray as $comentario): ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 event-comment">
        <div class="col-lg-2 col-md-2 col-sm-2 gravatar">

        <?php echo get_avatar($comentario->id); ?>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-10">
            <p>
              <h4>  <?php echo $comentario->nic;?> </h4>
            </p>

            <?php if (!empty($comentario->comentario)) { ?>
            	<div class="comentarios">
					<p>
						<?php echo $comentario->comentario;?>
					</p>
				</div>
            <?php } ?>



            <?php if(!empty($comentario->img)):
                $img=basename($comentario->img);
               ?>
            <div class="img-comentario">
              <p>
                <?php echo '<a href="'.$comentario->img.'" target="_blank">'.cl_image_tag($img, array("alt"=>"sample","width"=>200, "crop"=>"thumb","cloud_name" => "darwin123")).'</a>' ?>
              </p>
            </div>
          <?php endif;?>
              <h5 class="pull-right"> <?php echo $comentario->fecha ?> </h5>
        </div>
    </div>

      </div>

      <?php endforeach; ?>

<?php if(!is_user_logged_in()) { ?>
	<h3>Would you like to comment or post a photo? </h3>
	<p>
		Login with:
	</p>
	<?php do_action('oa_social_login');  }  ?>

<?php if(is_user_logged_in()){ ?>
						<div class="row">
              <h1> Leave a Comment</h1>

    <form class="form-coments" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
      <div class="div-comments">
				<?php echo get_avatar($user->ID,64); ?>
				<!-- <img class="top-timeline-tweet-box-user-image avatar size32" src="https://pbs.twimg.com/profile_images/774341475802968064/qtMQRmhI_normal.jpg" alt="Oscar J. Lopez"> -->
      </div>
      <div class="div-comments" >
          <textarea name="comment" rows="5" cols="55" maxlength="254" placeholder="Your Comment"></textarea>

        </div>


<div id="fine-uploader-manual-trigger"></div>
        <input type="hidden" name="url" value="<?php echo  $current_url ?>">
        <input type="hidden" name="event_id" value= "<?php echo $value['id'] ?>" >
        <input type="hidden" name="ge_tipo" value="ge_comment">
          <!-- <div id="fine-uploader-manual-trigger"></div> -->
          <p>
            <div class="btn-submit-comment">
              <input type="submit" class="btn btn-default btn-md" value="Send Comment">
            </div>
          </p>
    </form>

	</div>
		<?php } ?>

		<hr class="simple">
		<div class="row">
			<div class="col-sm-12 home">
				<div class="demo-gallery text-center">
				<h2> Event Gallery </h2>
				<ul id="lightgallery" class="list-unstyled row">
					<?php  foreach ($photosArray as $foto):
					$img=basename($foto->ruta_img);
					$coment=$foto->comentario;
					?>
					<li class="col-xs-6 col-sm-4 col-md-3"  data-src=<?php echo $foto->ruta_img ?> data-sub-html=<?php echo $coment;?>>
						<a href="">
						<img class="img-responsive" src=<?php echo cloudinary_url($img,
						array("width"=>200,"cloud_name" => "darwin123"));  ?> >
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
				</div>
			</div>
		</div>

	</div>
	  			</div>
	  			<div class="col-xs-12 col-md-3 ">
	  				<div class="tittle-right" >
						<a href="<?php echo ALPAGE_URL_SITE.'?nameSite='.$value['site_link'];?>">
							<?php echo $value['name_site'];?>
						</a>
					</div>
					<div class="point-stars">
						<?php
						function_rating();
						echo rating ( $arrayName = array('id_event' =>  $value['id'] ) );
						?>
					</div>

		  			<?php
		  			if (!empty($date)) {
		  				echo '<p> Date: '.strftime("%B %d, %Y",strtotime($date)).'</p>';
		  				if (!empty($Ftime)) {
		  					echo '&nbsp;&nbsp;&nbsp;'.$Ftime;
		  				}
		  				if (!empty($Ttime)) {
		  					echo ' - '.$Ttime.'<br><br>';
		  				}
		  			}
		  			if (!empty($value['clothing_type'])) {
		  				echo '<p> Clothing: '.$value['clothing_type'].'</p>';
		  			}
		  			if (!empty($value['ticket_selling'])) {
		  				echo '<p> Ticket selling: '.$value['ticket_selling'].'</p>';
		  			}
		  			if (!empty($value['addres'])) {
		  				echo '<p> Addres: '.$value['addres'].'</p>';
		  			}
		  			?>
				</div>
	  		</div>
				<script type="text/javascript">
								jQuery(document).ready(function($){
										$('#lightgallery').lightGallery();
								});
								</script>
        <script>
        url_endpoint='<?php echo ALPAGE_URL.'endpoint.php' ?>';
        	            var manualUploader = new qq.FineUploader({
        	                element: document.getElementById('fine-uploader-manual-trigger'),
        	                template: 'qq-template-manual-trigger',
        	                request: {
        	                    endpoint: url_endpoint//'php-traditional-server/endpoint.php'
        	                },
        	                thumbnails: {
        	                    placeholders: {
        	                        waitingPath: '/source/placeholders/waiting-generic.png',
        	                        notAvailablePath: '/source/placeholders/not_available-generic.png'
        	                    }
        	                },
        	                    validation: {
        	                        allowedExtensions: ['jpeg', 'jpg', 'png', 'gif'],
        	                         itemLimit: 1,
        	                         sizeLimit: 2048000 // 50 kB = 50 * 1024 bytes
        	                    },
        	                autoUpload: false,
        	                debug: true
        	            });

        	            qq(document.getElementById("trigger-upload")).attach("click", function() {
        	                manualUploader.uploadStoredFiles();

        	            });
        	        </script>


			<?php
			$result = ob_get_clean();

		}else{
			$GeneratorEvents->redirect_user('');
		}

   	}else{
   		$GeneratorEvents->redirect_user('/events');
   	}


	return $result;

}






function alpage_events_shortchode_func( $atts ) { // New function parameter $content is added!
   extract( shortcode_atts( array(
	  'title' => 'Title',
	  'no_of_post' => '8',
	  'event_look' => 'simple',
   ), $atts ) );

   registerFileFront();

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
		$start = '1';
		$per_page 	= '4';

		$EventArray 		= $GeneratorEvents->get_itemsEvent(null,null,null,null,null, 'news', 'se.`date` asc');
		$EventArraBefore	= $GeneratorEvents->get_itemsEvent($start,$per_page,null,null,null, 'before', 'se.`date` desc');



		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			<?php
			if (empty($EventArray) && !empty($EventArraBefore)) {
				echo "setTimeout(function(){ jQuery('#see-more-event').click() }, 1000);";
			}
			?>
		});
		</script>
		<?php


		if(!empty($EventArray)):

			foreach ($EventArray as $key => $value) {


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

		endif;

		if (!empty($EventArraBefore)){
		$result .= '<hr class="simple">
			<div id="cont-event-before">
				<!--here are the events-->
			</div>
			<div class="text-center col-xs-12">
				<input disabled="disabled" type="button" class="see-more btn-default" value="See before" id="see-more-event"
 data-start="'.$start.'" data-action="getEventsBefores" >
 			</div>';

		}
	   $result .= '</div></div>';
   }
   return $result;
}


?>
