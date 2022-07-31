<?php

namespace Doton\Core\Encryption;


function CustomizeEncryption($Sampled, $Len = 8){

    $Output = false;

    if(gettype($Sampled) == "string"){

        $Sampled = explode(" ", $Sampled);

    }

        if(is_array($Sampled)){

            $Output = "";

            for($x = $Len; $x > 0; $x--){

                $Output .= $Sampled[ mt_rand(0, count($Sampled) - 1) ];

        }

    }

    return $Output;

}





