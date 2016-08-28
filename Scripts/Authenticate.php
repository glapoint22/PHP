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
        $email = mysqli_real_escape_string($dbc, $email);
    }
    else
    {
        echo encrypt(uniqid());
        exit();
    }
    


    //Get the salt
    $result = mysqli_query($dbc, "SELECT Salt from accounts WHERE Email = '" . $email . "'");
    if (mysqli_num_rows($result) == 0)
    {
        echo encrypt(uniqid());
        exit();
    }
    $row = mysqli_fetch_array($result);
    $salt = $row[0];



    //validate password
    $pw = trim($password);
    if(preg_match('~^(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{8,16}$~', $pw))
    {
        $pw = mysqli_real_escape_string($dbc, $pw);
        $pw = hash_pbkdf2("sha256", $pw, $salt, 1000, 24, false);
    }
    else
    {
        echo encrypt(uniqid());
        exit();
    }


    //See if the password is in the database
    $result = mysqli_query($dbc, "SELECT ID from accounts WHERE Password = '" . $pw . "'");
    if (mysqli_num_rows($result) == 0)
    {
        echo encrypt(uniqid());
        exit();
    }

    
    //Player ID
    $row = mysqli_fetch_array($result);
    $id = $row[0];
    

    //Get character count and playerID
    $result = mysqli_query($dbc, "SELECT COUNT(CharacterIndex) as count from characters WHERE PlayerID = '" . $id . "' and deleted = 0");
    $row = mysqli_fetch_assoc($result);
    echo encrypt($row["count"] . "|" . $id);
    
    
	
?>
		
	
