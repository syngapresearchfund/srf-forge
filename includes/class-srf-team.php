<?php
/**
 * SRF Team Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Team;

use WP_Post;

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
	public static function get_instance(): self {
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
	public function init(): void {
		if ( $this->did_init ) {
			return; // Already initialized.
		}

		// Flag as initialized.
		$this->did_init = true;

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_team_category_tax' ) );
		add_action( 'init', array( $this, 'register_state_reps_tax' ) );
		add_action( 'init', array( $this, 'register_state_advocates_tax' ) );

		add_filter( 'post_type_link', array( $this, 'modify_permalinks' ), 10, 2 );
		add_action( 'generate_rewrite_rules', array( $this, 'custom_rewrite_rules' ) );
	}

	/**
	 * Registers SRF Team post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'          => 'SRF Team',
			'singular_name' => 'SRF Team Member',

			'name_admin_bar' => 'SRF Team Member',
			'menu_name'      => 'SRF Team',

			'all_items'    => 'All SRF Team',
			'add_new'      => 'Add SRF Team Member',
			'add_new_item' => 'Add New SRF Team Member',
			'new_item'     => 'New SRF Team Member',
			'edit_item'    => 'Edit SRF Team Member',
			'view_item'    => 'View SRF Team Member',

			'search_items'       => 'Search SRF Team',
			'not_found'          => 'No SRF Team Found',
			'not_found_in_trash' => 'No SRF Team Found in Trash',

			'parent_item_colon' => 'Parent SRF Team Member:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Team',
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 21, // After Warriors.
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
			'rewrite'             => true,
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
				'page-attributes',
			),
		);
		register_post_type( 'srf-team', $args );
	}

	/**
	 * Registers SRF Team Category taxonomy.
	 *
	 * @since 2021-19-21
	 */
	public function register_team_category_tax(): void {
		$labels = array(
			'name'          => 'SRF Team Categories',
			'singular_name' => 'SRF Team Category',

			'name_admin_bar' => 'SRF Team Category',
			'menu_name'      => 'Team Categories',

			'all_items'           => 'All Team Categories',
			'add_new_item'        => 'Add New Team Category',
			'new_item_name'       => 'New Team Category Name',
			'add_or_remove_items' => 'Add or Remove Team Categories',
			'view_item'           => 'View Team Category',
			'edit_item'           => 'Edit Team Category',
			'update_item'         => 'Update Team Category',

			'search_items' => 'Search Team Categories',
			'not_found'    => 'No Team Categories Found',
			'no_terms'     => 'No Team Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Team Categories',
			'separate_items_with_commas' => 'Separate Team Categories w/ Commas',

			'items_list'            => 'Team Categories List',
			'items_list_navigation' => 'Team Categories List Navigation',

			'archives'          => 'All Team Categories',
			'popular_items'     => 'Popular Team Categories',
			'parent_item'       => 'Parent Team Category',
			'parent_item_colon' => 'Parent Team Category:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF Team Categories',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'sort'              => true,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => 'team',
			),
		);
		register_taxonomy(
			'srf-team-category',
			'srf-team',
			$args
		);
	}

	/**
	 * Registers State Representatives taxonomy.
	 *
	 * @since 2021-19-21
	 */
	public function register_state_reps_tax(): void {
		$labels = array(
			'name'          => 'SRF State Reps',
			'singular_name' => 'SRF State Rep',

			'name_admin_bar' => 'SRF State Rep',
			'menu_name'      => 'State Reps',

			'all_items'           => 'All State Reps',
			'add_new_item'        => 'Add New State Rep',
			'new_item_name'       => 'New State Rep Name',
			'add_or_remove_items' => 'Add or Remove State Reps',
			'view_item'           => 'View State Rep',
			'edit_item'           => 'Edit State Rep',
			'update_item'         => 'Update State Rep',

			'search_items' => 'Search State Reps',
			'not_found'    => 'No State Reps Found',
			'no_terms'     => 'No State Reps',

			'choose_from_most_used'      => 'Choose From the Most Used State Reps',
			'separate_items_with_commas' => 'Separate State Reps w/ Commas',

			'items_list'            => 'State Reps List',
			'items_list_navigation' => 'State Reps List Navigation',

			'archives'          => 'All State Reps',
			'popular_items'     => 'Popular State Reps',
			'parent_item'       => 'Parent State Rep',
			'parent_item_colon' => 'Parent State Rep:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF State Reps',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'sort'              => true,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => 'team/state-representatives',
			),
		);
		register_taxonomy(
			'srf-state-reps',
			'srf-team',
			$args
		);
	}

	/**
	 * Registers State Advocates taxonomy.
	 *
	 * @since 2021-19-21
	 */
	public function register_state_advocates_tax(): void {
		$labels = array(
			'name'          => 'SRF State Advocates',
			'singular_name' => 'SRF State Advocate',

			'name_admin_bar' => 'SRF State Advocate',
			'menu_name'      => 'State Advocates',

			'all_items'           => 'All State Advocates',
			'add_new_item'        => 'Add New State Advocate',
			'new_item_name'       => 'New State Advocate Name',
			'add_or_remove_items' => 'Add or Remove State Advocates',
			'view_item'           => 'View State Advocate',
			'edit_item'           => 'Edit State Advocate',
			'update_item'         => 'Update State Advocate',

			'search_items' => 'Search State Advocates',
			'not_found'    => 'No State Advocates Found',
			'no_terms'     => 'No State Advocates',

			'choose_from_most_used'      => 'Choose From the Most Used State Advocates',
			'separate_items_with_commas' => 'Separate State Advocates w/ Commas',

			'items_list'            => 'State Advocates List',
			'items_list_navigation' => 'State Advocates List Navigation',

			'archives'          => 'All State Advocates',
			'popular_items'     => 'Popular State Advocates',
			'parent_item'       => 'Parent State Advocate',
			'parent_item_colon' => 'Parent State Advocate:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF State Advocates',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'sort'              => true,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => 'team/state-advocates',
			),
		);
		register_taxonomy(
			'srf-state-advocates',
			'srf-team',
			$args
		);
	}

	/**
	 * Modifies permalinks.
	 *
	 * @param string $permalink Link.
	 * @param WP_Post $post Post object.
	 *
	 * @return string         Modified link.
	 * @since 2018-08-22
	 *
	 */
	public function modify_permalinks( $permalink, $post ): string {
		if ( 'srf-team' === $post->post_type ) {
			$resource_terms = get_the_terms( $post, 'srf-team-category' );
			$term_slug      = '';
			if ( ! empty( $resource_terms ) ) {
				foreach ( $resource_terms as $term ) {

					// The featured resource will have another category which is the main one.
					if ( 'featured' === $term->slug ) {
						continue;
					}

					$term_slug = $term->slug;
					break;
				}
			}
			$permalink = get_home_url() . '/team/' . $term_slug . '/' . $post->post_name;
		}

		return $permalink;
	}

	/**
	 * Attempts to fix pagination for taxonomy permalinks.
	 *
	 * @param  $wp_rewrite Rewrite rules array.
	 *
	 * @return void
	 * @since 2018-08-22
	 *
	 */
	public function custom_rewrite_rules( $wp_rewrite ): void {
		$rules = array();
		$terms = get_terms(
			array(
				'taxonomy'   => 'srf-team-category',
				'hide_empty' => false,
			)
		);

		$post_type = 'srf-team';

		foreach ( $terms as $term ) {

			$rules[ 'team/' . $term->slug . '/([^/]*)$' ] = 'index.php?post_type=' . $post_type . '&srf-team=$matches[1]&name=$matches[1]';

		}

		// merge with global rules.
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}
}

SRF_Team::get_instance()->init();
