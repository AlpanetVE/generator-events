<?php
/**
 * List Tables View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * List Tables View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class GeneratorEventList extends WP_List_Table {

	private $db;

    public function __construct(){

    	$this->load_dependencies();

    	$this->db = GeneratorEvents::get_instance();

    	global $status, $page;

		parent::__construct( array(
			'singular'  => 'id_event',
			'plural'    => 'events',
			'ajax'      => false,
			'screen'    => $_REQUEST['page']
		) );
    }

    function load_dependencies(){
    	require_once( ALPAGE_ABSPATH . 'classes/class-generator-events.php' );
    }

	function get_columns(){
		$columns = array(
			'cb'	=> '<input type="checkbox" />',
			'id'	=> __('ID', 'wptg-plugin'),
			'name'	=> __('Name', 'wptg-plugin'),
			'name_site'	=> __('Site', 'wptg-plugin'),
			'date'	=> __('Date', 'wptg-plugin')
		);
		return $columns;
	}

    function column_default($item, $column_name){
        return stripslashes($item[$column_name]);
    }

	function column_name($item){
		//Build row actions
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&id_event=%s">%s</a>', $_REQUEST['page'],'edit',$item['id'], __('Edit', 'wptg-plugin') )
		);

		//Return the title contents
		return sprintf('%1$s %2$s',
			/*$1%s*/ stripslashes($item['name']),
			/*$2%s*/ $this->row_actions($actions)
		);
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
		);
	}

    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete', 'wptg-plugin')
        );
        return $actions;
    }

	function prepare_items() {
		$per_page               = 25;
		$hidden                 = array();
		$columns                = $this->get_columns();
		$sortable               = array();
		$curr_page              = $this->get_pagenum();

		$total_items            = $this->db->getCountEvents();
		$data                   = $this->db->get_itemsEvent($curr_page, $per_page);

		$this->items            = $data;
		$this->_column_headers  = array($columns, $hidden, $sortable);

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		) );
	}

	function show(){
		echo sprintf('<div class="wrap">');
    	echo sprintf( '<h2>%s <a class="add-new-h2" href="%s">%s</a></h2>', __('Events', 'wptg-plugin'), admin_url('admin.php?page=GeneratorEvents&action=add'), __('Add New', 'wptg-plugin') );
        echo sprintf('<form method="GET"><input type="hidden" name="page" value="'.$_GET['page'].'">');
	    $this->prepare_items();
		$this->display();
	    echo sprintf('</form>');
    	echo sprintf('</div>');
	}


	function showForm($id=null){
		wp_enqueue_style('alpage_admin_style');
		wp_enqueue_style('datetimepicker');
		wp_enqueue_style('alpage_filecomponent');
		wp_enqueue_script('alpage_filecomponent',ALPAGE_URL.'views/backend/js/custom-file-input.js');
		$Sites=$this->db->getSite();

		if (!empty($id)) {
			$data=$this->db->get_itemsEvent(1,1,$id);
			$savedata = 'update';

			if (empty($data[0]['poster']))
				$msjPoster = 'Choose a file';
			else
				$msjPoster = 'Update image';

			$siteid	=isset($data[0]['siteid'])?$data[0]['siteid']:'';
		}else {
			$data=array();
			$savedata = 'insert';
			$msjPoster = 'Choose a file';

			$siteid	=isset($_GET['id_site'])?$_GET['id_site']:'';
		}


		$name			=isset($data[0]['name'])?$data[0]['name']:'';
		$date			=isset($data[0]['date'])?$data[0]['date']:'';
		$description	=isset($data[0]['description'])?$data[0]['description']:'';
		$clothing_type	=isset($data[0]['clothing_type'])?$data[0]['clothing_type']:'';
		$ticket_selling	=isset($data[0]['ticket_selling'])?$data[0]['ticket_selling']:'';
		$opening_hour	=isset($data[0]['opening_hour'])?$data[0]['opening_hour']:'';
		$closed_hour	=isset($data[0]['closed_hour'])?$data[0]['closed_hour']:'';

		?>

		<script>
		jQuery(document).ready(function($){
		   jQuery('#table-opening_hour,#table-closed_hour').datetimepicker({
			  datepicker:false,
			  format:'H:i'
			});
			jQuery('#table-date').datetimepicker({
		      timepicker: false,
		      format:'Y-m-d',
			  formatDate:'Y-m-d',
			  minDate:'-1970/01/02' // yesterday is minimum date
		    });
		});
		</script>
		<script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script>
		<div class="wrap">
		<h1>Add Site Event</h1>
			<form method="post" enctype="multipart/form-data" action="?page=GeneratorEvents&action=list" >
				<input type="hidden" name="id_site" value="<?php echo $id; ?>" >
				<input type="hidden" name="savedata" value="<?php echo $savedata; ?>" >
				<div class="postbox ">
				<div class="form-wrap inside">
					<div class="form-field">
						<label for="table-name"><?php _e( 'Site', 'GeneratorEvents' ); ?>:</label>
						<select required name="GeForm[siteid]">
							<option value=""> Select Site Here </option>
							<?php
								foreach ($Sites as $key => $site) {
									echo '<option value="'.$site['id'].'"';
									if($site['id']==$siteid)
										echo ' selected="selected"';
									echo '"> '.$site['name'].'</option>';
								}
							?>
						</select>
					</div>
					<div class="form-field">
						<label for="table-name"><?php _e( 'Event Name', 'GeneratorEvents' ); ?>:</label>
						<input required type="text" name="GeForm[name]" value="<?php echo $name; ?>" id="table-name" class="placeholder placeholder-active"  placeholder="<?php esc_attr_e( 'Enter Event Name here', 'GeneratorEvents' ); ?>" />
					</div>




					<div class="form-field">
						<label for="table-latitude"><?php _e( 'Upload Poster', 'GeneratorEvents' ); ?> <span style="color: red;font-size: 11px;"><?php _e( '(300px with 200px height)', 'GeneratorEvents' ); ?></span>  :</label>


						<div class="box">
						<input accept="image/*"  name="poster" id="table-poster" type="file" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" />
						<label for="table-poster"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg>
						<span><?php _e( $msjPoster, 'GeneratorEvents' ); ?>&hellip;</span>
						</label>
					</div>


					</div>


					<div class="form-field">
						<label for="table-date"><?php _e( 'Date', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="GeForm[date]" value="<?php echo $date; ?>" id="table-date" />
					</div>


					<div class="form-field">
						<label for="site-description"><?php _e( 'Description', 'GeneratorEvents' ); ?>:</label>
						<textarea name="GeForm[description]" id="site-description" rows="2"><?php echo $description; ?></textarea>
						<p><?php _e( 'Enter the description of the Event (Optional)', 'GeneratorEvents' ); ?></p>
					</div>

					<div class="form-field">
						<label for="table-clothing_type"><?php _e( 'Clothing Type', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="GeForm[clothing_type]" value="<?php echo $clothing_type; ?>" id="table-clothing_type" />
					</div>

					<div class="form-field">
						<label for="table-ticket_selling"><?php _e( 'Ticket Selling', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="GeForm[ticket_selling]" value="<?php echo $ticket_selling; ?>" id="table-ticket_selling" />
					</div>


					<div class="form-field form-field-small">
						<label for="table-opening_hour"><?php _e( 'Opening hour', 'GeneratorEvents' ); ?>:</label>
						<input type="time" name="GeForm[opening_hour]" value="<?php echo $opening_hour; ?>" id="table-opening_hour" title="<?php esc_attr_e( 'Opening hour', 'GeneratorEvents' ); ?>"/>
						<p><?php _e( 'Time to begin the event.', 'GeneratorEvents' ); ?></p>
					</div>
					<div class="form-field form-field-small">
						<label for="table-closed_hour"><?php _e( 'Closed hour', 'GeneratorEvents' ); ?>:</label>
						<input type="time" name="GeForm[closed_hour]" value="<?php echo $closed_hour; ?>" id="table-closed_hour" title="<?php esc_attr_e( 'CLosed hour.', 'GeneratorEvents' ); ?>" />
						<p><?php _e( 'Time to finish the event.', 'GeneratorEvents' ); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<?php submit_button(); ?>
			</form>
		</div>

		<?php
	}

	function delete(){
		return $this->db->deleteEvent($_GET['id_event']);
	}

	function processData(){

		if (isset($_POST['savedata']) && !empty($_POST['savedata'])){
			$savedata=$_POST['savedata'];
			switch ($savedata) {
	 			case 'insert':
	 				return $this->db->addEvent();
	 			break;
	 			case 'update':
	 				return $this->db->editEvent();
	 			break;
	 		}
		}
		return false;
	}

	function doAction($action){
		$this->processData();
 		switch ($action) {
 			case 'list':
 				$this->show();
 			break;
 			case 'add':
 				$this->showForm();
 			break;
 			case 'edit':
 				$id=$_GET['id_event'];
 				$this->showForm($id);
 			break;
 			case 'delete':
 				$this->delete();
 				$this->show();
 			break;
 		}
	}


}
