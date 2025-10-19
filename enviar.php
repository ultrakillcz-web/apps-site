<?php
 
				require 'PHPMailerAutoload.php';
 
//A partir daqui temos o código PHP para conexão ao server este código utilza PHP Mailer 5.2
 
$mail = new PHPMailer;
 
 
//$mail->SMTPDebug = 3;                 // Habilita modo debug na saída
$mail->isSMTP();                        // Setar o uso do SMTP
 
$mail->Host = 'smtp.smallsites.com.br';  	// Servidor smtp
//Para cPanel: 'mail.dominio.com.br' ou 'localhost';
//Para Plesk 7 / 8 : 'smtp.dominio.com.br' ou 'localhost';
//Para Plesk 11 / 11.5: 'smtp.dominio.com.br';
 
$mail->SMTPAuth = true;                 // Habilita a autenticação do form
$mail->Username = 'contato@smallsites.com.br';       // Conta de e-mail que realizará o envio
$mail->Password = 'Small@123';       // Senha da conta de e-mail
 
//$mail->SMTPSecure = 'tls';            // Habilitar uso do TLS (plesk 11.5 Linux ou utilizando contas do Gmail)
 
$mail->Port = 587;                       // Porta de conexão
$mail->From = 'contato@smallsites.com.br'; 			// OBRIGATÓRIO: o e-mail From deve ser o mesmo de "username" (contadeEmail)
$mail->FromName = ($_POST['nome']);
$mail->addAddress('mateus@agenciasmall.com.br');
$mail->addReplyTo($_POST['email']); 
//$mail->addCC($_POST['emailDes']);
//$mail->addBCC('bcc@example.com'); // Adicionar cópia oculta para o recebimento.
 
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Caso queira anexar um arquivo,(imagem,zip, pdf e outros), use está linha de comado.
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); 
$mail->isHTML(true);                                  // Set email format to HTML
 
$mail->Subject = ($_POST['assunto']);
$mail->Body    = ($_POST['corpo']); 
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
   echo "$var <br>";
 
if(!$mail->send()) {
    echo "$var <br>";
    echo '    Mensagem nao enviada, por favor tente novamente mais tarde';
    echo "$var <br>";
    echo     ' Mailer Error_log:  ' .  $mail->ErrorInfo;
} else {
     echo "<script>window.location='index.html';alert('Mensagem enviada com sucesso! Obrigado pelo contato.');</script>"; 
    // echo 'Mensagem enviada com sucesso! =D';
    //echo "<meta http-equiv='Refresh' content='1';URL=index.php>";
}
 
?>