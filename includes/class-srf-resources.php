<?php
/**
 * SRF Resources Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Resources;

class SRF_Resources {
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
 		add_filter( 'srf-resources_rewrite_rules', array( $this, 'modify_rewrite_rules' ) );
	}

	/**
	 * Registers SRF Resources post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		$labels = array(
			'name'                  => 'SRF Resources',
			'singular_name'         => 'SRF Resource',

			'name_admin_bar'        => 'SRF Resource',
			'menu_name'             => 'SRF Resources',

			'all_items'             => 'All SRF Resources',
			'add_new'               => 'Add SRF Resource',
			'add_new_item'          => 'Add New SRF Resource',
			'new_item'              => 'New SRF Resource',
			'edit_item'             => 'Edit SRF Resource',
			'view_item'             => 'View SRF Resource',

			'search_items'          => 'Search SRF Resources',
			'not_found'             => 'No SRF Resources Found',
			'not_found_in_trash'    => 'No SRF Resources Found in Trash',

			// 'items_list'            => 'SRF Resources List',
			// 'items_list_navigation' => 'SRF Resources List Navigation',

			// 'archives'              => 'SRF Resources Archives',
			// 'filter_items_list'     => 'Filter SRF Resources List',
			'parent_item_colon'     => 'Parent SRF Resource:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Resources',
			'menu_icon'           => 'dashicons-networking',
			'menu_position'       => 22, // After Events.
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
				// 'slug'       => 'resources/%srf-resources-category%',
				'slug'       => '%srf-resources-category%',
			),
			'taxonomies'          => array(
				'srf-resources-category',
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
		register_post_type( 'srf-resources', $args );
	}

	/**
	 * Registers SRF Resources custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		$labels = array(
			'name'                       => 'SRF Resource Categories',
			'singular_name'              => 'SRF Resource Category',

			'name_admin_bar'             => 'SRF Resource Category',
			'menu_name'                  => 'Resource Categories',

			'all_items'                  => 'All Resource Categories',
			'add_new_item'               => 'Add New Resource Category',
			'new_item_name'              => 'New Resource Category Name',
			'add_or_remove_items'        => 'Add or Remove Resource Categories',
			'view_item'                  => 'View Resource Category',
			'edit_item'                  => 'Edit Resource Category',
			'update_item'                => 'Update Resource Category',

			'search_items'               => 'Search Resource Categories',
			'not_found'                  => 'No Resource Categories Found',
			'no_terms'                   => 'No Resource Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Resource Categories',
			'separate_items_with_commas' => 'Separate Resource Categories w/ Commas',

			'items_list'                 => 'Resource Categories List',
			'items_list_navigation'      => 'Resource Categories List Navigation',

			'archives'                   => 'All Resource Categories',
			'popular_items'              => 'Popular Resource Categories',
			'parent_item'                => 'Parent Resource Category',
			'parent_item_colon'          => 'Parent Resource Category:',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'SRF Resources Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'sort'              => true,
		);
		register_taxonomy(
			'srf-resources-category',
			'srf-resources',
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

		if ( $post instanceof \WP_Post && 'srf-resources' === $post->post_type ) {
			$cats = get_the_terms( $post->ID, 'srf-resources-category' );

			if ( $cats && is_array( $cats ) ) {
				$cat_slug = current( $cats )->slug;
				$link     = str_replace( '%srf-resources-category%', $cat_slug, $link );
			} else {
				$link = str_replace( '%srf-resources-category%', 'uncategorized', $link );
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
			$modified_rules[ preg_replace( '/^resources\//u', 'resources\\/(?!(?:srf-resources-category)\\/)', $_key ) ] = $_value;
		}

		return $modified_rules;
	}
}
SRF_Resources::get_instance()->init();