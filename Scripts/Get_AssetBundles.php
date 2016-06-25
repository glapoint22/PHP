<?php
    require_once("../include/config.php");

    //Get the data
    $query = "SELECT * FROM AssetBundles";
    
    //Get the result
    $result = mysql_query($query);
    $data = "";

    while ($row = mysql_fetch_row($result))
    {
        $data .= $row[0] . "|" . $row[1] . "|";
    }

    echo encrypt($data);

?>
            
