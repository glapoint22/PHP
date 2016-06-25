<form action="create_account.php" method="post" />
    <p>First Name: <input type="text" name="fname" /></p>
    <p>Last Name: <input type="text" name="lname" /></p>
    <p>E-mail: <input type="text" name="email" /></p>
    <p>Password: <input type="text" name="pw" /></p>
    <input type="submit" value="submit" />
    <input type="hidden" name="submitted" value="TRUE" />
</form>


<?php


    require_once("../include/config.php");

    if(isset($_POST['submitted']))
    {
        
        
        //First Name
        $fn = trim($_POST['fname']);
        if(preg_match('~^[a-zA-Z\.\'\-]{2,15}$~', $fn))
        {
            $fn = mysql_real_escape_string($fn, $dbc);
        }
        else
        {
            echo "Invalid first name!";
            exit();
        }


        //Last Name
        $ln = trim($_POST['lname']);
        if(preg_match('~^[a-zA-Z\.\'\-]{2,15}$~', $ln))
        {
            $ln = mysql_real_escape_string($ln, $dbc);
        }
        else
        {
            echo "Invalid last name!";
            exit();
        }


        //E-mail
        $email = trim($_POST['email']);
        if(preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$~', $email))
        {
            $email = mysql_real_escape_string($email, $dbc);
        }
        else
        {
            echo "Invalid e-mail!";
            exit();
        }


        //Password
        $pw = trim($_POST['pw']);
        if(preg_match('~^(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{8,16}$~', $pw))
        {
            $pw = mysql_real_escape_string($pw, $dbc);

            $salt = base64_encode(mcrypt_create_iv(24, MCRYPT_DEV_URANDOM));

            $pw = hash_pbkdf2("sha256", $pw, $salt, 1000, 24, false);

        }
        else
        {
            echo "Invalid Password!";
            exit();
        }

        $id = MD5(uniqid());

        mysql_query("INSERT INTO accounts(ID, FirstName, LastName, Email, Password, Salt) VALUES ('" . $id . "', '" . $fn . "', '" . $ln . "', '" . $email . "', '" . $pw . "', '" . $salt . "')");
        
    }



    
    
	
?>
		
	
