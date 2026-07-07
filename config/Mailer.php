<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Envio de email a serio via SMTP (Gmail), usando o PHPMailer.
class Mailer
{
    public static function enviar(string $paraEmail, string $paraNome, string $assunto, string $corpo): bool
    {
        $config = require __DIR__ . '/config.php';
        $email = $config['email'];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $email['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $email['utilizador'];
            $mail->Password = $email['senha'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $email['porta'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($email['utilizador'], $email['remetente_nome']);
            $mail->addAddress($paraEmail, $paraNome);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $corpo;

            $mail->send();

            return true;
        } catch (Exception $e) {
            error_log('Falha ao enviar email: ' . $mail->ErrorInfo);

            return false;
        }
    }
}
