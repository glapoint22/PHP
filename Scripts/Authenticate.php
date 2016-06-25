<?php


    require_once("../include/config.php");

    //Get the account data
    $account = decrypt($_POST['Account']);
    $accountData = explode("|", $account);

    //Get the account name and password
    $accountname = $accountData[0];
    $password = $accountData[1];
    

    //validate E-mail
    $email = trim($accountname);
    if(preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$~', $email))
    {
        $email = mysql_real_escape_string($email, $dbc);
    }
    else
    {
        echo encrypt(uniqid());
        exit();
    }
    


    //Get the salt
    $result = mysql_query("SELECT Salt from accounts WHERE Email = '" . $email . "'");
    if (mysql_num_rows($result) == 0)
    {
        echo encrypt(uniqid());
        exit();
    }
    $row = mysql_fetch_array($result);
    $salt = $row[0];



    //validate password
    $pw = trim($password);
    if(preg_match('~^(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{8,16}$~', $pw))
    {
        $pw = mysql_real_escape_string($pw, $dbc);
        $pw = hash_pbkdf2("sha256", $pw, $salt, 1000, 24, false);
    }
    else
    {
        echo encrypt(uniqid());
        exit();
    }


    //See if the password is in the database
    $result = mysql_query("SELECT ID from accounts WHERE Password = '" . $pw . "'");
    if (mysql_num_rows($result) == 0)
    {
        echo encrypt(uniqid());
        exit();
    }

    
    //Player ID
    $row = mysql_fetch_array($result);
    $id = $row[0];
    

    //Get character count and playerID
    $result = mysql_query("SELECT COUNT(CharacterIndex) as count from characters WHERE PlayerID = '" . $id . "'");
    $row = mysql_fetch_assoc($result);
    echo encrypt($row["count"] . "|" . $id);
    
    
	
?>
		
	
