<?php
	DEFINE('DBHOST', 'localhost');
    DEFINE('DBUSER', 'root');
    DEFINE('DBPASSWORD', 'Gumpy');
    DEFINE('DBNAME', 'alien warfare');


    DEFINE('KEY', base64_decode("mviGtw1/fLj6eEc3mIo1w2NRCrDVZsMS4VGSMFTmMdc="));
    DEFINE('IV', base64_decode("7WMJTktvP4cDhdtThDK3LSu46y3DzksQ6Cj9+I2Bwxk="));


    


    if($dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD))
    {
        if(!mysqli_select_db($dbc, DBNAME))
        {
            trigger_error("Could not select DB!");
            exit();
        }
    }else
    {
        trigger_error("Could not connect!");
        exit();
    }
    
    global $dbc;



    function encrypt($text)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, KEY, $text, MCRYPT_MODE_CBC, IV));
    }



    function decrypt($text)
    {
        return trim(mcrypt_decrypt (MCRYPT_RIJNDAEL_256, KEY, base64_decode($text), MCRYPT_MODE_CBC, IV));
    }


    
       


?>
