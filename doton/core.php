<?php


namespace Doton;

use Doton\Core\Server\ResourceChecker;
use Doton\Core\Utilities\IArray;


if( !defined( 'DOTON_CORE_DIR' ) ){ exit( 'Doton Core not found' ); }


const DOTON_NAME = 'Doton';

const DOTON_VERSION = '0.0.1';

const DOTON_SERIE = 'virivitae';




function configFile(){

    return DOTON_PROJECT_CONFIG_FILE;
    
}



function parseConfig( string $path, string $branch = null, mixed $value = null ) : mixed{

    if( is_file( $path ) ){
    
        $config = new Config( $path );

        // $config = json_decode( file_get_contents( $path ) );


        if( isset($arguments[1]) ){

            /**
             * Set Value
             */
            if( isset( $arguments[2] ) ){

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
    
    return null;

}



function config( string $branch = null, mixed $value = null ) : mixed{

    $path = configFile();
    
    $arguments = func_get_args();


    if( isset($arguments[1]) ){

        return parseConfig( $path, $branch, $value );

    }

    else if( isset($arguments[0]) ){

        return parseConfig( $path, $branch );

    }

    else{

        return parseConfig( $path );

    }
    
}


/**
 * @property object|null ${'php-require'}
 */
class Config{

    public bool $FILE_EXISTS = false;
    
    public bool $PARAMETERS_EXISTS = false;


    public string $mode;
    
    public string $name;
    
    public string $version;
    
    public string $description;
    
    public object | null $doton;
    
    public object | null $autoload;
    
    public object | null $bin;
    
    public object | null $development;
    
    public object | null $production;
    
    public object | null $dependencies;
    
    public object | null $devDependencies;
    
    
    public function __construct( public string $file ){

        if( is_file( $this->file ) ){
    
            $parameters = json_decode( file_get_contents( $this->file ) );

            if( is_object( $parameters ) ){

                foreach( $parameters as $key => $value ){

                    $this->{ $key } = $value;
                    
                }

                $this->PARAMETERS_EXISTS = true;

            }
            
            $this->FILE_EXISTS = true;

        }
        
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


    static protected array $caches = [
        
        'config' => []

    ];
    

    static function cache( string $slot ){

        return isset( self::$caches[ $slot ] ) ? self::$caches[ $slot ] : null; 
        
    }
    
    
    static public function stabilizePath( string $path ){

        return implode( DIRECTORY_SEPARATOR, explode("\\", $path ) );

    }
    
    
    static public function paths( 
        
        string $class, 
        
        array | null $slots = null,

        bool $loadClass = true,
        
    ){

        $config = config();


        $namespace = explode('\\', $class)[0] . '\\';

        $file = self::stabilizePath( $class ) . self::EXTENSION;

        $dir = dirname( $file );

        $base = basename( $file );

        $config->autoload = (
        
            isset(  $config->autoload ) && is_object( $config->autoload )
        
            ?  $config->autoload 
            
            : ( object )[]
            
        );

        $slots = is_array( $slots ) ? $slots : [

            'src', 'lib', 'package', 'main'
            
        ];

        $ns = DOTON_CORE_DIR . 
        
            DIRECTORY_SEPARATOR . 'packages' . 
            
            DIRECTORY_SEPARATOR 
            
        ;

        $composite = [

            ...array_map(function( string $slot ) use ( $ns, $dir, $base, $loadClass ) {

                return str_replace( '//', '/',
                    
                    $ns . $dir . DIRECTORY_SEPARATOR . ($slot ?: '') . DIRECTORY_SEPARATOR . ($loadClass ? $base : '')

                );
                
            }, $slots),

        ];



        if( 
            
            isset( $config->autoload->{ $namespace } ) && 
            
            is_string( $config->autoload->{ $namespace } ) 
            
        ){

            $pkg = DOTON_ROOT_DIR 
                    
                . DIRECTORY_SEPARATOR 
                
                . $config->autoload->{ $namespace }

                . $base
                
            ;

            $composite[] = $pkg;
                

            if( $loadClass === false ){

                $delegatePkg = dirname(
                    
                    DOTON_ROOT_DIR 
                    
                    . DIRECTORY_SEPARATOR 
                    
                    . $config->autoload->{ $namespace }
                
                ) . DIRECTORY_SEPARATOR;


                if( count( $slots ) ){

                    foreach( $slots as $slot ){

                        $composite[] = $delegatePkg . $slot . DIRECTORY_SEPARATOR;

                    }
                    
                }

                else{
                    
                    $composite[] = $delegatePkg;

                }


            }

        }
        


        if( $loadClass === true ){

            $composite[] = $ns . $dir  . DIRECTORY_SEPARATOR . $base;

        }



        return array_reverse($composite);
        
    }
    

    static public function autoload( string $class ){

        $found = false;

        $paths = self::paths( $class );

        foreach( $paths as $path ){

            if( is_file( $path ) ){

                if( self::validate( $class ) ){

                    $__DOTON_AUTOLOAD_CONTINUE__ = false;

                        include $path;

                        $found = true;

                    if( $__DOTON_AUTOLOAD_CONTINUE__ === false ){ break; }

                }
                
            }
            
        }
        
        return $found;

    }
    



    static public function config( string $class ){

        $ns = explode('\\', $class )[0];

        
        if( isset( self::$caches[ 'config' ][ $ns ] )){

            return self::$caches[ 'config' ][ $ns ];
            
        }


        $file = self::stabilizePath(
            
            DOTON_CORE_DIR . DIRECTORY_SEPARATOR . 
            
            'packages' . DIRECTORY_SEPARATOR . 
            
            $ns . DIRECTORY_SEPARATOR . 
            
            basename( DOTON_PROJECT_CONFIG_FILE )

        );

    
        self::$caches[ 'config' ][ $ns ] = parseConfig( $file );

        return self::$caches[ 'config' ][ $ns ];

    }
    
    

    static public function validate( string $class ){

        $ns = explode('\\', $class )[0];

        $config = self::config( $class );

        if( $config ){

            $dotonrequire = ( isset( $config->doton ) && is_object( $config->doton ) )

                ? $config->doton : (object) [];
        
            
            $phprequire = ( isset( $config->{'php-require'} ) && is_object( $config->{'php-require'} ) )
            
                ? $config->{'php-require'} : (object) [];
            


            if( isset( $dotonrequire->version ) ){

                ResourceChecker::version( 

                    $ns . ' requires Doton version',

                    $dotonrequire->version,

                    DOTON_VERSION,
                    
                    true 

                );

            }
            


            if( isset( $phprequire->version ) ){

                ResourceChecker::version( 

                    $ns . ' requires PHP version',

                    $phprequire->version,

                    PHP_VERSION,
                    
                    true 

                );

            }


            if( isset( $phprequire->modules ) ){

                ResourceChecker::modules(
        
                    $ns . ' requires PHP Modules',
                    
                    (array) $phprequire->modules, 
                    
                    true 
                
                );

            }


            if( isset( $phprequire->settings ) ){

                ResourceChecker::settings(

                    $ns . ' requires PHP Settings',
                    
                    (array) $phprequire->settings, 
                    
                    true 
                    
                );     

            }

            

        }

        return true;

    }
    
    

    static public function assets( 
        
        object | string $class, 
        
        string $filename, 
        
        bool $return = true 
        
    ) : string | null {

        $paths = self::paths( $class, [ 'assets' ], false );



        foreach( $paths as $path ){

            $asset = $path . $filename;

            echo "<pre>"; var_dump( $asset ); echo "</pre>";

            if( is_file( $asset ) ){

                if( $return ){ return file_get_contents( $asset ); }

                if( !$return ){ include( $asset ); }
                
                break;
                
            }

        }

        return null;
        
    }

}

