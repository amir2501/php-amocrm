<?php



function Correct_user_phone_number($arr)
{
    return array_map(function ($n) {
        $n = preg_replace('/[\s+\-\(\)]/', '', $n);

        if (strlen($n) >= 13 || strlen($n) < 9) {
            return "Invalid phone number: $n \n";
        } else if (strlen($n) == 9) {
            return 'Updated phone number: 998' . $n . "\n";
        } else {
            return "Correct phone number: $n \n";
        }


    }, $arr);

}

$result = Correct_user_phone_number(["9 -458148", "9",  "99897", "99897", "+998958148", "998975 458148", "998975458148", "998975458148", "998975458148", "975458148", "98-444-05-05"]);
echo implode(" ,  \n ", $result);
?>