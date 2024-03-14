<?php
/**
 * SRF Siblings Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Siblings;

class SRF_Siblings {
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
	}

	/**
	 * Registers SRF Siblings post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'          => 'Syngap Siblings',
			'singular_name' => 'Syngap Sibling',

			'name_admin_bar' => 'Syngap Sibling',
			'menu_name'      => 'Syngap Siblings',

			'all_items'    => 'Syngap Siblings',
			'add_new'      => 'Add Syngap Sibling',
			'add_new_item' => 'Add New Syngap Sibling',
			'new_item'     => 'New Syngap Sibling',
			'edit_item'    => 'Edit Syngap Sibling',
			'view_item'    => 'View Syngap Sibling',

			'search_items'       => 'Search Syngap Siblings',
			'not_found'          => 'No Syngap Siblings Found',
			'not_found_in_trash' => 'No Syngap Siblings Found in Trash',

			'parent_item_colon' => 'Parent Syngap Sibling:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'Syngap Siblings',
			'menu_icon'           => 'dashicons-heart',
			'menu_position'       => 20, // After Pages.
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=srf-warriors',
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
				'slug'       => 'syngap-siblings',
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
		register_post_type( 'srf-siblings', $args );
	}
}

SRF_Siblings::get_instance()->init();
