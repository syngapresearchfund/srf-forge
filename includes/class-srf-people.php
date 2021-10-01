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
	}

	/**
	 * Registers SRF People post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type() : void {
		register_post_type(
			'srf-people',
			[
				'public'        => true,

				'supports'      => [
					'title',
					'editor',
					'excerpt',
					'author',
					'thumbnail',
					'custom-fields',
					'page-attributes',
					'revisions',
				],
				'hierarchical'  => true,
				'has_archive'   => true,
				'show_in_rest'  => true,
				'taxonomies'    => [
					'srf-people-category',
					'srf-people-tag',
				],

				'rewrite'       => [
					'with_front' => false,
					'slug'       => 'people',
				],

				'menu_position' => 9, // After Snippets [8].
				'menu_icon'     => 'dashicons-admin-page',

				'description'   => 'SRF People',
				'labels'        => [
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

					'insert_into_item'      => 'Insert Into SRF Person',
					'uploaded_to_this_item' => 'Upload to this SRF Person',

					'featured_image'        => 'Set Featured Image',
					'remove_featured_image' => 'Remove Featured Image',
					'use_featured_image'    => 'Use as Featured Image',

					'items_list'            => 'SRF People List',
					'items_list_navigation' => 'SRF People List Navigation',

					'archives'              => 'SRF People Archives',
					'filter_items_list'     => 'Filter SRF People List',
					'parent_item_colon'     => 'Parent SRF Person:',
				],
			]
		);
	}

	/**
	 * Registers SRF People custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies() : void {
		register_taxonomy(
			'srf-people-category',
			'srf-people',
			[
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'hierarchical'      => true,

				'rewrite'           => [
					'with_front' => false,
					'slug'       => 'category',
				],

				'description'       => 'SRF People Categories',
				'labels'            => [
					'name'                       => 'SRF People Categories',
					'singular_name'              => 'SRF People Category',

					'name_admin_bar'             => 'SRF People Category',
					'menu_name'                  => 'Categories',

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
				],
			]
		);

		register_taxonomy(
			'srf-people-tag',
			'srf-people',
			[
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'hierarchical'      => false,

				'rewrite'           => [
					'with_front' => false,
					'slug'       => 'tag',
				],

				'description'       => 'SRF People Tags',
				'labels'            => [
					'name'                       => 'SRF People Tags',
					'singular_name'              => 'SRF People Tag',

					'name_admin_bar'             => 'SRF People Tag',
					'menu_name'                  => 'Tags',

					'all_items'                  => 'All Tags',
					'add_new_item'               => 'Add New Tag',
					'new_item_name'              => 'New Tag Name',
					'add_or_remove_items'        => 'Add or Remove Tags',
					'view_item'                  => 'View Tag',
					'edit_item'                  => 'Edit Tag',
					'update_item'                => 'Update Tag',

					'search_items'               => 'Search Tags',
					'not_found'                  => 'No Tags Found',
					'no_terms'                   => 'No Tags',

					'choose_from_most_used'      => 'Choose From the Most Used Tags',
					'separate_items_with_commas' => 'Separate Tags w/ Commas',

					'items_list'                 => 'Tags List',
					'items_list_navigation'      => 'Tags List Navigation',

					'archives'                   => 'All Tags',
					'popular_items'              => 'Popular Tags',
					'parent_item'                => 'Parent Tag',
					'parent_item_colon'          => 'Parent Tag:',
				],
			]
		);
	}
}
SRF_People::get_instance()->init();