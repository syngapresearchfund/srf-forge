<?php
/**
 * SRF Team Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Team;

class SRF_Team {
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
	}

	/**
	 * Registers SRF Team post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		$labels = array(
			'name'                  => 'SRF Team',
			'singular_name'         => 'SRF Team Member',

			'name_admin_bar'        => 'SRF Team Member',
			'menu_name'             => 'SRF Team',

			'all_items'             => 'All SRF Team',
			'add_new'               => 'Add SRF Team Member',
			'add_new_item'          => 'Add New SRF Team Member',
			'new_item'              => 'New SRF Team Member',
			'edit_item'             => 'Edit SRF Team Member',
			'view_item'             => 'View SRF Team Member',

			'search_items'          => 'Search SRF Team',
			'not_found'             => 'No SRF Team Found',
			'not_found_in_trash'    => 'No SRF Team Found in Trash',

			'parent_item_colon'     => 'Parent SRF Team Member:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Team',
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 20, // After Pages.
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
				'slug'       => 'team/%srf-team-category%',
			),
			'taxonomies'          => array(
				'srf-team-category',
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
		register_post_type( 'srf-team', $args );
	}

	/**
	 * Registers SRF Team custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		$labels = array(
			'name'                       => 'SRF Team Categories',
			'singular_name'              => 'SRF Team Category',

			'name_admin_bar'             => 'SRF Team Category',
			'menu_name'                  => 'Team Categories',

			'all_items'                  => 'All Team Categories',
			'add_new_item'               => 'Add New Team Category',
			'new_item_name'              => 'New Team Category Name',
			'add_or_remove_items'        => 'Add or Remove Team Categories',
			'view_item'                  => 'View Team Category',
			'edit_item'                  => 'Edit Team Category',
			'update_item'                => 'Update Team Category',

			'search_items'               => 'Search Team Categories',
			'not_found'                  => 'No Team Categories Found',
			'no_terms'                   => 'No Team Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Team Categories',
			'separate_items_with_commas' => 'Separate Team Categories w/ Commas',

			'items_list'                 => 'Team Categories List',
			'items_list_navigation'      => 'Team Categories List Navigation',

			'archives'                   => 'All Team Categories',
			'popular_items'              => 'Popular Team Categories',
			'parent_item'                => 'Parent Team Category',
			'parent_item_colon'          => 'Parent Team Category:',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'SRF Team Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'sort'              => true,
		);
		register_taxonomy(
			'srf-team-category',
			'srf-team',
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

		if ( $post instanceof \WP_Post && 'srf-team' === $post->post_type ) {
			$cats = get_the_terms( $post->ID, 'srf-team-category' );

			if ( $cats && is_array( $cats ) ) {
				$cat_slug = current( $cats )->slug;
				$link     = str_replace( '%srf-team-category%', $cat_slug, $link );
			} else {
				$link = str_replace( '%srf-team-category%', 'uncategorized', $link );
			}
		}

		return $link;
	}
}
SRF_Team::get_instance()->init();