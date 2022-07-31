<?php


namespace Doton\Core\Server;


use Doton\Core\Utilities\IValidator;
use VisualKit\Console;
use VisualKit\Table;



function DetectCli(){

    return (
        
        php_sapi_name() === 'cli' ||

        php_sapi_name() === 'cli-server'
    
    );
    
}




class ResourceChecker{


    static public function version( bool $exit = true ){

        if( DOTON_PROJECT_VERSION && version_compare(PHP_VERSION, DOTON_PROJECT_VERSION, '<') ){ 

            if(  class_exists( '\VisualKit\Console' ) ){

                Console::log( 
                    
                    'Project requires PHP version',
                    
                    "This project requires PHP version " . DOTON_PROJECT_VERSION,

                    $exit

                );

            }

            else{

                exit( "This project requires PHP version " . DOTON_PROJECT_VERSION );
                
            }

            return false;

        }


        return true;
        
    }
    



    static public function modules( bool $exit = true ){
                
        if( DOTON_PROJECT_MODULES && is_array( DOTON_PROJECT_MODULES ) ){ 

            $found = 0;

            $length = count( DOTON_PROJECT_MODULES );


            if( class_exists( '\VisualKit\Console' ) ){
    
                $table = new Table();

                    $table->column('Status');

                    $table->column('Module');

                

                foreach( DOTON_PROJECT_MODULES as $name ){

                    $status = extension_loaded($name);

                    $table->row(

                        $status,

                        "" . $name . ""
                        
                    );

                    if( $status ){ $found++; }

                }

                if( $found != $length ){

                    Console::error( 
                        
                        'Project requires PHP Modules',
                        
                        $table->toString(),

                        $exit

                    );

                    return false;

                }

            }


            else{

                foreach( DOTON_PROJECT_MODULES as $name ){

                    $status = extension_loaded($name);

                    if( $status ){ $found++; }

                    else{

                        echo "Project requires PHP Modules ( " . ($status ? 'YES' : 'NO') . ") " . $name;
                        
                    }

                }

                if( $found != $length ){

                    if( $exit === true ){ exit; }

                    return false;

                }
                
            }
            
        }


        return true;
        
    }
    
    



    static public function settings(
        
        array $settings,
        
        bool $exit = true 
        
    ){
            
        $ini = ini_get_all( null, false );
    
        $found = 0;

        $length = count( $settings );



        if( class_exists( '\VisualKit\Console' ) ){
        
            $table = new Table();

                $table->column('Status');

                $table->column('Paramètre');

                $table->column('Require');
                
                $table->column('Valeur');



            foreach( $settings as $name => $setting ){

                $value = $ini[ $name ];

                $status = IValidator::matching(

                    $setting->validator,

                    $value,

                    $setting->expect,

                    isset( $setting->operator ) ? $setting->operator : '<=',

                );
                    
                $table->row(

                    ($status),

                    "" . ($name) . "",

                    ("" . $setting->expect ?: 0) . "",

                    "" . ($value) . "",
                    
                );

                if( $status ){ $found++; }

            }




            if( $found != $length ){

                Console::error( 
                    
                    'Project requires PHP Settings',
                    
                    $table->toString(),

                    $exit

                );

                return false;

            }

        }

        else{

            foreach( $settings as $name => $setting ){

                $value = $ini[ $name ];

                $status = IValidator::matching(

                    $setting->validator,

                    $value,

                    $setting->expect,

                    isset( $setting->operator ) ? $setting->operator : '<=',

                );
                    
                if( $status ){ $found++; }

                else{

                    echo "Project requires PHP Settings : ( " . ($status ? 'YES' : 'NO') . ") " . $name;
                    
                }

            }

            if( $found != $length ){

                if( $exit === true ){ exit; }

                return false;

            }
            
        }




        return true;
        
    }
    
    
    
}




