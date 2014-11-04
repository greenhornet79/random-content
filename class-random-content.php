<?php


class Endo_Random_Content {

	/**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $name    The ID of this plugin.
     */
    private $name;

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string    $version    The version of the plugin
     */
    private $version;

	/**
	* Initializes the plugin by defining the properties.
	*
	* @since 0.1.0
	*/
	public function __construct() {

		$this->name = 'random-content';
		$this->version = '1.0';

	}

	/**
	* Defines the hooks that will register the post type and taxonomy, adds the shortcode, and registers the widget
	*
	* @since 1.0
	*/
	public function run() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_shortcode( 'random', array( &$this, 'shortcode') );

		add_action( 'widgets_init' , array( $this, 'register_endo_wrc_widget' ) );

	}

	/**
	* Calls the widget class that extends WP_Widget
	*
	* @since 1.0
	*/
	public function register_endo_wrc_widget() {

		require_once( plugin_dir_path( __FILE__ ) . 'class-random-content-widget.php' );

		register_widget('Endo_WRC_Widget');
		
	} 


	/**
	* Registers the random content post type
	*
	* @since 0.1.0
	*/
	public function register_post_type() {

		$args = array(
			'labels' => array(
				'name' => 'Random Content',
				'singular_name' => 'Random Content',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Random Content',
				'edit_item' => 'Edit Random Content',
				'new_item' => 'Add New Random Content',
				'view_item' => 'View Random Content',
				'search_items' => 'Search',
				'not_found' => 'No Random Content Found',
				'not_found_in_trash' => 'No Random Content Found in Trash'
			),
			'show_ui' => true,
			'supports' => array(
				'title',
				'editor'
			)
		);
		register_post_type( 'endo_wrc_cpt', $args );

	}

	/**
	* Registers the group taxonomy for the random content post type
	*
	* @since 0.1.0
	*/
	public function register_taxonomy() {

		register_taxonomy(
			'endo_wrc_group',
			'endo_wrc_cpt',
			array(
				'label' => __( 'Group' ),
				'hierarchical' => true
			)
		);
	}

	/**
	* Defines random content shortcode
	*
	* @since 0.3.0
	*/
	public function shortcode( $atts ) {
		extract( shortcode_atts( array(
			'group_id' => ''
			), $atts ));

		// if $group_id is set, then filter results by $group_id
		if ( $group_id ) {
			$my_query = new WP_Query( array( 
				'post_type' => 'endo_wrc_cpt', 
				'posts_per_page' => 1, 
				'orderby' => 'rand', 
				'tax_query' => array(
					array(
						'taxonomy' => 'endo_wrc_group',
						'field' => 'id',
						'terms' => $group_id
					)
				)
			) );

		}
		else {
			// filter through all entries
			$my_query = new WP_Query( array( 
				'post_type' => 'endo_wrc_cpt', 
				'posts_per_page' => 1, 
				'orderby' => 'rand'
			) );
		}

		if ( $my_query->have_posts() ) {

			while ( $my_query->have_posts() ) : $my_query->the_post();
				
				$content = apply_filters('the_content', get_the_content() );
					
			endwhile;

		} else {
			$content = 'No posts found.';
		}

		wp_reset_postdata();

		return $content;
		
	}

} // end class