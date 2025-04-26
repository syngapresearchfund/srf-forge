<?php
/**
 * SRF Team Post Type
 *
 * @since 2021-09-21
 * @package srf-forge
 */

namespace SRF_Team;

use WP_Post;
use WP_Query;
use SRF_Base\SRF_Post_Type;

class SRF_Team extends SRF_Post_Type {
	/**
	 * Initialize additional functionality for this post type.
	 *
	 * @since 2024-03-26
	 */
	protected function init_post_type(): void {
		add_action( 'init', array( $this, 'register_taxonomies' ) );
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

			'all_items'    => 'All SRF Team Members',
			'add_new'      => 'Add SRF Team Member',
			'add_new_item' => 'Add New SRF Team Member',
			'new_item'     => 'New SRF Team Member',
			'edit_item'    => 'Edit SRF Team Member',
			'view_item'    => 'View SRF Team Member',

			'search_items'       => 'Search SRF Team Members',
			'not_found'          => 'No SRF Team Members Found',
			'not_found_in_trash' => 'No SRF Team Members Found in Trash',

			'parent_item_colon' => 'Parent SRF Team Member:',
		);

		$args = array(
			'labels'              => $labels,
			'description'         => 'SRF Team Members',
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
			'rewrite'             => array(
				'with_front' => false,
				'slug'       => 'team/%srf-team-category%',
			),
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
	 * Registers SRF Team custom taxonomies.
	 *
	 * @since 2021-19-21
	 */
	public function register_taxonomies(): void {
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
		);
		register_taxonomy(
			'srf-team-category',
			'srf-team',
			$args
		);
	}

	/**
	 * Modifies archive page to order by state meta field.
	 *
	 * @param WP_Query $query Query object.
	 *
	 * @since 2024-03-23
	 */
	public function order_by_state( $query ): void {
		if ( ! is_admin() && $query->is_main_query() && is_tax( 'srf-team-category', array(
				'state-ambassadors',
				'state-advocates'
			) ) ) {
			$query->set( 'posts_per_page', '100' );
			$query->set( 'meta_key', 'ambassador_states' );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'order', 'ASC' );
		}
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
	 * Adds custom rewrite rules.
	 *
	 * @param WP_Rewrite $wp_rewrite WordPress rewrite class.
	 *
	 * @since 2024-03-23
	 */
	public function custom_rewrite_rules( $wp_rewrite ): void {
		$new_rules = array(
			'team/([^/]+)/([^/]+)/?$' => 'index.php?post_type=srf-team&name=$matches[2]',
			'team/([^/]+)/?$'         => 'index.php?srf-team-category=$matches[1]',
		);
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}

SRF_Team::get_instance()->init();
