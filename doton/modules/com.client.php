<?php


namespace Doton\Core\Client;

    use const Doton\Core\Contantes\ALPHA;
    use const Doton\Core\Contantes\ALPHA_NUMERIC;
    use const Doton\Core\Contantes\ALPHA_NUMERIC_LOWER;
    use const Doton\Core\Contantes\ALPHA_NUMERIC_UPPER;
    use const Doton\Core\Contantes\NUMERIC;

    use function Doton\Core\Encryption\CustomizeEncryption;

    


    const CookieID = 'sn-client-borrowing';


    

	function HTTP_SEC_FETCH_MODE() : ?string {

		return ( isset($_SERVER[ 'HTTP_SEC_FETCH_MODE' ]) ) ? $_SERVER[ 'HTTP_SEC_FETCH_MODE' ] : null;
		
	}
	
    

	function DetectFetchNavigation() : bool {

		return HTTP_SEC_FETCH_MODE() != 'navigate';
		
	}
	
	


    function CreateBorrowing(?String $Splitter = '/', ?Int $Length = 32){

        return implode($Splitter, [ 

            CustomizeEncryption(ALPHA_NUMERIC_LOWER, $Length),

            CustomizeEncryption(ALPHA_NUMERIC, $Length),

            CustomizeEncryption(ALPHA, $Length),

            CustomizeEncryption(NUMERIC, $Length),

            time(),

            CustomizeEncryption(ALPHA, $Length),
            
            CustomizeEncryption(ALPHA_NUMERIC, $Length),
            
            CustomizeEncryption(ALPHA_NUMERIC_UPPER, $Length),

            CustomizeEncryption(NUMERIC, $Length)
            
        ]);
        
    }
    
    

    function Borrowing(){

        $encrypted = isset( $_COOKIE[ CookieID ] ) ?  $_COOKIE[ CookieID ] : CreateBorrowing();

        if(!isset($_COOKIE[ CookieID ])){

			try{

				setcookie(
					
					CookieID, 
					
					$encrypted,

					time() + (3600 * 24 * 7 * 30)
				
				);

			}

			catch( \Exception $e){

			}

        }

        return $encrypted;

    }





	function Infos(){

		$UA = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : false;

		// var_dump($UA);exit;

		$Out = [

			"browser" => null

			, "platform" => null

			, "version" => null

			, "model" => null

		];

		if(is_string($UA)){

			$Browsers = [

				"Edg" => "Microsft Edge"

				,"Firefox"

				, "Chrome"

				, "MSIE" => "msie trident"

				, "Edge"

				, "Safari"

				, "Symbian"

				, "Ovi"

				, "Opera"

				, "OperaMini"

				, "BlackBerry"

			];

			$OS = [

				"Android" => " android "

				, "iOS"

				, "SymbianOS"

				, "BlackBerry"

				, "TV"

				, "Win" => "windows"

				, "MacOSX" => "Mac OS X"

				, "MacOS" => "Macintosh; "

				, "Linux"

			];


			foreach($Browsers as $Name => $Brow){

				$Brs = explode(" ", is_numeric($Name) ? $Brow : $Name);

				$Found = false;

				// echo "<pre>"; var_dump('Browser Type', $Brs, $Brow); echo "</pre>";

				foreach($Brs as $Browser){

					$P0 = '/' . strtolower($Browser) . '\/((?:[0-9]+\.?)+)/i';

					$P1 = '/' . strtolower($Browser) . '((?:[0-9]+\.?)+)\/((?:[0-9]+\.?)+)$/i';


					if(preg_match($P0, $UA, $About)){

						$Out['browser'] = $Brow;

						$Out['browser.Agent'] = $Browser;

						$Out['version'] = $About[1];

						$Found = true;

						break;

					}


					if(preg_match($P1, $UA, $About)){

						$Out['browser'] = $Brow;

						$Out['browser.Agent'] = $Browser;

						$Out['model'] = $About[1];

						$Out['version'] = $About[2];

						$Found = true;

						break;

					}

				}


				if($Found === true){break;}

			}


			foreach($OS as $Name => $Get){

				$GOs = explode(" ", is_numeric($Name) ? $Get : $Name);

				$Found = false;

				foreach($GOs as $O){

					$P0 = '/' . strtolower($Get) . '/i';

					if($Fo = preg_match($P0, strtolower($UA))){

						$Out['platform'] = $O;

						$Found = true;

					}

				}

				if($Found === true){break;}

			}


		}

		return $Out;

	}




    function Browser(){

		return (is_array($Get = Infos()) && isset($Get["browser"]))

            ? $Get["browser"]

            : null

        ;

    }



    function BrowserVersion(){

		return (is_array($Get = Infos()) && isset($Get["version"]))

            ? $Get["version"]

            : null

        ;

    }



    function Platform(){

		return (is_array($Get = Infos()) && isset($Get["platform"]))

            ? $Get["platform"]

            : null

        ;

    }





	function iP(){

		$Out = false;

		$Headers = [

			'HTTP_VIA'

			, 'HTTP_X_FORWARDED_FOR'

			, 'HTTP_FORWARDED_FOR'

			, 'HTTP_X_FORWARDED'

			, 'HTTP_FORWARDED'

			, 'HTTP_CLIENT_IP'

			, 'HTTP_FORWARDED_FOR_IP'

			, 'VIA'

			, 'X_FORWARDED_FOR'

			, 'FORWARDED_FOR'

			, 'X_FORWARDED'

			, 'FORWARDED'

			, 'CLIENT_IP'

			, 'FORWARDED_FOR_IP'

			, 'HTTP_PROXY_CONNECTION'

			, 'REMOTE_ADDR'

		];


		foreach($Headers as $Header){

			if(isset($_SERVER[$Header])){

				$Out = $_SERVER[$Header];

				break;

			}

		}


		return str_replace("::1", "127.0.0.1",$Out);


    }


    





