<?php


namespace Doton\Core\Server;


use Doton\Core\Utilities\IValidator;
use VisualKit\Console as VisualKitConsole;
use VisualKit\Table;



function DetectCli(){

    return (
        
        php_sapi_name() === 'cli' ||

        php_sapi_name() === 'cli-server'
    
    );
    
}




class ResourceChecker{


    static public function version( 

        string $label,

        string | null $version,

        string | null $original,
        
        bool $exit = true 
        
    ){

        $original = $original ?: PHP_VERSION;


        if( $version && version_compare( $original, $version, '<') ){ 

            if( class_exists( '\VisualKit\Console' ) ){

                VisualKitConsole::log( 
                    
                    $label,
                    
                    "Requires PHP < " . $version . ' > version',

                    $exit

                );

            }

            else{

                exit( $label . " : Requires PHP < " . $version . ' > version' );
                
            }

            return false;

        }


        return true;
        
    }
    



    static public function modules( 
        
        string $label, 
        
        array $modules, 
        
        bool $exit = true 
        
    ){
                
        $found = 0;

        $length = count( $modules );


        if( 
            
            class_exists( '\VisualKit\Console' ) &&

            class_exists( '\VisualKit\Table' ) 
            
        ){

            $table = new Table();

                $table->column('Status');

                $table->column('Module');

            

            foreach( $modules as $name ){

                $status = extension_loaded($name);

                $table->row(

                    $status,

                    "" . $name . ""
                    
                );

                if( $status ){ $found++; }

            }

            if( $found != $length ){

                VisualKitConsole::error( 
                    
                    $label,
                    
                    $table->toString(),

                    $exit

                );

                return false;

            }

        }


        else{

            foreach( $modules as $name ){

                $status = extension_loaded($name);

                if( $status ){ $found++; }

                else{

                    echo $label . " : Requires PHP Modules ( " . ($status ? 'YES' : 'NO') . ") " . $name;
                    
                }

            }

            if( $found != $length ){

                if( $exit === true ){ exit; }

                return false;

            }
            
        }
        



        return true;
        
    }
    
    



    static public function settings(
        
        string $label, 

        array $settings,
        
        bool $exit = true 
        
    ){
            

        if( !DetectCli() ){

            
            $ini = ini_get_all( null, false );
        
            $found = 0;

            $length = count( $settings );



            if( 
                
                class_exists( '\VisualKit\Console' ) &&

                class_exists( '\VisualKit\Table' ) 
                
            ){
            
                $table = new Table();

                    $table->column('Status');

                    $table->column('Settings');

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

                    VisualKitConsole::error( 
                        
                        $label,
                        
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

                        echo $label . " : equires PHP Settings : ( " . ($status ? 'YES' : 'NO') . ") " . $name;
                        
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
    
    
    
}




