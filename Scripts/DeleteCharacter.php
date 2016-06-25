<?php
    require_once("../include/config.php");

    $decryptString = decrypt($_POST['CharacterData']);

    $characterData = explode("|", $decryptString);

    $playerID = $characterData[0];
    $characterIndex = $characterData[1];

    

    //Validate the player ID
    if(!preg_match('~^[a-zA-Z0-9]{32}$~', $playerID))
    {
        //Invalid field
        echo encrypt("INVALID FIELD");
        exit();
    }

   
   
    //Validate the character index
    if(!preg_match('~^[0-9]+$~', $characterIndex))
    {
        //Invalid field
        echo encrypt("INVALID FIELD");
        exit();
    }

      
    
    
    $query = "UPDATE characters SET Deleted = true WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex;
    $result = mysql_query($query);
    
?>
		
	
