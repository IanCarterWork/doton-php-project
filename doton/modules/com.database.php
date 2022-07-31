<?php



namespace Doton\Core\Database;



class Configuration{

    public $Host;

    public $Name;

    public $User;

    public $Password;

    public $Charset = "utf8mb4";


    public function __construct(
        
        String $Host,

        String $Name,

        String $User,

        String $Password

    ) {


        $this->Host = $Host;

        $this->Name = $Name;

        $this->User = $User;

        $this->Password = $Password;
        
    }
    

}




