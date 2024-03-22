<?php
/**
 * SRF Resources Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Resources;

use Fieldmanager_Datepicker;
use WP_Post;

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
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_filter( 'post_type_link', array( $this, 'modify_permalinks' ), 10, 2 );
		add_action( 'generate_rewrite_rules', array( $this, 'custom_rewrite_rules' ) );

		add_action( 'fm_post_srf-resources', array( $this, 'register_event_date_fields' ) );
	}

	/**
	 * Registers SRF Resources post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'          => 'SRF Resources',
			'singular_name' => 'SRF Resource',

			'name_admin_bar' => 'SRF Resource',
			'menu_name'      => 'SRF Resources',

			'all_items'    => 'All SRF Resources',
			'add_new'      => 'Add SRF Resource',
			'add_new_item' => 'Add New SRF Resource',
			'new_item'     => 'New SRF Resource',
			'edit_item'    => 'Edit SRF Resource',
			'view_item'    => 'View SRF Resource',

			'search_items'       => 'Search SRF Resources',
			'not_found'          => 'No SRF Resources Found',
			'not_found_in_trash' => 'No SRF Resources Found in Trash',

			'parent_item_colon' => 'Parent SRF Resource:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Resources',
			'menu_icon'           => 'dashicons-networking',
			'menu_position'       => 22, // After Team.
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
				'page-attributes',
			),
		);
		register_post_type( 'srf-resources', $args );
	}

	/**
	 * Registers SRF Resources custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies(): void {
		$labels = array(
			'name'          => 'SRF Resource Categories',
			'singular_name' => 'SRF Resource Category',

			'name_admin_bar' => 'SRF Resource Category',
			'menu_name'      => 'Resource Categories',

			'all_items'           => 'All Resource Categories',
			'add_new_item'        => 'Add New Resource Category',
			'new_item_name'       => 'New Resource Category Name',
			'add_or_remove_items' => 'Add or Remove Resource Categories',
			'view_item'           => 'View Resource Category',
			'edit_item'           => 'Edit Resource Category',
			'update_item'         => 'Update Resource Category',

			'search_items' => 'Search Resource Categories',
			'not_found'    => 'No Resource Categories Found',
			'no_terms'     => 'No Resource Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Resource Categories',
			'separate_items_with_commas' => 'Separate Resource Categories w/ Commas',

			'items_list'            => 'Resource Categories List',
			'items_list_navigation' => 'Resource Categories List Navigation',

			'archives'          => 'All Resource Categories',
			'popular_items'     => 'Popular Resource Categories',
			'parent_item'       => 'Parent Resource Category',
			'parent_item_colon' => 'Parent Resource Category:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF Resources Categories',
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
				'slug'       => 'resources',
			),
		);
		register_taxonomy(
			'srf-resources-category',
			'srf-resources',
			$args
		);
	}

	/**
	 * Registers Event date custom fields for webinars.
	 *
	 * @since 2024-03-22
	 */
	public function register_event_date_fields(): void {
		if ( ! class_exists( 'Fieldmanager_Datepicker' ) ) {
			return; // Fieldmanager not loaded. Bail early.
		}

		$event_dates = new Fieldmanager_Datepicker( array(
			'name'     => 'event_date',
			'label'    => esc_html__( 'Choose date & time', 'srf' ),
			'use_time' => true,
		) );

		$event_dates->add_meta_box( esc_html__( 'Event Date', 'srf' ), 'srf-resources' );
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
		if ( 'srf-resources' === $post->post_type ) {
			$resource_terms = get_the_terms( $post, 'srf-resources-category' );
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
			$permalink = get_home_url() . '/resources/' . $term_slug . '/' . $post->post_name;
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
				'taxonomy'   => 'srf-resources-category',
				'hide_empty' => false,
			)
		);

		$post_type = 'srf-resources';

		foreach ( $terms as $term ) {

			$rules[ 'resources/' . $term->slug . '/([^/]*)$' ] = 'index.php?post_type=' . $post_type . '&srf-resources=$matches[1]&name=$matches[1]';

		}

		// merge with global rules.
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}
}

SRF_Resources::get_instance()->init();
