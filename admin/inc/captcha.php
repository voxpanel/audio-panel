<?php
session_start();

$codigoCaptcha = substr(md5( time()) ,0,6);

$_SESSION["captcha_login"] = $codigoCaptcha;

$imagemCaptcha = imagecreatefrompng("../img/img-fundo-captcha.png");

$fonteCaptcha = imageloadfont("fonte-captcha.gdf");

$corCaptcha = imagecolorallocate($imagemCaptcha,255,0,0);

imagestring($imagemCaptcha,5,10,5,$codigoCaptcha,$corCaptcha);

header("Content-type: image/png");

imagepng($imagemCaptcha);

imagedestroy($imagemCaptcha);

?>