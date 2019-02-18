<?php

function envia_Email($smtp_servidor,$smtp_porta,$smtp_email,$smtp_senha,$destino,$assunto,$mensagem,$tipo) {

require_once('classe.phpmailer.php');

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Host = $smtp_servidor;
$mail->Port = $smtp_porta;
$mail->SMTPAuth = true;
$mail->Username = $smtp_email;
$mail->Password = $smtp_senha;

$mail->From = $smtp_email;
$mail->FromName = "Streaming";
$mail->AddAddress($destino);

$mail->IsHTML($tipo); // set email format to HTML

$mail->Subject = $assunto;
$mail->Body = $mensagem;

if(!$mail->Send()){
return $mail->ErrorInfo;
}

}
?>