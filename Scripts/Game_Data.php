<?php
    require_once("../include/config.php");
    DEFINE('DELIMITER', '|');



    $playerID = decrypt($_POST['PlayerID']);
    
    

    if(!preg_match('~^[a-zA-Z0-9]{32}$~', $playerID))
    {
        //An error has occurred
        exit();
    }

    $data = "";

    //Weapons
    $query = "SELECT itemtypes.Value as ItemType, items.ID, items.Name, items.Cost, items.Sell, items.ShopCategory, items.ImageIndex, items.IsStackable, items.StackCount, 
                items.StackCap, weapons.WeaponID, weapons.CriticalStrike FROM items JOIN itemtypes on itemtypes.ItemType = items.Type JOIN 
                weapons on items.ID = weapons.ItemID";
    $data .= GetData($query, true, false);


    //Item Mods
    $query = "SELECT ItemID, ModName, ModDescription, UpgradeCount, BaseCost, CostExponent FROM itemmodifications";
    $data .= GetData($query, false, false);


    //Armor
    $query = "SELECT itemtypes.Value as ItemType, items.ID, items.Name, items.Cost,  items.Sell, items.ShopCategory, items.ImageIndex, items.IsStackable, armor.ArmorType, 
                armor.Armor, armor.Intelligence, armor.Agility, armor.Vigor, armor.Resilience, armor.Vitality, armor.Health, armor.ArmorID FROM items JOIN 
                itemtypes on itemtypes.ItemType = items.Type JOIN armor ON armor.ItemID = items.ID";
    $data .= GetData($query, true, false);


    //Characters
    $query = "SELECT Level, CharacterName, Rating, Currency, characters.RaceID, races.RaceName, races.Team, characters.HasKeybinds, 
                races.BaseHealth, CharacterIndex FROM characters JOIN races on races.ID = characters.RaceID WHERE characters.PlayerID = '" . 
                $playerID . "' and Deleted = false";
    $data .= GetData($query, false, true);




    //Character Abilities
    $query = "SELECT characters.CharacterIndex, abilities.Name, abilities.Description, abilities.AvailableLevel from abilities JOIN 
                characters ON abilities.RaceID = characters.RaceID WHERE characters.PlayerID = '" . $playerID . "' and characters.Deleted = false";
    $data .= GetData($query, false, false);
   


    //Attributes
    $query = "SELECT Name, Description FROM attributes";
    $data .= GetData($query, false, false);
    


    //Character Attributes
    $query = "SELECT characters.CharacterIndex, Value, LevelExponent FROM baseattributes JOIN characters ON characters.RaceID = 
                baseattributes.RaceID and characters.PlayerID = '" . $playerID . "' and characters.Deleted = false";
    $data .= GetData($query, false, false);


    
    //Statistics
    $query = "SELECT Name FROM statistics";
    $data .= GetData($query, false, false);


    
    //Character Statistics
    $query = "SELECT characters.CharacterIndex, characterstatistics. StatisticValue FROM characters JOIN characterstatistics ON 
                characterstatistics.PlayerID = characters.PlayerID AND characterstatistics.CharacterIndex = characters.CharacterIndex 
                AND characters.PlayerID = '" . $playerID . "' and characters.Deleted = false";
    $data .= GetData($query, false, false);

    


    //Inventory
    $query = "SELECT characters.CharacterIndex, characterinventory.ItemID, characterinventory.Category, characterinventory.SlotNum, 
                characterinventory.StackCount from characters JOIN characterinventory ON characterinventory.PlayerID = characters.PlayerID 
                AND characterinventory.CharacterIndex = characters.CharacterIndex AND characters.PlayerID = '" . $playerID . "' AND characters.Deleted = false";
    $data .= GetData($query, false, false);
    


    //CharacterItemModifications
    $query = "SELECT characters.CharacterIndex, characteritemmodifications.ItemID, characteritemmodifications.ModIndex, characteritemmodifications.UpgradeNum from 
                characters JOIN characteritemmodifications ON characteritemmodifications.PlayerID = characters.PlayerID AND characteritemmodifications.CharacterIndex 
                = characters.CharacterIndex AND characters.PlayerID = '" . $playerID . "' AND characters.Deleted = false";
    $data .= GetData($query, false, false);

    



    //Equipped Items
    $query = "SELECT characters.CharacterIndex, characterequippeditems.ItemID, characterequippeditems.ArmorSlot from characters JOIN characterequippeditems ON 
                characterequippeditems.PlayerID = characters.PlayerID AND characterequippeditems.CharacterIndex = characters.CharacterIndex AND characters.PlayerID 
                = '" . $playerID . "' AND characters.Deleted = false";
    $data .= GetData($query, false, false);

    $data .= "*";


    echo encrypt($data);




    function GetData($query, $fieldCount, $rowCount)
    {
        //Mark the begining of the query and get the results
        $data = "*";
        $result = mysql_query($query);

        if($result)
        {
            $num_rows = mysql_num_rows($result);

            if($num_rows == 0)
            {
                $data .= DELIMITER;
                return $data;
            }

            $numFields = mysql_num_fields($result);

            //Output the field count if true
            if($fieldCount)
            {
            
                $data .= $numFields . DELIMITER . "*";
            }


            //Output the row count if true
            if($rowCount)
            {
                $num_rows = mysql_num_rows($result);
                $data .= $num_rows . DELIMITER . "*";
            }


            //Loop through the result array and return the data
            while ($row = mysql_fetch_row($result))
            {
                for($i = 0; $i < $numFields; $i++)
                {
                    $data .= $row[$i] . DELIMITER;
                }
            }
        }
        else
        {
            $data = "";
        }
        
        
        return $data;
    }




?>
