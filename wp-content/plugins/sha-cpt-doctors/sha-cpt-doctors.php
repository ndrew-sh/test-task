<?php
/*
 * Plugin Name:       CPT Doctors
 * Description:       Custom Post Types for Doctors
 * Version:           0.1.0
 * Author:            Andrew Sh
 * Text Domain:       cpt-doctors
 * Domain Path:       /languages
 */

if ( !defined( 'ABSPATH' ) )  {
  exit;
}

class SHA_CPT_Doctors {

	private static $_instance;

	protected $_plugin_version = '0.1.0';

	protected $_plugin_slug = 'cpt_dctrs';

	protected $_prefix = 'cpt_dctrs_';

	protected $_cpt_slug = 'doctors';


    // Instance of this class
    public static function get_instance() {

        if ( !isset( self::$_instance ) ) {
            self::$_instance = new SHA_CPT_Doctors;
            self::$_instance->init();
        }

        return self::$_instance;
    }

	// Base initing function
	public function init() {

		$this->init_admin_hooks();
		$this->init_public_hooks();
	}

	// Initing all admin actions and filters
	private function init_admin_hooks() {

        $cpt_slug = $this->_cpt_slug;

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'admin_init', array( $this, 'add_meta_fields' ) );
        add_action( 'save_post_' . $cpt_slug, array( $this, 'on_save_post' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		add_action( 'restrict_manage_posts', array( $this, 'admin_grid_filters' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_filter( 'parse_query', array( $this, 'filter_admin_grid' ) );

        add_action( 'manage_' . $cpt_slug . '_posts_custom_column', array( $this, 'columns_content_in_admin_grid' ), 10, 4 );
        add_filter( 'manage_' . $cpt_slug . '_posts_columns', array( $this, 'add_colums_to_admin_grid' ) );
	}

	// Initing all public actions and filters
	private function init_public_hooks() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );
		add_action( 'pre_get_posts', array( $this, 'sort_and_filter_doctors') );
	}

	// Register custom post type and taxonomy
	public function register_cpt() {

        $labels = array(
            'name'          => __( 'Доктора', 'cpt-doctors' ),
            'add_new'       => __( 'Добавить нового', 'cpt-doctors' ),
            'add_new_item'  => __( 'Добавить нового', 'cpt-doctors' ),
            'all_items'     => __( 'Все доктора', 'cpt-doctors' ),
            'edit_item'     => __( 'Редактировать доктора', 'cpt-doctors' ),
            'new_item'      => __( 'Новый доктор', 'cpt-doctors' ),
            'view_item'     => __( 'Просмотреть доктора', 'cpt-doctors' ),
            'search_items'  => __( 'Искать доктора', 'cpt-doctors' ),
            'not_found'     => __( 'Ни одного доктора не найдено', 'cpt-doctors' ),
        );

        // For detailed translation
        $labels = apply_filters( 'sha_dctrs_cpt_labels', $labels );

        register_post_type( $this->_cpt_slug,
			array(
                'labels'				=> $labels,
				'public'				=> true,
				'has_archive'			=> true,
				'rewrite'				=> array( 'slug' => 'doctors' ),
				'supports'				=> array(
					'title',
					'excerpt',
					'thumbnail',
					'editor'				
				),
				'taxonomies'			=> array(
					'specialization',
					'city'
				),
			)
		);

		// Registering Specialization
		if ( ! taxonomy_exists( 'specialization' ) ) {
            $spec_tax_labels = array(
                'name'              => __( 'Специализация', 'cpt-doctors' ),
                'singular_name'     => __( 'Специализация', 'cpt-doctors' ),
                'search_items'      => __( 'Искать специализацию', 'cpt-doctors' ),
                'all_items'         => __( 'Все специализации', 'cpt-doctors' ),
                'edit_item'         => __( 'Редактировать специализацию', 'cpt-doctors' ),
                'view_item'         => __( 'Просмотреть специализацию', 'cpt-doctors' ),
                'add_new_item'      => __( 'Добавить новую', 'cpt-doctors' ),
                'new_item_name'     => __( 'Новая специализация', 'cpt-doctors' ),
                'not_found'         => __( 'Ни одной специализации не найдено', 'cpt-doctors' ),
            );

            // For detailed translation
            $spec_tax_labels = apply_filters( 'sha_spec_tax_labels', $spec_tax_labels );

			register_taxonomy(
				'specialization',
				$this->_cpt_slug,
				array(
					'hierarchical'			=> true,
					'labels'				=> $spec_tax_labels,
					'publicly_queryable'	=> false,
					'show_in_quick_edit'	=> true,
					'show_admin_column'		=> true,
				)
			);
		}

		// Registering Cities
		if ( ! taxonomy_exists( 'cities' ) ) {
            $cities_tax_labels = array(
                'name'              => __( 'Города', 'cpt-doctors' ),
                'singular_name'     => __( 'Город', 'cpt-doctors' ),
                'search_items'      => __( 'Искать город', 'cpt-doctors' ),
                'all_items'         => __( 'Все города', 'cpt-doctors' ),
                'edit_item'         => __( 'Редактировать город', 'cpt-doctors' ),
                'view_item'         => __( 'Просмотреть город', 'cpt-doctors' ),
                'add_new_item'      => __( 'Добавить новый', 'cpt-doctors' ),
                'new_item_name'     => __( 'Новый город', 'cpt-doctors' ),
                'not_found'         => __( 'Ни одного города не найдено', 'cpt-doctors' ),
            );

            // For detailed translation
            $cities_tax_labels = apply_filters( 'sha_cities_tax_labels', $cities_tax_labels );

			register_taxonomy(
				'cities',
				$this->_cpt_slug,
				array(
					'hierarchical'			=> false,
					'labels'				=> $cities_tax_labels,
					'publicly_queryable'	=> false,
					'show_in_quick_edit'	=> true,
					'show_admin_column'		=> true,
				)
			);
		}
	}

	// Load textdomain
	public function load_textdomain() {

		load_plugin_textdomain( 'cpt-doctors', false, basename( dirname( __FILE__, 1 ) ) . '/languages' );
	}

    // Enqueue admin styles and scripts
    public function enqueue_admin_styles( $hook ) {

        $plugin_slug = $this->_plugin_slug;
        $allowed_hooks = array(
            'post-new.php',
            'edit.php'
        );

        if ( ! in_array( $hook, $allowed_hooks, true ) && isset( $_GET['post_type'] ) && ( $_GET['post_type'] !== $plugin_slug ) ) {
            return;
        }

		wp_enqueue_style( $plugin_slug . '-admin', plugin_dir_url( __FILE__ ) . 'admin/css/styles.css', '', $this->_plugin_version );
    }

	// Enqueue fancybox css/js
    public function enqueue_styles_and_scripts() {

    	// Include only on doctors archive/single post
    	if ( ! is_post_type_archive( $this->_cpt_slug ) && ! is_singular( $this->_cpt_slug ) ) {
    		return;
    	}

        $timestamp = ( WP_DEBUG ) ? time() : $this->_plugin_version;

        wp_enqueue_style(
            $this->_plugin_slug,
            plugin_dir_url( __FILE__ ) . 'frontend/css/styles.css',
            array(),
            $timestamp,
            'all'
        );
	}



	// Show experience, price and rating metaboxes on cpt edit page
    public function add_meta_fields() {

        add_meta_box(
            'sha-dctrs-metabox',
            __( 'Дополнительные данные доктора', 'cpt-doctors' ),
            array( $this, 'doctor_metabox_html' ),
            $this->_cpt_slug,
            'normal',
            'high'
        );
    }

    // Add doctors meta fields (experience, price, rating)
    public function doctor_metabox_html( $item_data ) {

        $prefix = $this->_prefix;
        $experience = get_post_meta( $item_data->ID, $prefix . 'experience', true );
        $price = get_post_meta( $item_data->ID, $prefix . 'price', true );
        $rating = get_post_meta( $item_data->ID, $prefix . 'rating', true );

        $experience = ! empty( $experience ) ? absint( $experience ) : '';
        $price = ! empty( $price ) ? absint( $price ) : '';
        $rating = ! empty( $rating ) ? absint( $rating ) : '';

        wp_nonce_field( 'dctrs_save_metabox', 'dctrs_nonce' );

        echo $this->get_module_template(
            'admin/templates/metabox.phtml',
            array(
                'prefix'        => $prefix,
				'experience'	=> $experience,
				'price'         => $price,
				'rating'		=> $rating
			)
        );
    }

    // Save/update post handler
    public function on_save_post( $post_id, $post_data ) {

        // Nonce check
        if ( ! isset( $_POST['dctrs_nonce'] ) ||
             ! wp_verify_nonce( $_POST['dctrs_nonce'], 'dctrs_save_metabox' ) ) {
            return;
        }

        // Users rights check
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Skipping on autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $prefix = $this->_prefix;

		// Update experience
		$experience = isset( $_POST[ $this->_prefix . 'experience' ] ) ? absint( $_POST[ $prefix . 'experience' ] ) : 0;
		update_post_meta( $post_id, $prefix . 'experience', $experience );

		// Update price
		$price = isset( $_POST[ $this->_prefix . 'price' ] ) ? absint( $_POST[ $prefix . 'price' ] ) : 0;
		update_post_meta( $post_id, $prefix . 'price', $price );

		// Update rating
		$rating = isset( $_POST[ $this->_prefix . 'rating' ] ) ? absint( $_POST[ $prefix . 'rating' ] ) : 0;
		update_post_meta( $post_id, $prefix . 'rating', $rating );

	}

    // Add doctor experience columnt to admin grid
    public function add_colums_to_admin_grid( $defaults ) {

        $prefix = $this->_prefix;
        unset( $defaults['date'] );

        $defaults['title'] = __( 'Доктор', 'cpt-doctors' );
        $defaults[ $prefix . 'rating' ] = __( 'Рейтинг', 'cpt-doctors' );

        return $defaults;
    }

    // Add rating column to admin grid 
    public function columns_content_in_admin_grid( $column_name, $post_ID ) {
            
        $prefix = $this->_prefix;

        // Show doctor's rating value
        if ( $column_name == $prefix . 'rating' ) {
            $rating = get_post_meta( $post_ID , $prefix . 'rating', true );
            $rating = absint( $rating );

            if ( $rating > 0 ) {
                echo str_repeat( '<span class="dashicons dashicons-star-filled"></span>', $rating );
            } else {
                echo '&mdash;';
            }
        }
        
    }

	// Add city to query var
	public function add_query_vars( $vars ) {

		$vars[] = 'city';

	    return $vars;
	}

    // Add city filter to admin grid
    public function admin_grid_filters( $post_type ) {

    	if ( $post_type != $this->_cpt_slug ) {
    		return;
    	}

		$selected = isset( $_GET['city'] ) ? absint( $_GET['city'] ) : '';

    	wp_dropdown_categories(
    		array(
				'show_option_all'	=> __( 'Показать все города', 'cpt-doctors' ),
				'taxonomy'			=> 'cities',
				'name'				=> 'city',
				'orderby'			=> 'name',
				'selected'			=> $selected,
				'hide_if_empty'		=> true,
				'value_field'		=> 'ID'
         	)
    	);
    }

    // Filter admin grid by city
    public function filter_admin_grid( $query ) {

		global $pagenow, $typenow;

        // Ensure we are on the correct admin page and post type
        if ( $pagenow == 'edit.php' && $typenow == $this->_cpt_slug && is_admin() ) {
        	if ( ! empty( $query->query_vars['city'] ) ) {
				$tax_query = array(
				    array(
				        'taxonomy' => 'cities',
				        'field'    => 'ID',
				        'terms'    => absint( $query->query_vars['city'] ),
				    )
				);

        		$query->set( 'tax_query', $tax_query );
    		}
        }
    }

    // Sort and filter doctors query on frontend
    public function sort_and_filter_doctors( $query ) {

    	if ( is_admin() || ! $query->is_main_query() || ! is_post_type_archive( $this->_cpt_slug ) ) {
	        return;
	    }

	    // Set posts_per_page
	    $doctors_per_page = apply_filters( 'sha_doctors_per_page', 9 );
	    $doctors_per_page = absint( $doctors_per_page );

	    $query->set( 'posts_per_page', $doctors_per_page );

	    // Tax filters
	    $tax_query = array();

	    if ( ! empty( $_GET['specialization'] ) ) {
	        $tax_query[] = array(
	            'taxonomy'			=> 'specialization',
	            'terms'				=> absint( $_GET['specialization'] ),
	            'include_children'	=> false
	        );
	    }

	    if ( ! empty( $_GET['city'] ) ) {
	        $tax_query[] = array(
	            'taxonomy'			=> 'cities',
	            'terms'				=> absint( $_GET['city'] ),
	            'include_children'	=> false
	        );
	    }

	    if ( $tax_query ) {
	        $query->set( 'tax_query', $tax_query );
	    }

	    // Sorting
	    if ( ! empty( $_GET['sort'] ) ) {

	    	list( $sort_by, $direction ) = explode( '_', $_GET['sort'] );

	    	// Allow only asc/desc values
	    	$direction = ( strtolower( $direction ) == 'asc' ) ? 'asc' : 'desc';

	        switch ( $sort_by ) {
	            case 'rating':
	                $query->set( 'meta_key', $this->_prefix . 'rating' );
	                $query->set( 'orderby', 'meta_value_num' );
	                $query->set( 'order', $direction );
        	        break;

	            case 'price':
	                $query->set( 'meta_key', $this->_prefix . 'price' );
	                $query->set( 'orderby', 'meta_value_num' );
	                $query->set( 'order', $direction );
	                break;

	            case 'exp':
	                $query->set( 'meta_key', $this->_prefix . 'experience' );
	                $query->set( 'orderby', 'meta_value_num' );
	                $query->set( 'order', $direction );
	                break;
	        }
	    }

    }

	// Get template and output it's html
    private function get_module_template( $template, $args = array(), $global = false ) {

        $template = ltrim( $template, '/' );

        if ( $global ) {
            $template_file = $template;
        } else {
            $template_file = sprintf(
                '%s%s',
                plugin_dir_path( __FILE__ ),
                $template
            );
        }

        if ( ! is_readable( $template_file ) ) {
            return null;
        }

        $args['module_slug'] = $this->_plugin_slug;
        extract( $args, EXTR_SKIP | EXTR_REFS );

        ob_start();
        require( $template_file );
        return ob_get_clean();
    }
}

// Init module instance
function init_cpt_doctors_plugin() {

    return SHA_CPT_Doctors::get_instance();
}

add_action( 'plugins_loaded', 'init_cpt_doctors_plugin', 100 );
