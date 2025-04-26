<?php
/**
 * SRF Post Type Class
 *
 * @since 2024-03-26
 * @package srf-forge
 */

namespace SRF_Base;

abstract class SRF_Post_Type {
    /**
     * Singleton instances.
     *
     * @since 2024-03-26
     *
     * @var array Instances.
     */
    protected static $instances = array();

    /**
     * Has been initialized yet?
     *
     * @since 2024-03-26
     *
     * @var bool Initialized?
     */
    private $did_init;

    /**
     * Protected constructor.
     *
     * @since 2024-03-26
     */
    protected function __construct() {
        $this->did_init = false;
    }

    /**
     * Create or return instance of this class.
     *
     * @since 2024-03-26
     */
    public static function get_instance(): self {
        $class = get_called_class();
        if ( ! isset( static::$instances[$class] ) ) {
            static::$instances[$class] = new $class();
        }

        return static::$instances[$class];
    }

    /**
     * Initialize the plugin.
     *
     * @since 2024-03-26
     */
    public function init(): void {
        if ( $this->did_init ) {
            return; // Already initialized.
        }

        // Flag as initialized.
        $this->did_init = true;

        add_action( 'init', array( $this, 'register_post_type' ) );

        // Call child-specific initialization
        $this->init_post_type();
    }

    /**
     * Child classes can override this to add their own initialization logic
     *
     * @since 2024-03-26
     */
    protected function init_post_type(): void {
        // Optional override in child classes
    }

    /**
     * Register the post type.
     * Must be implemented by child classes.
     *
     * @since 2024-03-26
     */
    abstract public function register_post_type(): void;
}
