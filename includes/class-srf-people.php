<?php
/**
 * Landpack Post Type
 *
 * @since 2021-09-21
 * @package Landpack
 */

namespace SRF_People;

class SRF_People {
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

		add_filter( 'post_type_link', [ $this, 'modify_permalinks' ], 10, 2 );
 		add_filter( 'srf-people_rewrite_rules', [ $this, 'modify_rewrite_rules' ] );
	}

	/**
	 * Registers SRF People post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		$labels = array(
			'name'                  => 'SRF People',
			'singular_name'         => 'SRF Person',

			'name_admin_bar'        => 'SRF Person',
			'menu_name'             => 'SRF People',

			'all_items'             => 'All SRF People',
			'add_new'               => 'Add SRF Person',
			'add_new_item'          => 'Add New SRF Person',
			'new_item'              => 'New SRF Person',
			'edit_item'             => 'Edit SRF Person',
			'view_item'             => 'View SRF Person',

			'search_items'          => 'Search SRF People',
			'not_found'             => 'No SRF People Found',
			'not_found_in_trash'    => 'No SRF People Found in Trash',

			// 'items_list'            => 'SRF People List',
			// 'items_list_navigation' => 'SRF People List Navigation',

			// 'archives'              => 'SRF People Archives',
			// 'filter_items_list'     => 'Filter SRF People List',
			'parent_item_colon'     => 'Parent SRF Person:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF People',
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 9, // After something.
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'  => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array(
				'with_front' => false,
				// 'slug'       => 'people/%srf-people-category%',
				'slug'       => '%srf-people-category%',
			),
			'taxonomies'          => array(
				'srf-people-category',
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
		register_post_type( 'srf-people', $args );
	}

	/**
	 * Registers SRF People custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		$labels = array(
			'name'                       => 'SRF People Categories',
			'singular_name'              => 'SRF People Category',

			'name_admin_bar'             => 'SRF People Category',
			'menu_name'                  => 'Person Categories',

			'all_items'                  => 'All Categories',
			'add_new_item'               => 'Add New Category',
			'new_item_name'              => 'New Category Name',
			'add_or_remove_items'        => 'Add or Remove Categories',
			'view_item'                  => 'View Category',
			'edit_item'                  => 'Edit Category',
			'update_item'                => 'Update Category',

			'search_items'               => 'Search Categories',
			'not_found'                  => 'No Categories Found',
			'no_terms'                   => 'No Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Categories',
			'separate_items_with_commas' => 'Separate Categories w/ Commas',

			'items_list'                 => 'Categories List',
			'items_list_navigation'      => 'Categories List Navigation',

			'archives'                   => 'All Categories',
			'popular_items'              => 'Popular Categories',
			'parent_item'                => 'Parent Category',
			'parent_item_colon'          => 'Parent Category:',

		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'SRF Person Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'sort'              => true,
			'capabilities' => array(
				'manage_terms'  =>   'manage_srf-people-category',
				'edit_terms'    =>   'edit_srf-people-category',
				'delete_terms'  =>   'delete_srf-people-category',
				'assign_terms'  =>   'assign_srf-people-category',
			),
			'default_term'      => array(
				'name' => 'Uncategorized',
				'slug' => 'uncategorized'
			),
		);
		register_taxonomy(
			'srf-people-category',
			'srf-people',
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

		if ( $post instanceof \WP_Post && 'srf-people' === $post->post_type ) {
			$cats = get_the_terms( $post->ID, 'srf-people-category' );

			if ( $cats && is_array( $cats ) ) {
				$cat_slug = current( $cats )->slug;
				$link     = str_replace( '%srf-people-category%', $cat_slug, $link );
			} else {
				$link = str_replace( '%srf-people-category%', 'uncategorized', $link );
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
			$modified_rules[ preg_replace( '/^people\//u', 'people\\/(?!(?:srf-people-category)\\/)', $_key ) ] = $_value;
		}

		return $modified_rules;
	}
}
SRF_People::get_instance()->init();