<?php

namespace Doton;


if( !defined( 'DOTON_CORE_DIR' ) ){ exit( 'Doton Core not found' ); }

/**
 * 
 * --------------------------------------
 * |                                    |
 * |    Doton Native Modules            |
 * |                                    |
 * --------------------------------------
 * 
 */

function nativeModules(){

    $nativeModules = [];

    $nativeModules[] = "errors";

    $nativeModules[] = "client";

    $nativeModules[] = "constantes";

    $nativeModules[] = "database";

    $nativeModules[] = "encryption";

    $nativeModules[] = "http";

    $nativeModules[] = "server";

    $nativeModules[] = "utilities";

    return $nativeModules;
    
}


/**
 * 
 * --------------------------------------
 * |                                    |
 * |    Doton Modules Blend             |
 * |                                    |
 * --------------------------------------
 * 
 */

function loadModules(){

    $modules = config('modules') ?: [];

    $modules = is_array( $modules ) ? $modules : [];

    foreach( $modules as $mod){

        $modules[] = $mod;
        
    }

    return array_merge( nativeModules(), $modules );
    
}