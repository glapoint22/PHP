<?php



    require_once("../include/config.php");

    $data = "";


    $result = mysql_query("SELECT ID, RaceName, RaceDescription, Team from races ORDER BY ID");

    $numFields = mysql_num_fields($result);

    //Loop through the result array and return the data
    while ($row = mysql_fetch_array($result, MYSQL_NUM))
    {
        for($i = 0; $i < $numFields; $i++)
        {
            $data .= $row[$i] . "|";
        }
    }

    echo encrypt($data);



?>
