<?php
/**
 * WP Download Codes Plugin
 * 
 * FILE
 * includes/helpers/string.php
 *
 * DESCRIPTION
 * Additional string functions.
 *
 */
 
/**
 * Callback function to trim array value white space
 */
function dc_trim_value(&$value) 
{ 
    $value = trim($value); 
}

/**
 * Generate a random character string
 */
function rand_str( $length = 32, $chars = '' )
{
    // Character list
    if ($chars == '') $chars = dc_code_chars();

    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};
   
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};
       
        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }
   
    // Return the string
    return $string;
}
?>