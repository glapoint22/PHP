<?php
    require_once("../include/config.php");

    DEFINE('MAX_SLOTS', '20');
    $slots = array();

    //Get the data
    $decryptString = decrypt($_POST['Data']);
    $itemData = explode("|", $decryptString);

    //Assign the variables
    $playerID = $itemData[0];
    $characterIndex = $itemData[1];
    $itemID = $itemData[2];
    $stackCount = $itemData[3];
    $startStackCount = $itemData[3];


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


    //Validate the stackCount
    if(!preg_match('~^[0-9]+$~', $stackCount))
    {
        //Invalid field
        echo encrypt("INVALID FIELD");
        exit();
    }


    //See if the character has enough money
    $currency = GetFunds($playerID, $characterIndex, $itemID, $startStackCount);
    if(!$currency)
    {
        echo encrypt("INSUFFICIENT FUNDS");
        exit();
    }


    //Query for information we need about this item
    $itemInfo = GetItemInfo($itemID);
    $category = $itemInfo[0];
    $isStackable = $itemInfo[1];
    $stackCap = $itemInfo[2];
    $cost = $itemInfo[3];

    //If the item is stackable
    if($isStackable)
    {
        //See if this item exists in the character's inventory
        $result = FindItem($playerID, $characterIndex, $itemID, $category, $stackCap);
        
            
        //Loop through the number of times this item exists in the inventory
        //Calculate what the stack count should be for each slot this item is in
        while ($row = mysql_fetch_row($result))
        {
            $slotNum = $row[0];
            $slotStackCount = $row[1];

            $sum = $slotStackCount + $stackCount;

            if($sum <= $stackCap)
            {
                array_push($slots, $slotNum, $sum);
                $stackCount = 0;
                break;
            }
            else
            {
                array_push($slots, $slotNum, $stackCap);
                $stackCount =  $sum - $stackCap;
            }
        }


        //We still have remaining stack counts of this item
        if($stackCount > 0)
        {
            //Get the next available slot
            $slotNum = GeEmptytSlot($playerID, $characterIndex, $itemID, $category);

            //If slotNum is -1 this means inventory is full
            if($slotNum == -1)
            {
                echo encrypt("INVENTORY FULL");
                exit();
            }

            //We have the same number of items in a stack when we started, so insert into an empty slot
            if($stackCount == $startStackCount)
            {
                //Insert the new item into the character's inventory
                echo encrypt(Insert($playerID, $characterIndex, $itemID, $category, $slotNum, $stackCount) . "|" . DebitFunds($playerID, $characterIndex, $currency, $cost, $startStackCount));
            }
            else
            {
                //We have to fill exisiting slots and populate a new slot
                $data = Update($playerID, $characterIndex, $category, $slots);
                $data .= "|" . Insert($playerID, $characterIndex, $itemID, $category, $slotNum, $stackCount);
                echo encrypt($data . "|" . DebitFunds($playerID, $characterIndex, $currency, $cost, $startStackCount));
            }
        }
        else
        {
            //Fill exisiting slots
            echo encrypt(Update($playerID, $characterIndex, $category, $slots) . "|" . DebitFunds($playerID, $characterIndex, $currency, $cost, $startStackCount));
        }
    }
    else
    {
        //Get the next available slot
        $slotNum = GeEmptytSlot($playerID, $characterIndex, $itemID, $category);

    
        //If slotNum is -1 this means inventory is full
        if($slotNum == -1)
        {
            echo encrypt("INVENTORY FULL");
            exit();
        }

        //Insert the new item into the character's inventory
        echo encrypt(Insert($playerID, $characterIndex, $itemID, $category, $slotNum, $stackCount) . "|" . DebitFunds($playerID, $characterIndex, $currency, $cost, $startStackCount));
    }

    
    


    
   


    





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


    function GetFunds($playerID, $characterIndex, $itemID, $stackCount)
    {
        //Query to see if this character has enough money
        $query = "SELECT characters.Currency, items.Cost FROM characters, items WHERE 
                items.ID = '" . $itemID . "' and characters.PlayerID = '" . $playerID . "' AND 
                characters.CharacterIndex = " . $characterIndex;

        //Get the result
        $result = mysql_query($query);
        $row = mysql_fetch_row($result);
        $currency =  $row[0];
        $cost = $row[1];

        if($cost * $stackCount > $currency)
        {
            return 0;
        }
        else
        {
            return $currency;
        }
    }


    function GetItemInfo($itemID)
    {
        $query = "SELECT InventoryCategory, IsStackable, StackCap, Cost FROM items WHERE items.ID = '" . $itemID . "'";
    
        //Get the result
        $result = mysql_query($query);
        return mysql_fetch_row($result);
        
    }

    function FindItem($playerID, $characterIndex, $itemID, $category, $stackCap)
    {
        $query = "SELECT SlotNum, StackCount FROM characterinventory WHERE PlayerID = '" . $playerID . "' 
                AND CharacterIndex = " . $characterIndex . " and ItemID = '" . $itemID . "' 
                and Category = " . $category . " AND StackCount < " . $stackCap;

        return mysql_query($query);
        
    }



    function Insert($playerID, $characterIndex, $itemID, $category, $slotNum, $stackCount)
    {
        $query = "INSERT INTO characterinventory (PlayerID, CharacterIndex, ItemID, Category, SlotNum, StackCount) 
                VALUES ('" . $playerID . "', " . $characterIndex . ", '" . $itemID . "', " . $category . ", " 
                . $slotNum . ", " . $stackCount . ")";

        $result = mysql_query($query);

        return $category . "|" . $slotNum . "|" . $stackCount;
    }


    function Update($playerID, $characterIndex, $category, $slots)
    {
        $when = "";
        $in = "";
        $data = "";

        for($i = 0; $i < count($slots); $i += 2)
        {
            $slotNum = $slots[$i];
            $stackCount = $slots[$i + 1];
            

            $when .= " WHEN " . $slotNum . " THEN " . $stackCount;
            
            if($i + 2 < count($slots))
            {
                $in .= $slotNum . ", ";
                $data .= $category . "|" . $slotNum . "|" . $stackCount . "|";
            }
            else
            {
                $in .= $slotNum;
                $data .= $category . "|" . $slotNum . "|" . $stackCount;
            }
        }
                
        
        $query = "UPDATE characterinventory SET StackCount = CASE SlotNum" . $when . " 
                END WHERE SlotNum IN(" . $in . ") AND PlayerID = '" . $playerID . "' AND 
                CharacterIndex = " . $characterIndex . " AND Category = " . $category;

        $result = mysql_query($query);

        return $data;
    }

    function DebitFunds($playerID, $characterIndex, $currency, $cost, $stackCount)
    {
        $amount = $currency - ($cost * $stackCount);

        $query = "UPDATE characters SET Currency = " . $amount . " WHERE PlayerID = '" . $playerID . "' and CharacterIndex = " . $characterIndex;

        $result = mysql_query($query);

        return $amount;

    }
?>
