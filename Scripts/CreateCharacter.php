<?php
    require_once("../include/config.php");

    $decryptString = decrypt($_POST['CharacterData']);

    $characterData = explode("|", $decryptString);

    $playerID = $characterData[0];
    $characterName = $characterData[1];
    $raceID = $characterData[2];

    

    //Validate the player ID
    if(!preg_match('~^[a-zA-Z0-9]{32}$~', $playerID))
    {
        //An error has occurred
        echo encrypt(uniqid());
        exit();
    }

   
   
    //Validate the race ID
    if(!preg_match('~^[0-9]$~', $raceID))
    {
        //An error has occurred
        echo encrypt(uniqid());
        exit();
    }

      

    //Make sure its a valid name
    if(preg_match('~^[a-zA-Z]{2,12}$~', $characterName))
    {
        $characterName = ucfirst($characterName);
    }
    else
    {
        echo encrypt(uniqid());
        exit();
    }

    //See if the name already exist
    $result = mysql_query("SELECT COUNT(CharacterName) as count FROM characters WHERE CharacterName = '" . $characterName . "'");
    $row = mysql_fetch_assoc($result);
    $count = $row["count"];

    //If the count is 0, that means the name is not in the database
    if($count == 0)
    {
        //Query to see if this player has any characters
        //If this player has no characters, we assign the index as 0 for this player's first character
        $index = 0;
        $result = mysql_query("SELECT CharacterIndex FROM characters WHERE PlayerID = '" . $playerID . "'");

        //If the player has at least 1 character, we find out what the next character index will be
        if(mysql_num_rows($result) > 0)
        {
            $result = mysql_query("SELECT MAX(CharacterIndex) as 'index' FROM characters WHERE PlayerID = '" . $playerID . "'");
            $row = mysql_fetch_assoc($result);
            $index = $row["index"] + 1;
        }


        //Insert the new character into the database
        $result = mysql_query("INSERT INTO characters(PlayerID, CharacterIndex, CharacterName, RaceID) VALUES ('" . $playerID . "', " . $index . ", '" . $characterName . "', " . $raceID . ")");

        if($result)
        {
            echo encrypt(rand());
        }
        
    }
    else
    {
        echo encrypt(uniqid());
    }
    
?>
		
	
