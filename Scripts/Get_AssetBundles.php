<?php
    require_once("../include/config.php");

    //Get the data
    $query = "SELECT * FROM AssetBundles";
    
    //Get the result
    $result = mysqli_query($dbc, $query);
    $data = "";

    while ($row = mysqli_fetch_row($result))
    {
        $data .= $row[0] . "|" . $row[1] . "|";
    }

    echo encrypt($data);

?>
            
