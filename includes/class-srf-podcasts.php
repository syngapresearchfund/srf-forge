<?php
/**
 * SRF Podcasts Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Podcasts;

use WP_Post;

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
	}

	/**
	 * Registers SRF Podcasts post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'          => 'SRF Podcasts',
			'singular_name' => 'SRF Podcast',

			'name_admin_bar' => 'SRF Podcast',
			'menu_name'      => 'SRF Podcasts',

			'all_items'    => 'All SRF Podcasts',
			'add_new'      => 'Add SRF Podcast',
			'add_new_item' => 'Add New SRF Podcast',
			'new_item'     => 'New SRF Podcast',
			'edit_item'    => 'Edit SRF Podcast',
			'view_item'    => 'View SRF Podcast',

			'search_items'       => 'Search SRF Podcasts',
			'not_found'          => 'No SRF Podcasts Found',
			'not_found_in_trash' => 'No SRF Podcasts Found in Trash',

			'parent_item_colon' => 'Parent SRF Podcast:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Podcasts',
			'menu_icon'           => 'dashicons-microphone',
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
	public function register_taxonomies(): void {
		$labels = array(
			'name'          => 'SRF Podcast Categories',
			'singular_name' => 'SRF Podcast Category',

			'name_admin_bar' => 'SRF Podcast Category',
			'menu_name'      => 'Podcast Categories',

			'all_items'           => 'All Podcast Categories',
			'add_new_item'        => 'Add New Podcast Category',
			'new_item_name'       => 'New Podcast Category Name',
			'add_or_remove_items' => 'Add or Remove Podcast Categories',
			'view_item'           => 'View Podcast Category',
			'edit_item'           => 'Edit Podcast Category',
			'update_item'         => 'Update Podcast Category',

			'search_items' => 'Search Podcast Categories',
			'not_found'    => 'No Podcast Categories Found',
			'no_terms'     => 'No Podcast Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Podcast Categories',
			'separate_items_with_commas' => 'Separate Podcast Categories w/ Commas',

			'items_list'            => 'Podcast Categories List',
			'items_list_navigation' => 'Podcast Categories List Navigation',

			'archives'          => 'All Podcast Categories',
			'popular_items'     => 'Popular Podcast Categories',
			'parent_item'       => 'Parent Podcast Category',
			'parent_item_colon' => 'Parent Podcast Category:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF Podcasts Categories',
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
				'slug'       => 'podcasts',
			),
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
	 * @param string $permalink Link.
	 * @param WP_Post $post Post object.
	 *
	 * @return string         Modified link.
	 * @since 2018-08-22
	 *
	 */
	public function modify_permalinks( $permalink, $post ): string {
		if ( 'srf-podcasts' === $post->post_type ) {
			$podcast_terms = get_the_terms( $post, 'srf-podcasts-category' );
			$term_slug     = '';
			if ( ! empty( $podcast_terms ) ) {
				foreach ( $podcast_terms as $term ) {

					// The featured podcast will have another category which is the main one.
					if ( 'featured' === $term->slug ) {
						continue;
					}

					$term_slug = $term->slug;
					break;
				}
			}
			$permalink = get_home_url() . '/podcasts/' . $term_slug . '/' . $post->post_name;
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
				'taxonomy'   => 'srf-podcasts-category',
				'hide_empty' => false,
			)
		);

		$post_type = 'srf-podcasts';

		foreach ( $terms as $term ) {

			$rules[ 'podcasts/' . $term->slug . '/([^/]*)$' ] = 'index.php?post_type=' . $post_type . '&srf-podcasts=$matches[1]&name=$matches[1]';

		}

		// merge with global rules.
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}
}

SRF_Podcasts::get_instance()->init();
