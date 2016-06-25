<?php
    require_once("../include/config.php");

    //Get the data
    $decryptString = decrypt($_POST['Data']);
    $data = explode("|", $decryptString);

    //Assign the variables
    $playerID = $data[0];
    $characterIndex = $data[1];
    $itemID = $data[2];
    $category = $data[3];
    $slotNum = $data[4];


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
    
    

    //Validate the item ID
    if(!preg_match('~^[a-zA-Z0-9]{8}$~', $itemID))
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


    

    $query = "SELECT ArmorType FROM armor WHERE ItemID = '" . $itemID . "'";
    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    $armorSlot =  $row[0];



    $query = "SELECT ItemID FROM characterequippeditems WHERE PlayerID = '" . $playerID . "' 
            and CharacterIndex = " . $characterIndex . " and ArmorSlot = " . $armorSlot;
    $result = mysql_query($query);

    if(mysql_num_rows($result))
    {
        $row = mysql_fetch_row($result);
        $equippedItemID = $row[0];

        $query = "UPDATE characterinventory SET ItemID = '" . $equippedItemID . "' WHERE 
                PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex . 
                " AND Category = " . $category . " AND SlotNum = " . $slotNum;

        $result = mysql_query($query);

        $query = "UPDATE characterequippeditems SET ItemID = '" . $itemID . "' WHERE 
                PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex . 
                " AND ArmorSlot = " . $armorSlot;

        $result = mysql_query($query);

        echo encrypt($equippedItemID);
    }
    else
    {
        $query = "INSERT INTO characterequippeditems (PlayerID, CharacterIndex, ArmorSlot, ItemID) 
                VALUES ('" . $playerID . "', " . $characterIndex . ", " . $armorSlot . ", '" . $itemID . "')";

        $result = mysql_query($query);

        $query = "DELETE FROM characterinventory WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex . " AND ItemID = '" . $itemID . "' AND Category = " . $category . " AND SlotNum = " . $slotNum;
        $result = mysql_query($query);

        echo encrypt($itemID);
    }

    
?>
