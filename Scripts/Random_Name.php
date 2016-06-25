<?php
    require_once("../include/config.php");


    $count = 1;
    $characterName = "";

    //Generate a random name
    //We then check to see if that name is not taken.
    //If it is taken, we keep looping until it generates a unique name
    while($count == 1)
    {
        $characterName = GetRandomName();

        $result = mysql_query("SELECT COUNT(CharacterName) as count FROM characters WHERE CharacterName = '" . $characterName . "'");
    
        $row = mysql_fetch_assoc($result);
        $count = $row["count"];
    }

    //Return the name
    echo encrypt($characterName);



    function GetRandomName()
    {
        $characterName = "";

        //Get a random ammount of syllables the name will have
        $numSyllables = rand(2, 4);

        //Create the name by generating random syllables and piecing them together
        for ($i = 0; $i < $numSyllables; $i++)
        {
            $characterName = $characterName . GetSyllable();
        }

        //Return the name with the first letter uppercase
        return trim(ucfirst($characterName));
    }



    function GetSyllable()
    {
        //The vowels
        $vowels = array( "a", "e", "i", "o", "u");

        //the consonants
        $consonants = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "y", "z");
                                
        $syllable = "";

        //Is there an onset to this syllable? If so, grab a random consonant
        $onset = rand(0, 1);
        if ($onset == 1)
        {
            $syllable = $consonants[rand(0, count($consonants)-1)];
        }

        //Get a random vowel
        $syllable = $syllable . $vowels[rand(0, count($vowels)-1)];

        //Is there a coda to this syllable? If so, grab a random consonant
        $coda = rand(0, 1);
        if ($coda == 1)
        {
            $syllable = $syllable . $consonants[rand(0, count($consonants)-1)];
        }


        //Return the syllable
        return $syllable;
    }
?>
		
	
