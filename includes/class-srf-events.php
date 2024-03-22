<?php
/**
 * SRF Events Post Type
 *
 * @since 2021-09-21
 * @package srf
 */

namespace SRF_Events;

use Fieldmanager_Datepicker;
use WP_Post;

class SRF_Events {
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

		add_action( 'fm_post_srf-events', array( $this, 'register_event_date_fields' ) );
		add_action( 'fm_post_srf-resources', array( $this, 'register_event_date_fields' ) );
	}

	/**
	 * Registers SRF Events post type.
	 *
	 * @since 2021-19-21
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'          => 'SRF Events',
			'singular_name' => 'SRF Event',

			'name_admin_bar' => 'SRF Event',
			'menu_name'      => 'SRF Events',

			'all_items'    => 'All SRF Events',
			'add_new'      => 'Add SRF Event',
			'add_new_item' => 'Add New SRF Event',
			'new_item'     => 'New SRF Event',
			'edit_item'    => 'Edit SRF Event',
			'view_item'    => 'View SRF Event',

			'search_items'       => 'Search SRF Events',
			'not_found'          => 'No SRF Events Found',
			'not_found_in_trash' => 'No SRF Events Found in Trash',

			'parent_item_colon' => 'Parent SRF Event:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Events',
			'menu_icon'           => 'dashicons-tickets',
			'menu_position'       => 23, // After Resources.
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
				'slug'       => 'events/%srf-events-category%',
			),
			'taxonomies'          => array(
				'srf-events-category',
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
		register_post_type( 'srf-events', $args );
	}

	/**
	 * Registers SRF Events custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies(): void {
		$labels = array(
			'name'          => 'SRF Event Categories',
			'singular_name' => 'SRF Event Category',

			'name_admin_bar' => 'SRF Events Category',
			'menu_name'      => 'Event Categories',

			'all_items'           => 'All Event Categories',
			'add_new_item'        => 'Add New Event Category',
			'new_item_name'       => 'New Event Category Name',
			'add_or_remove_items' => 'Add or Remove Event Categories',
			'view_item'           => 'View Event Category',
			'edit_item'           => 'Edit Event Category',
			'update_item'         => 'Update Event Category',

			'search_items' => 'Search Event Categories',
			'not_found'    => 'No Event Categories Found',
			'no_terms'     => 'No Event Categories',

			'choose_from_most_used'      => 'Choose From the Most Used Event Categories',
			'separate_items_with_commas' => 'Separate Event Categories w/ Commas',

			'items_list'            => 'Event Categories List',
			'items_list_navigation' => 'Event Categories List Navigation',

			'archives'          => 'All Event Categories',
			'popular_items'     => 'Popular Event Categories',
			'parent_item'       => 'Parent Event Category',
			'parent_item_colon' => 'Parent Event Category:',
		);
		$args   = array(
			'labels'            => $labels,
			'description'       => 'SRF Event Categories',
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
			'srf-events-category',
			'srf-events',
			$args
		);
	}

	/**
	 * Registers Event date custom fields.
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

		$event_dates->add_meta_box( esc_html__( 'Event Date', 'srf' ), 'srf-events' );
		$event_dates->add_meta_box( esc_html__( 'Event Date', 'srf' ), 'srf-resources' );
	}

	/**
	 * Modifies permalinks.
	 *
	 * @param string $link Link.
	 * @param WP_Post $post Post object.
	 *
	 * @return string         Modified link.
	 * @since 2018-08-22
	 *
	 */
	public function modify_permalinks( $link, $post ): string {
		$link = (string) $link;

		if ( $post instanceof WP_Post && 'srf-events' === $post->post_type ) {
			$cats = get_the_terms( $post->ID, 'srf-events-category' );

			if ( $cats && is_array( $cats ) ) {
				$cat_slug = current( $cats )->slug;
				$link     = str_replace( '%srf-events-category%', $cat_slug, $link );
			} else {
				$link = str_replace( '%srf-events-category%', 'uncategorized', $link );
			}
		}

		return $link;
	}
}

SRF_Events::get_instance()->init();
