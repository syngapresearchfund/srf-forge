<?php
/**
 * SRF Warriors Post Type
 *
 * @since 2021-09-21
 * @package srf-forge
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
	 * Registers SRF Warriors post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'          => 'SRF Warriors',
			'singular_name' => 'SRF Warrior',

			'name_admin_bar' => 'SRF Warrior',
			'menu_name'      => 'SRF Warriors',

			'all_items'    => 'All SRF Warriors',
			'add_new'      => 'Add SRF Warrior',
			'add_new_item' => 'Add New SRF Warrior',
			'new_item'     => 'New SRF Warrior',
			'edit_item'    => 'Edit SRF Warrior',
			'view_item'    => 'View SRF Warrior',

			'search_items'       => 'Search SRF Warriors',
			'not_found'          => 'No SRF Warriors Found',
			'not_found_in_trash' => 'No SRF Warriors Found in Trash',

			'parent_item_colon' => 'Parent SRF Warrior:',
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
}

SRF_Warriors::get_instance()->init();
