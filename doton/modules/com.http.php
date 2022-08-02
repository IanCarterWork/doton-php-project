<?php



namespace Doton\Core\HTTP;

use function Doton\config;


function isSecureProtocol(){

    return (
        
        isset($_SERVER['HTTPS']) &&
        
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        
    ) ? true : false;
    
}


function URL(){

    $config = config();

    if(
        
        !isset( $config->host->name ) ||

        !is_object( $config->host->name )
        
    ){

        $public = DOTON_ROOT_DIR . DIRECTORY_SEPARATOR . ( ( isset( $config->directories->public )) ? $config->directories->public : '' );

        $info = ( ( isset( $_SERVER["PATH_INFO"] ) && !( $_SERVER["PATH_INFO"] ?: false ) ) ? dirname($_SERVER["PHP_SELF"]) : ('') );

        $root = ( $_SERVER["DOCUMENT_ROOT"] ?: $_SERVER["PWD"] ) .  $info;


        $serverprotocol = (
            
            isset( $_SERVER['REQUEST_SCHEME'] ) 
            
                ? $_SERVER['REQUEST_SCHEME']
                
                : ( 
                    
                    isset( $_SERVER[ 'SERVER_PROTOCOL' ] )

                    ?  $_SERVER[ 'SERVER_PROTOCOL' ]

                    : ''
                    
                )

            )  . '://'
            
        ;
        
        $servername = ( isset( $_SERVER["SERVER_NAME"] ) ? $_SERVER["SERVER_NAME"] : ('localhost') );

        $serverport = (isset($_SERVER["SERVER_PORT"])) 
        
            ? (($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? '' : ':' . $_SERVER['SERVER_PORT'])
            
            : ''

        ;


        $path = isset( $public ) ? ( explode( $root, $public )[1] ?: '' ) : '';

            $URL = (

            ( isset( $config->host->protocol ) ? $config->host->protocol : $serverprotocol )

            . $servername

            . $serverport

            . $info

            . (( DOTON_ROOT_DIR == $_SERVER['DOCUMENT_ROOT'] ) ? '' : $path)

            . "/"

        );
        
        $Built = (substr($URL, -2) == '//') ? substr($URL, 0, -1) : $URL;
        
        // $config['host'] = $Built;
        
        return $Built;
        
    }


    return $config->host->protocol . $config->host->name . ( $config->host->port ? ( ":" . $config->host->port ): '');
    
};






    
