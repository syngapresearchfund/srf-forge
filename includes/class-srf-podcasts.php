<?php
/**
 * SRF Podcasts Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Podcasts;

class SRF_Podcasts {
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
			self::$instance = new self();
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
	 * Registers SRF Podcasts post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		$labels = array(
			'name'               => 'SRF Podcasts',
			'singular_name'      => 'SRF Podcast',

			'name_admin_bar'     => 'SRF Podcast',
			'menu_name'          => 'SRF Podcasts',

			'all_items'          => 'All SRF Podcasts',
			'add_new'            => 'Add SRF Podcast',
			'add_new_item'       => 'Add New SRF Podcast',
			'new_item'           => 'New SRF Podcast',
			'edit_item'          => 'Edit SRF Podcast',
			'view_item'          => 'View SRF Podcast',

			'search_items'       => 'Search SRF Podcasts',
			'not_found'          => 'No SRF Podcasts Found',
			'not_found_in_trash' => 'No SRF Podcasts Found in Trash',

			'parent_item_colon'  => 'Parent SRF Podcast:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Podcasts',
			'menu_icon'           => 'dashicons-microphone',
			'menu_position'       => 24, // After Events.
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
				'slug'       => 'podcasts/%srf-podcasts-category%',
			),
			'taxonomies'          => array(
				'srf-podcasts-category',
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
				'page-attributes',
			),
		);
		register_post_type( 'srf-podcasts', $args );
	}

	/**
	 * Registers SRF Podcasts custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		$labels = array(
			'name'                       => 'SRF Podcast Categories',
			'singular_name'              => 'SRF Podcast Category',

			'name_admin_bar'             => 'SRF Podcasts Category',
			'menu_name'                  => 'Podcast Categories',

			'all_items'                  => 'All Podcast Categories',
			'add_new_item'               => 'Add New Podcast Category',
			'new_item_name'              => 'New Podcast Category Name',
			'add_or_remove_items'        => 'Add or Remove Podcast Categories',
			'view_item'                  => 'View Podcast Category',
			'edit_item'                  => 'Edit Podcast Category',
			'update_item'                => 'Update Podcast Category',

			'search_items'               => 'Search Podcast Categories',
			'not_found'                  => 'No Podcast Categories Found',
			'no_terms'                   => 'No Podcast Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Podcast Categories',
			'separate_items_with_commas' => 'Separate Podcast Categories w/ Commas',

			'items_list'                 => 'Podcast Categories List',
			'items_list_navigation'      => 'Podcast Categories List Navigation',

			'archives'                   => 'All Podcast Categories',
			'popular_items'              => 'Popular Podcast Categories',
			'parent_item'                => 'Parent Podcast Category',
			'parent_item_colon'          => 'Parent Podcast Category:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF Podcast Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'sort'              => true,
		);
		register_taxonomy(
			'srf-podcasts-category',
			'srf-podcasts',
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

		if ( $post instanceof \WP_Post && 'srf-podcasts' === $post->post_type ) {
			$cats = get_the_terms( $post->ID, 'srf-podcasts-category' );

			if ( $cats && is_array( $cats ) ) {
				$cat_slug = current( $cats )->slug;
				$link     = str_replace( '%srf-podcasts-category%', $cat_slug, $link );
			} else {
				$link = str_replace( '%srf-podcasts-category%', 'uncategorized', $link );
			}
		}

		return $link;
	}
}
SRF_Podcasts::get_instance()->init();
