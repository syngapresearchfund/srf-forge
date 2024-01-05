<?php
/**
 * SRF Warriors Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Warriors;

class SRF_Warriors {
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
		add_action( 'generate_rewrite_rules', array( $this, 'custom_rewrite_rules' ) );
	}

	/**
	 * Registers SRF Warriors post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		$labels = array(
			'name'               => 'SRF Warriors',
			'singular_name'      => 'SRF Warrior',

			'name_admin_bar'     => 'SRF Warrior',
			'menu_name'          => 'SRF Warriors',

			'all_items'          => 'All SRF Warriors',
			'add_new'            => 'Add SRF Warrior',
			'add_new_item'       => 'Add New SRF Warrior',
			'new_item'           => 'New SRF Warrior',
			'edit_item'          => 'Edit SRF Warrior',
			'view_item'          => 'View SRF Warrior',

			'search_items'       => 'Search SRF Warriors',
			'not_found'          => 'No SRF Warriors Found',
			'not_found_in_trash' => 'No SRF Warriors Found in Trash',

			'parent_item_colon'  => 'Parent SRF Warrior:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Warriors',
			'menu_icon'           => 'dashicons-heart',
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
				'slug'       => 'syngap-warriors',
			),
			'taxonomies'          => array(
				'srf-warriors-category',
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
		register_post_type( 'srf-warriors', $args );
	}

	/**
	 * Registers SRF Warrior custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		$labels = array(
			'name'                       => 'SRF Warrior Categories',
			'singular_name'              => 'SRF Warrior Category',

			'name_admin_bar'             => 'SRF Warrior Category',
			'menu_name'                  => 'SRF Warrior Categories',

			'all_items'                  => 'All Warrior Categories',
			'add_new_item'               => 'Add New Warrior Category',
			'new_item_name'              => 'New Warrior Category Name',
			'add_or_remove_items'        => 'Add or Remove Warrior Categories',
			'view_item'                  => 'View Warrior Category',
			'edit_item'                  => 'Edit Warrior Category',
			'update_item'                => 'Update Warrior Category',

			'search_items'               => 'Search Warrior Categories',
			'not_found'                  => 'No Warrior Categories Found',
			'no_terms'                   => 'No Warrior Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Warrior Categories',
			'separate_items_with_commas' => 'Separate Warrior Categories w/ Commas',

			'items_list'                 => 'Warrior Categories List',
			'items_list_navigation'      => 'Warrior Categories List Navigation',

			'archives'                   => 'All Warrior Categories',
			'popular_items'              => 'Popular Warrior Categories',
			'parent_item'                => 'Parent Warrior Category',
			'parent_item_colon'          => 'Parent Warrior Category:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF Warrior Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'sort'              => true,
			'rewrite'           => true,
		);
		register_taxonomy(
			'srf-warriors-category',
			'srf-warriors',
			$args
		);
	}

	/**
	 * Modifies permalinks.
	 *
	 * @since 2018-08-22
	 *
	 * @param  string   $permalink Link.
	 * @param  \WP_Post $post Post object.
	 *
	 * @return string         Modified link.
	 */
	public function modify_permalinks( $permalink, $post ) : string {
		if ( 'srf-warriors' === $post->post_type ) {
			$resource_terms = get_the_terms( $post, 'srf-warriors-category' );
			$term_slug      = '';
			if ( ! empty( $resource_terms ) ) {
				foreach ( $resource_terms as $term ) {

					// The featured resource will have another category which is the main one.
					if ( 'featured' === $term->slug ) {
						continue;
					}

					$term_slug = $term->slug . '/';
					break;
				}
			}
			$permalink = get_home_url() . '/syngap-warriors/' . $term_slug . $post->post_name;
		}
		return $permalink;
	}

	/**
	 * Attempts to fix pagination for taxonomy permalinks.
	 *
	 * @since 2018-08-22
	 *
	 * @param  $wp_rewrite Rewrite rules array.
	 *
	 * @return void
	 */
	public function custom_rewrite_rules( $wp_rewrite ) : void {
		$rules = array();
		$terms = get_terms(
			array(
				'taxonomy'   => 'srf-warriors-category',
				'hide_empty' => false,
			)
		);

		$post_type = 'srf-warriors';

		foreach ( $terms as $term ) {

			$rules[ 'syngap-warriors/' . $term->slug . '/([^/]*)$' ] = 'index.php?post_type=' . $post_type . '&srf-warriors=$matches[1]&name=$matches[1]';

			// Allow archive page to display posts.
			$rules[ 'syngap-warriors/(.+)/page/?([0-9]{1,})/?$' ] = 'index.php?taxonomy=srf-warriors-category&term=' . $wp_rewrite->preg_index(1) . '&paged=' . $wp_rewrite->preg_index(2);
			$rules[ 'syngap-warriors/(.+)/?$' ] = 'index.php?taxonomy=srf-warriors-category&term=' . $wp_rewrite->preg_index(1);
		}

		// Merge with global rules.
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}
}
SRF_Warriors::get_instance()->init();
