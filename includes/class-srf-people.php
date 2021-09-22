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
					'author',
					'page-attributes',
					'revisions',
				],
				'hierarchical'  => true,
				'has_archive'   => false,
				'show_in_rest'  => true,

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
}
SRF_People::get_instance()->init();