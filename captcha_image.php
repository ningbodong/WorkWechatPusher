<?php
session_start();

header("Content-type: image/png");

$captcha = $_SESSION["captcha"];

$image = imagecreate(100, 40);
$bgColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 5, 20, 10, $captcha, $textColor);
imagepng($image);
imagedestroy($image);
?>
