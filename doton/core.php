<?php


namespace Doton;

use Doton\Core\Utilities\IArray;


if( !defined( 'DOTON_CORE_DIR' ) ){ exit( 'Doton Core not found' ); }


const DOTON_NAME = 'Doton (Yomi Numa)';

const DOTON_VERSION = '0.0.1';

const DOTON_SERIE = 'virivitae';




function configFile(){

    return DOTON_PROJECT_CONFIG_FILE;
    
}




function config( string $branch = null, mixed $value = null ) : mixed{

    $path = configFile();

    $arguments = func_get_args();

    $config = json_decode( file_get_contents( $path ) );
    

    if( isset($arguments[0]) ){

        /**
         * Set Value
         */
        if( isset( $arguments[1] ) ){

            $get = config( $branch );

            if( $get != $value ){

                $config->{ $branch } = $value;

                return file_put_contents( $path, json_encode( $config, JSON_PRETTY_PRINT ) );

            }

            return null;
            
        }

        /**
         * Get Value
         */
        else{

            return isset( $config->{ $branch } ) ? $config->{ $branch } : null;
            
        }

    }

    else{

        return $config;
        
    }


}




class Module{

    static public function load( array $modules, bool $exit = true ){

        if( is_array( $modules ) ) {

            foreach($modules as $module){
    
                $file = DOTON_CORE_DIR . "/modules/com.$module.php";
    
    
                if(file_exists($file)){
    
                    require $file;
                    
                }
    
                else{
    
                    if( $exit === true ){

                        exit("Doton Core Module < $module > not found");

                    }
                    
                }
                
            }
            
            return true;
            
        }
    
        return false;

    }
    
}



class Package{

    
    const EXTENSION = '.php';
    
    static public function stabilizePath( string $path ){

        return implode( DIRECTORY_SEPARATOR, explode("\\", $path ) );

    }
    
    static public function paths( string $class ){

        $config = config();

        $config->autoload = (
        
            isset(  $config->autoload ) && is_object( $config->autoload )
        
            ?  $config->autoload 
            
            : ( object )[]
            
        );

        $namespace = explode('\\', $class)[0] . '\\';

        $file = self::stabilizePath( $class ) . self::EXTENSION;

        $ns = DOTON_CORE_DIR . 
        
            DIRECTORY_SEPARATOR . 'packages' . 
            
            DIRECTORY_SEPARATOR . ( 
                
                isset( $config->autoload->{ $namespace } ) 
                
                ? $config->autoload->{ $namespace } 
                
                : ''

            )
            
        ;

        $dir = dirname( $file );

        $base = basename( $file );


        return [

            $ns . $dir  . DIRECTORY_SEPARATOR . $base,

            $ns . $dir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $base,

            $ns . $dir . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $base,

            $ns . $dir . DIRECTORY_SEPARATOR . 'package' . DIRECTORY_SEPARATOR . $base,

            $ns . $dir . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . $base,
        
        ];
        
    }
    
    static public function autoload( string $class ){

        $found = false;

        $paths = self::paths( $class );
        

        foreach( $paths as $path ){

            if( is_file( $path ) ){

                $__DOTON_AUTOLOAD_IGNORE__ = false;

                    include $path;

                    $found = true;

                if( $__DOTON_AUTOLOAD_IGNORE__ === false ){ break; }
                
            }
            
        }
        
        return $found;

    }
    
}

