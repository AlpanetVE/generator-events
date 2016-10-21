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
class GeneratorSiteList extends WP_List_Table {

	private $db;

    public function __construct(){

    	$this->load_dependencies();

    	$this->db = GeneratorEvents::get_instance();
    	
    	global $status, $page;

		parent::__construct( array(
			'singular'  => 'table',
			'plural'    => 'tables',
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
			'latitude'	=> __('Latitude', 'wptg-plugin'),
			'longitude'	=> __('Longitude', 'wptg-plugin')
		);
		return $columns;
	}

    function column_default($item, $column_name){
        return stripslashes($item[$column_name]);
    }

	function column_name($item){
		//Build row actions
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&table=%s">%s</a>', $_REQUEST['page'],'edit',$item['id'], __('Edit', 'wptg-plugin') )
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

		$total_items            = $this->db->getCountSites();
		$data                   = $this->db->get_page_items($curr_page, $per_page);

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
    	echo sprintf( '<h2>%s <a class="add-new-h2" href="%s">%s</a></h2>', __('Site', 'wptg-plugin'), admin_url('admin.php?page=GeneratorSites&action=add'), __('Add New', 'wptg-plugin') );
        echo sprintf('<form method="GET"><input type="hidden" name="page" value="'.$_GET['page'].'">');
	    $this->prepare_items();
		$this->display();
	    echo sprintf('</form>');
    	echo sprintf('</div>');
	}

	

	function showAdd(){
		?>
		<div class="wrap">
		<h1>Add Site Event</h1>
			<form method="post" > 
				<div class="form-wrap">
					<div class="form-field">
						<label for="table-name"><?php _e( 'Site Name', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="table[name]" id="table-name" class="placeholder placeholder-active"  placeholder="<?php esc_attr_e( 'Enter Site Name here', 'GeneratorEvents' ); ?>" />
					</div>
					<div class="form-field">
						<label for="site-addres"><?php _e( 'Addres', 'GeneratorEvents' ); ?>:</label>
						<textarea name="table[addres]" id="site-addres" class="placeholder placeholder-active" rows="4" placeholder="<?php echo esc_textarea( __( 'Enter Addres here', 'GeneratorEvents' ) ); ?>"></textarea>
						<p><?php _e( 'Enter the address of the site.', 'GeneratorEvents' ); ?></p>
					</div>


					<div class="form-field">
						<label for="table-latitude"><?php _e( 'Site latitude', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="table[latitude]" id="table-latitude" class="placeholder placeholder-active"  placeholder="<?php esc_attr_e( 'Enter Site latitude here', 'GeneratorEvents' ); ?>" />
					</div>
					<div class="form-field">
						<label for="table-longitude"><?php _e( 'Site longitude', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="table[longitude]" id="table-longitude" class="placeholder placeholder-active"  placeholder="<?php esc_attr_e( 'Enter Site longitude here', 'GeneratorEvents' ); ?>" />
					</div>

					<div class="form-field">
						<label for="table-environment"><?php _e( 'Site environment', 'GeneratorEvents' ); ?>:</label>
						<input type="text" name="table[environment]" id="table-environment" class="placeholder placeholder-active"  placeholder="<?php esc_attr_e( 'Enter Site environment here', 'GeneratorEvents' ); ?>" />
					</div>

					<div class="form-field form-field-small">
						<label for="table-opening_hour"><?php _e( 'Opening hour', 'GeneratorEvents' ); ?>:</label>
						<input type="time" name="table[opening_hour]" id="table-opening_hour" title="<?php esc_attr_e( 'Opening hour', 'GeneratorEvents' ); ?>"/>
						<p><?php _e( 'Time to open the site.', 'GeneratorEvents' ); ?></p>
					</div>
					<div class="form-field form-field-small">
						<label for="table-closed_hour"><?php _e( 'Closed hour', 'GeneratorEvents' ); ?>:</label>
						<input type="time" name="table[closed_hour]" id="table-closed_hour" title="<?php esc_attr_e( 'CLosed hour.', 'GeneratorEvents' ); ?>" />
						<p><?php _e( 'Time to close the site.', 'GeneratorEvents' ); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	function addAction(){
		var_dump($_REQUEST['submit']);
		if (isset($_REQUEST['submit']) && !empty($_REQUEST['submit']))
			$action='AddAndList';
		else
			$action='addForm';
		
		switch ($action) {
 			case 'AddAndList':
 				$this->add();
 				$this->show();
 			break;
 			case 'addForm':
 				$this->showAdd();
 			break;
 		}
	}
	function add(){
		return true;
	}
	function delete(){
		return $this->db->deleteSites($_GET['table']);
	}

	function do_action($action){

 		switch ($action) {
 			case 'list':
 				$this->show();
 			break;
 			case 'add':
 				$this->addAction();
 			break;
 			case 'edit':
 				
 			break;
 			case 'delete':
 				$this->delete();
 				$this->show();
 			break;
 		}
	}


}