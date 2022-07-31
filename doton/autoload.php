<?php



/**
 * 
 * --------------------------------------
 * |                                    |
 * |    Define Doton Constante          |
 * |                                    |
 * --------------------------------------
 * 
 */

define('DOTON_CORE_DIR', dirname( __FILE__ ) );

define('DOTON_ROOT_DIR', dirname( DOTON_CORE_DIR ) );

define('DOTON_PROJECT_CONFIG_FILE', DOTON_ROOT_DIR . DIRECTORY_SEPARATOR . "doton.json" );


/**
 * 
 * --------------------------------------
 * |                                    |
 * |    Doton Bootstrapper              |
 * |                                    |
 * --------------------------------------
 * 
 */

require dirname( __FILE__ ) . "/index.php";

