<?php

session_start();

function generateRandomString($length = 10) {
    $characters = '0123456789' ;
    $charactersLength = strlen($characters);

    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$captcha_txt = generateRandomString(4);

$image = imagecreate(60, 23);

$a=array(
   "black"=>[255,255,255]
);

$color_index = (array_rand($a,1));

$bk_clr = imagecolorallocate($image, $a[$color_index][0], $a[$color_index][1], $a[$color_index][2]);

$txt_clr = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 20, 15, 3, $captcha_txt, $txt_clr);

$expire = gmdate(strtotime('+3 minutes', time())); 

$tmp_hash = $captcha_txt.$expire;

$_SESSION['captcha_set'] = TRUE;
$_SESSION['captcha_token'] =  md5($tmp_hash);
$_SESSION['captcha_expire'] = $expire;

imagepng($image);

imagedestroy($image);

?>