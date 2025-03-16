<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require __DIR__ . '/../../vendor/autoload.php';
    
    function sendEmail($to, $toName, $subject, $body) {
        $mail = new PHPMailer(true);
    
        try {
            // Configuration of SMTP Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aramadhana70@gmail.com'; // Replace with your Gmail email
            $mail->Password = 'ozhm lkgf hpbx ailm'; // Replace with Gmail "App Password"
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
           // Sender & Recipient ozhm lkgf hpbx ailm
            $mail->setFrom('daruma@noreply.com', 'Daruma'); // Change as needed
            $mail->addAddress($to, $toName); // Replace with destination email
    
            // Email Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $body;
    
            /// Send email
            $mail->send();
            return 'Email sent successfully!';
        } catch (Exception $e) {
            return "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    }
    