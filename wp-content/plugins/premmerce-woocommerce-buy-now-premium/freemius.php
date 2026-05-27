<?php

// Create a helper function for easy SDK access.
function premmerce_pwbn_fs()
{
    if ( ! class_exists( 'FsNull' ) ) {
        class FsNull {
            public static function is_registered() {
                return true;
            }

            public static function is__premium_only() {
                return true;
            }

            public static function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
                add_filter( $tag, $function_to_add, $priority, $accepted_args );
            }

            public static function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
                add_action( $tag, $function_to_add, $priority, $accepted_args );
            }
        }
    }

    return new FsNull();
}

// Init Freemius.
premmerce_pwbn_fs();
// Signal that SDK was initiated.
do_action( 'premmerce_pwbn_fs_loaded' );