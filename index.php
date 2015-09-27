<?php
/**
 * Load initial app state, or initiate some action processing
 */

// Init autoloader
require_once( dirname( __FILE__ ) . '/autoload.php' );

if ( isset( $_GET['action'] ) ) {
    new Table\Controllers\Main;
} else {
    require_once 'Views/main.html';
}