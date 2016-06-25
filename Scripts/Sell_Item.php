<?php
    require_once("../include/config.php");

    //Get the data
    $decryptString = decrypt($_POST['Data']);
    $itemData = explode("|", $decryptString);

    //Assign the variables
    $playerID = $itemData[0];
    $characterIndex = $itemData[1];
    $category = $itemData[2];
    $slotNum = $itemData[3];
    $stackCount = $itemData[4];
    


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
    
    

    //Validate the category
    if(!preg_match('~^[0-9]+$~', $category))
    {
        //Invalid field
        echo encrypt("INVALID FIELD");
        exit();
    }


     //Validate the slot num
    if(!preg_match('~^[0-9]+$~', $slotNum))
    {
        //Invalid field
        echo encrypt("INVALID FIELD");
        exit();
    }


    //Validate the stackCount
    if(!preg_match('~^[0-9]+$~', $stackCount))
    {
        //Invalid field
        echo encrypt("INVALID FIELD");
        exit();
    }



    $query = "SELECT StackCount, ItemID from characterinventory WHERE PlayerID = '" . $playerID . "' 
            AND CharacterIndex = " . $characterIndex . " AND Category = " . $category . " AND 
            SlotNum = " . $slotNum;

    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    $inventoryStackCount =  $row[0];
    $itemID =  $row[1];
    $finalStackCount = 0;

    if($stackCount < $inventoryStackCount)
    {
        $finalStackCount = $inventoryStackCount - $stackCount;
        
        $query = "UPDATE characterinventory SET StackCount = " . $finalStackCount . " 
                WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex . " AND 
                Category = " . $category . " AND SlotNum = " . $slotNum;

        $result = mysql_query($query);
    }
    else
    {
        $query = "DELETE FROM characterinventory WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = 
                " . $characterIndex . " AND Category = " . $category . " AND SlotNum = " . $slotNum;

        $result = mysql_query($query);
    }



    $query = "SELECT (Sell * " . $stackCount . ") + (SELECT characters.Currency from characters WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex . ") FROM items WHERE ID = '" . $itemID . "'";
    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    $currency = $row[0];

    

    $query = "UPDATE characters SET Currency = " . $currency . " WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex;

    $result = mysql_query($query);

    echo encrypt($finalStackCount . "|" . $currency);
    
    
?>
