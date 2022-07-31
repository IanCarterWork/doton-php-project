<?php


namespace Doton\Core\Utilities;



function useIProps( object | string $instance, array $props = []){

    return ( new $instance ) ( (object) $props );

}







trait IProps{

    public function __invoke( object $param ){

        $param = is_object( $param ) ? $param : (object) [];

        foreach( $param as $key => $value ){

            if( property_exists( $this, $key ) ){

                $this->{ $key } = $value;

            }
            
        }
        
        return $this;

    }
    
}





class CoreAssetsTemplate{


    public string $templateFile = '';

    public string $template = '';

    public string $generated = '';
    

    public function __construct( 
        
        public string $path, 
        
        public array $variables = []
        
    ) {

        $this->templateFile = $path;

        if( is_file( $this->templateFile )){


            $this->variables[ 'CONSOLE_COLOR' ] = isset( $this->variables[ 'CONSOLE_COLOR' ] ) 
            
                ? $this->variables[ 'CONSOLE_COLOR' ]
                
                : "#8200BA"

            ;

            $this->serializes();
            
            $this->template = file_get_contents( $this->templateFile );

        }

        else{ exit('CoreAssetsTemplate : template not found " ' . $path . ' "' ); }

    }
    


    public function serializes(){

        foreach($this->variables as $key => $value ){

            $this->variables[ $key ] = nl2br( $this->variables[ $key ] );
            
        }
        
        return $this;
        
    }
    

    public function toString() : string | null{

        $this->generated = $this->template;
        
        preg_match_all( "/%(.*?)%/U", $this->generated, $matches, PREG_PATTERN_ORDER );

        if( 
            
            is_array( $matches ) &&

            count( $matches[0] ) &&

            count( $matches[1] ) 
            
        ){

            $patterns = $matches[0];

            $paths = $matches[1];


            for ($key=0; $key < count( $patterns ); $key++) { 

                $this->generated = str_replace( 
                    
                    $patterns[ $key ], 
                    
                    isset( $this->variables[ $paths[ $key ] ] ) 
                        
                        ? $this->variables[ $paths[ $key ] ] 
                        
                        : $paths[ $key ],

                    $this->generated
                
                );
                
            }

        }

        return $this->generated ?: null;

    }
    
    
    
}




class IArray{

    static public function range(array $array = [], ?int $from = 0, ?int $to = 0) : array{

        $range = [];
    
        $to = $to ? $to : count($array);
    
        for ($i=$from; $i < $to; $i++) { 
    
            if($array[$i]){
    
                $range[] = $array[$i];
    
            }
            
        }
    
    
        return $range;
        
    }
    

    
}



class Cli{


    static public function OpenURL(string $URL, bool $apply = false): ?string {

        switch (PHP_OS) {

            case 'Darwin':
                $opener = 'open';
            break;

            case 'WINNT':
                $opener = 'start';
            break;
            
            default:
                $opener = 'xdg-open';
        }
    

        $cmd = sprintf('%s %s', $opener, $URL);

        if($apply === true){

            echo "Open URL..." . PHP_EOL;

            exec($cmd);

        }

        else{

            return $cmd;
            
        }

    }
    
}



class IString{

    static public function isHTML( string $input ){

        return preg_match( "/<[^<]+>/", $input );

    }
    

    static public function getBytes( string $val ){

        $val = trim( $val );

        $last = strtolower( $val[ strlen( $val ) - 1 ]);

        $num = (int) $val;
        
        switch($last) {

            case 'g': $num *= 1024;

            case 'm': $num *= 1024;

            case 'k': $num *= 1024;
        }
    
        return $num;

    }

    
}






class IValidator{


    static public function matching( 
        
        string $validator, 
        
        int | string | bool | null $expect = null, 
        
        int | string | bool | null $value = null,
        
        string $operator = "<=" 

    ){

        switch( $validator ){


            case 'boolean':

                return (bool) $value == (bool) $expect;

            break;
            
            


            case 'string':

                return $value == $expect;

            break;
            
            


            case 'integer':

                if( $operator == "<"){ return $value < $expect; }

                else if( $operator == "=="){ return $value == $expect; }

                else if( $operator == ">"){ return $value > $expect; }
                
                else if( $operator == ">="){ return $value >= $expect; }
                
                else { return $value <= $expect; }

            break;
            
            


            case 'bytes':

                $value = IString::getBytes( (string) $value );

                $expect = IString::getBytes( (string) $expect );


                if( $operator == "<"){ return $value < $expect; }

                else if( $operator == "=="){ return $value == $expect; }
                
                else if( $operator == ">"){ return $value > $expect; }
                
                else if( $operator == ">="){ return $value >= $expect; }
                
                else{ return $value <= $expect; }

            break;
            
            
        }


        return false;
        
    }
    
    
}


