<?php
	



	/**
	 * Add admin screens to the correct place in the admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu_entry() {
		
		// Callback for all menu entries.
		$callback = array( $this, 'show_admin_page' );
		/**
		 * Filter the TablePress admin menu entry name.
		 *
		 * @since 1.0.0
		 *
		 * @param string $entry_name The admin menu entry name. Default "TablePress".
		 */
		$admin_menu_entry_name = apply_filters( 'tablepress_admin_menu_entry_name', 'TablePress' );

		if ( $this->is_top_level_page ) {
			// Init i18n support here as translated strings for admin menu are needed already.
			$this->init_i18n_support();
			$this->init_view_actions(); // after init_i18n_support(), as it requires translation
			$min_access_cap = $this->view_actions['list']['required_cap'];

			$icon_url = 'dashicons-list-view';
			switch ( $this->parent_page ) {
				case 'top':
					$position = 3; // position of Dashboard + 1
					break;
				case 'bottom':
					$position = ( ++$GLOBALS['_wp_last_utility_menu'] );
					break;
				case 'middle':
				default:
					$position = ( ++$GLOBALS['_wp_last_object_menu'] );
					break;
			}
			add_menu_page( 'TablePress', $admin_menu_entry_name, $min_access_cap, 'tablepress', $callback, $icon_url, $position );
			foreach ( $this->view_actions as $action => $entry ) {
				if ( ! $entry['show_entry'] ) {
					continue;
				}
				$slug = 'tablepress';
				if ( 'list' !== $action ) {
					$slug .= '_' . $action;
				}
				$this->page_hooks[] = add_submenu_page( 'tablepress', sprintf( __( '%1$s &lsaquo; %2$s', 'tablepress' ), $entry['page_title'], 'TablePress' ), $entry['admin_menu_title'], $entry['required_cap'], $slug, $callback );
			}
		} else {
			$this->init_view_actions(); // no translation necessary here
			$min_access_cap = $this->view_actions['list']['required_cap'];
			$this->page_hooks[] = add_submenu_page( $this->parent_page, 'TablePress', $admin_menu_entry_name, $min_access_cap, 'tablepress', $callback );
		}
	}

	/**
	 * Init list of actions that have a view with their titles/names/caps.
	 *
	 * @since 1.0.0
	 */
	protected function init_view_actions() {
		$this->view_actions = array(
			'list' => array(
				'show_entry' => true,
				'page_title' => __( 'All Tables', 'tablepress' ),
				'admin_menu_title' => __( 'All Tables', 'tablepress' ),
				'nav_tab_title' => __( 'All Tables', 'tablepress' ),
				'required_cap' => 'tablepress_list_tables',
			),
			'add' => array(
				'show_entry' => true,
				'page_title' => __( 'Add New Table', 'tablepress' ),
				'admin_menu_title' => __( 'Add New Table', 'tablepress' ),
				'nav_tab_title' => __( 'Add New', 'tablepress' ),
				'required_cap' => 'tablepress_add_tables',
			),
			'edit' => array(
				'show_entry' => false,
				'page_title' => __( 'Edit Table', 'tablepress' ),
				'admin_menu_title' => '',
				'nav_tab_title' => '',
				'required_cap' => 'tablepress_edit_tables',
			),
			'import' => array(
				'show_entry' => true,
				'page_title' => __( 'Import a Table', 'tablepress' ),
				'admin_menu_title' => __( 'Import a Table', 'tablepress' ),
				'nav_tab_title' => _x( 'Import', 'navigation bar', 'tablepress' ),
				'required_cap' => 'tablepress_import_tables',
			),
			'export' => array(
				'show_entry' => true,
				'page_title' => __( 'Export a Table', 'tablepress' ),
				'admin_menu_title' => __( 'Export a Table', 'tablepress' ),
				'nav_tab_title' => _x( 'Export', 'navigation bar', 'tablepress' ),
				'required_cap' => 'tablepress_export_tables',
			),
			'options' => array(
				'show_entry' => true,
				'page_title' => __( 'Plugin Options', 'tablepress' ),
				'admin_menu_title' => __( 'Plugin Options', 'tablepress' ),
				'nav_tab_title' => __( 'Plugin Options', 'tablepress' ),
				'required_cap' => 'tablepress_access_options_screen',
			),
			'about' => array(
				'show_entry' => true,
				'page_title' => __( 'About', 'tablepress' ),
				'admin_menu_title' => __( 'About TablePress', 'tablepress' ),
				'nav_tab_title' => __( 'About', 'tablepress' ),
				'required_cap' => 'tablepress_access_about_screen',
			),
		);

		/**
		 * Filter the available TablePres Views/Actions and their parameters.
		 *
		 * @since 1.0.0
		 *
		 * @param array $view_actions The available Views/Actions and their parameters.
		 */
		$this->view_actions = apply_filters( 'tablepress_admin_view_actions', $this->view_actions );
	}

?>