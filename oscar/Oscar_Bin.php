<?php
/**
 * Description of Oscar_Bin
 * 
 * Simple library to work with binary values 
 *
 * @author cdesaintleger
 */
class Oscar_Bin {
    
    /*
     * 1. This function takes a decimal, converts it to binary and returns the
     *    decimal values of each individual binary value (a 1) in the binary string.
     *    You can use larger decimal values if you pass them to the function as a string!
     * 2. The second optional parameter reverses the output.
     * 3. The third optional parameter inverses the binary string, eg 101 becomes 010.
     *    -- darkshad3 at yahoo dot com
     */
    public static function bindecValues($decimal, $reverse=false, $inverse=false) {
    
        $bin = decbin($decimal);
        if ($inverse) {
            $bin = str_replace("0", "x", $bin);
            $bin = str_replace("1", "0", $bin);
            $bin = str_replace("x", "1", $bin);
        }
        $total = strlen($bin);

        $stock = array();

        for ($i = 0; $i < $total; $i++) {
            if ($bin{$i} != 0) {
                $bin_2 = str_pad($bin{$i}, $total - $i, 0);
                array_push($stock, bindec($bin_2));
            }
        }

        $reverse ? rsort($stock):sort($stock);
        
        return $stock;
    }
}

?>
