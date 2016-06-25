<?php
    require_once("../include/config.php");

    DEFINE('MAX_SLOTS', '20');

    //Get the data
    $decryptString = decrypt($_POST['Data']);
    $itemData = explode("|", $decryptString);

    //Assign the variables
    $playerID = $itemData[0];
    $characterIndex = $itemData[1];
    $itemID = $itemData[2];


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

    //Query for information we need about this item
    $itemInfo = GetItemInfo($itemID);
    $category = $itemInfo[0];
    
    //Get the next available slot
    $slotNum = GeEmptytSlot($playerID, $characterIndex, $itemID, $category);

    
    //If slotNum is -1 this means inventory is full
    if($slotNum == -1)
    {
        echo encrypt("INVENTORY FULL");
        exit();
    }

    //Insert the new item into the character's inventory
    echo encrypt(Insert($playerID, $characterIndex, $itemID, $category, $slotNum));

    $query = "DELETE FROM characterequippeditems WHERE PlayerID = '" . $playerID . "' AND CharacterIndex = " . $characterIndex . " AND ItemID = '" . $itemID . "'";
    $result = mysql_query($query);





    function GeEmptytSlot($playerID, $characterIndex, $itemID, $category)
    {
        $slotNum = -1;

        //Query for an empty slot
        $query = "SELECT SlotNum from characterinventory WHERE PlayerID = '" . $playerID . "' 
                and CharacterIndex = " . $characterIndex . " and Category = " . $category;
                
        //Get the results from the query
        $result = mysql_query($query);
        $num_rows = mysql_num_rows($result);
        

        //Find an empy slot
        for($i = 0; $i < $num_rows; $i++)
        {
            $row = mysql_fetch_row($result);
            if($i != $row[0])
            {
                $slotNum = $i;
                break;
            }
        }

        //If an empty slot was not found
        if($slotNum == -1)
        {
            //Is the inventory full?
            if($i< MAX_SLOTS)
            {
                //Assign a slot
                $slotNum = $i;
            }
            
        }

        return $slotNum;
    }


    


    function GetItemInfo($itemID)
    {
        $query = "SELECT InventoryCategory FROM items WHERE items.ID = '" . $itemID . "'";
    
        //Get the result
        $result = mysql_query($query);
        return mysql_fetch_row($result);
        
    }

    



    function Insert($playerID, $characterIndex, $itemID, $category, $slotNum)
    {
        $query = "INSERT INTO characterinventory (PlayerID, CharacterIndex, ItemID, Category, SlotNum, StackCount) 
                VALUES ('" . $playerID . "', " . $characterIndex . ", '" . $itemID . "', " . $category . ", " 
                . $slotNum . ", 1)";

        $result = mysql_query($query);

        return $category . "|" . $slotNum . "|1";
    }


    
?>
