<?php
// Debug attivo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //=== VALIDAZIONE reCAPTCHA 
    $recaptchaSecret = ''; // <-- cambia questo
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $verifyResponse = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse"
    );

    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        echo "Verifica reCAPTCHA non riuscita. Riprova.";
        exit;
    }
    // === VALIDAZIONE INPUT ===
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Compila tutti i campi correttamente.";
        exit;
    }

    // === invio mailcon PHPMailer ===
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'mail13.dominiofaidate.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '1@infowebstoreaps.it';
        $mail->Password   = 'GaiaBella07!';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('1@infowebstoreaps.it', 'infowebstoreaps.it');
        $mail->addReplyTo($email, $name);
        $mail->addAddress('gaia.mazzanti07@gmail.com');

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = "Nome: $name\nEmail: $email\n\nMessaggio:\n$message";

        if ($mail->send()) {
            echo "Messaggio inviato con successo! Ti risponderemo presto.";
        } else {
            echo "Errore durante l'invio. Riprova più tardi.";
        }
    } catch (Exception $e) {
        echo "Errore: " . $mail->ErrorInfo;
    }
}