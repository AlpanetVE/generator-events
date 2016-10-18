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
			'rows'	=> __('Rows', 'wptg-plugin'),
			'cols'	=> __('Columns', 'wptg-plugin')
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
		?>
		<form method="GET">
			<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
			<?php
				$this->prepare_items();
				$this->display();
			?>
		</form>
		<?php
	}

}