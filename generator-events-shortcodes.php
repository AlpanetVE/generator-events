<?php
//Events
//TEMPORALMENTE BORRADO PARA USAR CLASE
add_shortcode( 'alpage_events_shortchode', 'alpage_events_shortchode_func' );

add_shortcode( 'alpage_detail_event_shortchode', 'alpage_detail_event_shortchode' );

add_shortcode( 'alpage_detail_site_shortchode', 'alpage_detail_site_shortchode' );


/*------------------------------------------------------------------------*/
/*------------------------------------------------------------------------*/
add_shortcode( 'star-rating', 'rating' );
add_action( 'wp_enqueue_scripts', 'function_rating' );

require_once( ABSPATH . "wp-includes/pluggable.php" );

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

  //Incluir styes y scripts de fineuploader
	  wp_register_style('fine-uploader',ALPAGE_URL .'css/fine-uploader-new.css',array(),'1','all');
	  wp_register_style('custom-uploader',ALPAGE_URL .'css/custom-uploader.css',array(),'1','all');
	  wp_register_script('script-fine-uploader', ALPAGE_URL . 'js/fine-uploader.js');

	  wp_enqueue_script('script-fine-uploader');
	  wp_enqueue_style('fine-uploader');
	  wp_enqueue_style('custom-uploader');
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
		<input type="radio" class="star" name="api-select-test" value="1" <?php echo ($res==1) ?  "checked='checked'": "";?>/>
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
	if (isset($_GET['nameSite']) && !empty($_GET['nameSite'])) {


	}
}

function alpage_detail_event_shortchode( $atts ) { // New function parameter $content is added!
   extract( shortcode_atts( array(
	  'title' => 'Title',
	  'no_of_post' => '8',
	  'event_look' => 'simple',
   ), $atts ) );

   	$result	= '';

   	global $post;

   	if (isset($_GET['nameEvent']) && !empty($_GET['nameEvent'])) {

		$GeneratorEvents = new GeneratorEvents();
		$EventArray = $GeneratorEvents-> get_itemsEvent(null,null,null, $_GET['nameEvent']);

    $comentariosArray= $GeneratorEvents->getComentsEvent($EventArray[0]['id']);
		$value = $EventArray[0];

    global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request)).'?nameEvent='.$_GET['nameEvent'] ;

		 ?>
		 <script type="text/javascript">
		 	var x = document.getElementsByClassName("rock_heading");
    		x[0].innerHTML = '<h1><?php echo $EventArray[0]['name'];?></h1>';
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
                     <div>Select files</div>
                 </div>
                 <button type="button" id="trigger-upload" class="btn btn-primary">
                     <i class="icon-upload icon-white"></i> Upload
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

					<style type="text/css">
						.pmargin{
							margin: 10px 20px 10px 10px;
						}
						.img-add-photo{
							float: left;
						}
						.btn-submit-comment{
							float: right;
						}
						.input-commed textarea{
							width: 100% !important;
							color: black;
						}
						.img-add-photo span.glyphicon {
						    font-size: 30px;
						    margin-left: 1px;
						}
						.img-add-photo input{
							position: absolute;
						    top: 0;
						    z-index: 3;
						    width: 100%;
						    height: 100%;
						    cursor: pointer;
						    opacity: 0;
						    padding-top: 47px;
						}
						.brightd{
							border-right: 1px solid #ddd;
						}
						hr.simple{
							float: left;
						    width: 100%;
						    border-color: rgba(125, 125, 125, 0.28);
						}

						.input-commed {
						    margin-bottom: 5px;
						}

						.banner-event{
							max-width: 100%;
						}
						.img-user{
							text-align: right;
    						padding: 0px;
						}


              .event-comment{
                	width: 100%;
                	float: left;
                	background: #0d0d0d;
                	border: 1px solid rgba(0,0,0,0);
                	padding: 20px;
                	margin-top: 30px;
                  color: seashell;
                }

                .event-comment h4{
                  font-weight: bold;
                }

                .comentarios{
                  margin: 30px;
                }

                .gravatar img{
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                    margin: 25px 0px;
                }

					</style>

<?php if(is_user_logged_in()){ ?>
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

            <div class="comentarios">
            <p>
              <?php echo $comentario->comentario;?>
            </p>
            </div>
              <h5 class="pull-right"> <?php echo $comentario->fecha ?> </h5>
        </div>
    </div>

      </div>

      <?php endforeach; ?>

						<div class="row">
              <h1> Leave a Comment</h1>

    <form class="form-coments" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
      <div class="col-xs-2">
        <img class="top-timeline-tweet-box-user-image avatar size32" src="https://pbs.twimg.com/profile_images/774341475802968064/qtMQRmhI_normal.jpg" alt="Oscar J. Lopez">
      </div>
      <div class="">
          <textarea name="comment" rows="5" cols="40" maxlength="254" placeholder="Your Comment"></textarea>
            <!-- <div id="fine-uploader-manual-trigger"></div> -->
        </div>

        <p>
          <div class="btn-submit-comment">
            <input type="submit" class="btn btn-default btn-md" value="Enviar Comentario">
          </div>
        </p>
        <input type="hidden" name="url" value="<?php echo  $current_url ?>">
        <input type="hidden" name="event_id" value= "<?php echo $value['id'] ?>" >
        <input type="hidden" name="ge_tipo" value="ge_comment">
    </form>
							<!-- <div class="col-xs-2">
>>>>>>> eedba880deb6a142232edd95f0768ed29d2befe3
								<img class="top-timeline-tweet-box-user-image avatar size32" src="https://pbs.twimg.com/profile_images/774341475802968064/qtMQRmhI_normal.jpg" alt="Oscar J. Lopez">
							</div>
							<div class="col-xs-11 col-xs-10">
								<div class="input-commed">
									<textarea></textarea>
								</div>
								<div class="foot-form-add">
									<div class="img-add-photo">
										<span class="glyphicon glyphicon-camera" aria-hidden="true">
											<input type="file" name="media_empty" accept="image/gif,image/jpeg,image/jpg,image/png,video/mp4,video/x-m4v" multiple="" class="file-input js-tooltip" data-original-title="Añadir fotos o video" data-delay="150">
										</span>
									</div>
									<div class="btn-submit-comment">
										<input type="submit" class="btn btn-default btn-md">
									</div>
								</div>
							</div> -->
	</div>

<div id="fine-uploader-manual-trigger"></div>
					</div>
					<hr class="simple">

					<?php } ?>


	  			</div>
	  			<div class="col-xs-12 col-md-3 ">
		  			<div style="margin: 20px;"><?php echo $value['name_site'];

		  			function_rating();
		  			echo rating ( $arrayName = array('id_event' =>  $value['id'] ) ); ?>
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
        	                         itemLimit: 1
        	                        // sizeLimit: 2048000 // 50 kB = 50 * 1024 bytes
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

   	}


	return $result;

}






function alpage_events_shortchode_func( $atts ) { // New function parameter $content is added!
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

				$name_link =  $value['name_link'];

				//$loc =  get_post_meta( $post->ID, 'rockon_event_sysloaction', true );
				$map =  get_post_meta( $post->ID, 'rockon_event_syscomma', true );

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


?>
