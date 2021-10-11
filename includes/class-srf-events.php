<?php
/**
 * SRF Events Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Events;

class SRF_Events {
	/**
	 * Singleton instance.
	 *
	 * @since 2021-09-21
	 *
	 * @var self Instance.
	 */
	private static $instance = null;

	/**
	 * Has been initialized yet?
	 *
	 * @since 2021-09-21
	 *
	 * @var bool Initialized?
	 */
	private $did_init;

	/**
	 * Private constructor.
	 *
	 * @since 2021-09-21
	 */
	private function __construct() {
		$this->did_init = false;
	}

	/**
	 * Create or return instance of this class.
	 *
	 * @since 2021-09-21
	 */
	public static function get_instance() : self {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 2021-09-21
	 */
	public function init() : void {
		if ( ! function_exists( 'register_block_type' ) ) {
			return; // The block editor is not supported.
		} elseif ( $this->did_init ) {
			return; // Already initialized.
		}

		// Flag as initialized.
		$this->did_init = true;

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_filter( 'post_type_link', array( $this, 'modify_permalinks' ), 10, 2 );
 		add_filter( 'srf-events_rewrite_rules', array( $this, 'modify_rewrite_rules' ) );
	}

	/**
	 * Registers SRF Events post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		$labels = array(
			'name'                  => 'SRF Events',
			'singular_name'         => 'SRF Event',

			'name_admin_bar'        => 'SRF Event',
			'menu_name'             => 'SRF Events',

			'all_items'             => 'All SRF Events',
			'add_new'               => 'Add SRF Event',
			'add_new_item'          => 'Add New SRF Event',
			'new_item'              => 'New SRF Event',
			'edit_item'             => 'Edit SRF Event',
			'view_item'             => 'View SRF Event',

			'search_items'          => 'Search SRF Events',
			'not_found'             => 'No SRF Events Found',
			'not_found_in_trash'    => 'No SRF Events Found in Trash',

			// 'items_list'            => 'SRF Events List',
			// 'items_list_navigation' => 'SRF Events List Navigation',

			// 'archives'              => 'SRF Events Archives',
			// 'filter_items_list'     => 'Filter SRF Events List',
			'parent_item_colon'     => 'Parent SRF Event:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Events',
			'menu_icon'           => 'dashicons-tickets',
			'menu_position'       => 21, // After People.
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array(
				'with_front' => false,
				// 'slug'       => 'events',
				// 'slug'       => 'events/%srf-events-category%',
				'slug'       => '%srf-events-category%',
			),
			'taxonomies'          => array(
				'srf-events-category',
			),
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'editor',
				'excerpt',
				'author',
				'thumbnail',
				'custom-fields',
				'revisions',
			),
		);
		register_post_type( 'srf-events', $args );
	}

	/**
	 * Registers SRF Events custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		$labels = array(
			'name'                       => 'SRF Event Categories',
			'singular_name'              => 'SRF Event Category',

			'name_admin_bar'             => 'SRF Events Category',
			'menu_name'                  => 'Event Categories',

			'all_items'                  => 'All Event Categories',
			'add_new_item'               => 'Add New Event Category',
			'new_item_name'              => 'New Event Category Name',
			'add_or_remove_items'        => 'Add or Remove Event Categories',
			'view_item'                  => 'View Event Category',
			'edit_item'                  => 'Edit Event Category',
			'update_item'                => 'Update Event Category',

			'search_items'               => 'Search Event Categories',
			'not_found'                  => 'No Event Categories Found',
			'no_terms'                   => 'No Event Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Event Categories',
			'separate_items_with_commas' => 'Separate Event Categories w/ Commas',

			'items_list'                 => 'Event Categories List',
			'items_list_navigation'      => 'Event Categories List Navigation',

			'archives'                   => 'All Event Categories',
			'popular_items'              => 'Popular Event Categories',
			'parent_item'                => 'Parent Event Category',
			'parent_item_colon'          => 'Parent Event Category:',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'SRF Event Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'sort'              => true,
			'capabilities' => array(
				'manage_terms'  =>   'manage_srf-events-category',
				'edit_terms'    =>   'edit_srf-events-category',
				'delete_terms'  =>   'delete_srf-events-category',
				'assign_terms'  =>   'assign_srf-events-category',
			),
			'default_term'      => array(
				'name' => 'Uncategorized',
				'slug' => 'uncategorized'
			),
		);
		register_taxonomy(
			'srf-events-category',
			'srf-events',
			$args
		);
	}

	/**
	 * Modifies permalinks.
	 *
	 * @since 2018-08-22
	 *
	 * @param  string   $link Link.
	 * @param  \WP_Post $post Post object.
	 *
	 * @return string         Modified link.
	 */
	public function modify_permalinks( $link, $post ) : string {
		$link = (string) $link;

		if ( $post instanceof \WP_Post && 'srf-events' === $post->post_type ) {
			$cats = get_the_terms( $post->ID, 'srf-events-category' );

			if ( $cats && is_array( $cats ) ) {
				$cat_slug = current( $cats )->slug;
				$link     = str_replace( '%srf-events-category%', $cat_slug, $link );
			} else {
				$link = str_replace( '%srf-events-category%', 'uncategorized', $link );
			}
		}

		return $link;
	}

	/**
	 * Modifies rewrite rules.
	 *
	 * @since 2018-08-22
	 *
	 * @param  array $rules Rewrite rules.
	 *
	 * @return array        Modified rewrite rules.
	 */
	public function modify_rewrite_rules( $rules ) : array {
		$modified_rules = []; // Initialize.
		$rules          = is_array( $rules ) ? $rules : [];

		foreach ( $rules as $_key => $_value ) {
			$modified_rules[ preg_replace( '/^events\//u', 'events\\/(?!(?:srf-events-category)\\/)', $_key ) ] = $_value;
		}

		return $modified_rules;
	}
}
SRF_Events::get_instance()->init();