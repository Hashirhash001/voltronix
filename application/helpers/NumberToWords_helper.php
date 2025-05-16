<?php

// Define the helper function outside to avoid redeclaration
function convert_integer_to_words($num, $dictionary, $hyphen, $conjunction, $separator) {
    if ($num == 0) {
        return $dictionary[0];
    }

    $string = '';

    switch (true) {
        case $num < 21:
            $string = $dictionary[$num];
            break;
        case $num < 100:
            $tens = ((int) ($num / 10)) * 10;
            $units = $num % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $num < 1000:
            $hundreds = (int) ($num / 100);
            $remainder = $num % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_integer_to_words($remainder, $dictionary, $hyphen, $conjunction, $separator);
            }
            break;
        default:
            $baseUnit = 0;
            foreach ($dictionary as $base => $word) {
                if ($base > 100 && $num >= $base) {
                    $baseUnit = $base;
                }
            }
            if ($baseUnit) {
                $numBaseUnits = (int) ($num / $baseUnit);
                $remainder = $num % $baseUnit;
                $string = convert_integer_to_words($numBaseUnits, $dictionary, $hyphen, $conjunction, $separator) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= convert_integer_to_words($remainder, $dictionary, $hyphen, $conjunction, $separator);
                }
            }
            break;
    }

    return $string;
}

function convert_number_to_words($number) {
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    // Handle negative numbers
    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    // Split into integer and decimal parts
    $number = (float) $number;
    $integerPart = (int) $number;
    $decimalPart = round(($number - $integerPart) * 100); // Convert to fils (2 decimal places)

    // Convert the integer part
    $string = convert_integer_to_words($integerPart, $dictionary, $hyphen, $conjunction, $separator);

    // Prepend "DIRHAMS" only once
    $string = 'DIRHAMS ' . ucfirst($string);

    // Handle decimal part (fils)
    if ($decimalPart > 0) {
        $decimalString = '';
        if ($decimalPart < 21) {
            $decimalString = $dictionary[$decimalPart];
        } else {
            $tens = ((int) ($decimalPart / 10)) * 10;
            $units = $decimalPart % 10;
            $decimalString = $dictionary[$tens];
            if ($units) {
                $decimalString .= $hyphen . $dictionary[$units];
            }
        }
        $string .= $decimal . $decimalString . ' FILS';
    }

    return $string;
}
?>
