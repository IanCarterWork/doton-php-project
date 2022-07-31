<?php
namespace Doton\Core\Error;

use VisualKit\Console;
use ErrorException;
use Exception;
use Throwable;
use VisualKit\Badge;
use VisualKit\Block;
use VisualKit\Table;

function ShutDown(...$arguments){

    $errors = error_get_last();

    if( $errors ){

        throw (new ExceptionConsole(
            
            $errors['message'] ?: 'NaN'
            
            , $errors['type'] ?: 0 
        
        ))->console( $errors['file'] ?: null, $errors['line'] ?: null  );
    
    }

    return true;
    
}





function Handler(

    int $level,

    string $message,

    string $file,

    int $line

){

    $coloring = 'error';

    $name = "ERROR";

    switch ($level) {

        case E_USER_ERROR: 
            $coloring = 'error'; 
            $name = "USER_ERROR";
        break;

        case E_USER_WARNING: 
            $coloring = 'warning'; 
            $name = "USER_WARNING";
        break;

        case E_USER_DEPRECATED: 
            $coloring = 'warning'; 
            $name = "USER_DEPRECATED";
        break;

        case E_USER_NOTICE: 
            $coloring = 'notice'; 
            $name = "USER_NOTICE";
        break;

    }

    throw (new ExceptionConsole(
            
        "( " . $name . " ) " . $message ?: 'NaN'
        
        , $level ?: 0 

        , null, $coloring
    
    ))->console( $file ?: null, $line ?: null  );

    return true;

}




class ExceptionConsole extends Exception{


    public array $blacklist = [
        
        // 'Doton\Core\Error\Handler',
        
        // 'Doton\Core\Error\ShutDown',

        // 'Doton\Core\Error\ExceptionConsole',

    ];
    
    

    public function __construct(
        
        string $message, 
        
        int $code = 0, 
        
        Throwable | null $previous = null,

        public string $color = 'error',
        
    ) {

        parent::__construct($message, $code, $previous);

    }




    public function console( ?string $file = '',  ?string $line = '' ) : void{

        if( class_exists( '\VisualKit\Console' ) ){

            $composite = '';
        
            $composite .= Block::box( 
                
                "Message", 
                
                $this->message
            
            );
    
            $composite .= Block::box( 
                
                "File", 
                
                ( $file ?: $this->file ) . " " . Badge::error( 'Line  ' . ( $line ?: $this->line ) ) 
            
            );
    
    
    
            $table = new Table();
    
            $table->column('#');
    
            $table->column('Function');
            
            $table->column('File');
            
            $table->column('Line');
            
            foreach( array_reverse( $this->getTrace() ) as $trace ){
    
                if( in_array( $trace['function'], $this->blacklist ) ){ continue; }
                
                $table->row(
    
                    '↓',
    
                    $trace['function'] ?: 'NaN',
                    
                    isset( $trace['file'] ) ? $trace['file']: 'NaN',
                    
                    isset( $trace['line'] ) ? $trace['line'] : 'NaN',
                    
                );
    
            }
            
            $composite .= Block::box( "Stack Traces", $table->toString() );
    
    
    
            $console = method_exists( Console::class, $this->color );
            
            if( $console ){
    
                Console::{ $this->color }( 
                    
                    "Exception Console [ code : " .  (  $this->code ?: 0 ) ." ]" ,
                    
                    $composite,
        
                    true
        
                );
    
            }
    
            if( !$console ){
    
                Console::error( 
                    
                    "Exception Console [ code : " .  (  $this->code ?: 0 ) ." ]" ,
                    
                    $composite,
        
                    true
        
                );
    
            }
            
        }

        else{

            echo "" . $this . "";
            
        }

        exit(0);
        
    }



    public function __toString() {

        return "\n" . __CLASS__ . ": [{$this->code}]: {$this->message}\n";

    }
    
    
}
