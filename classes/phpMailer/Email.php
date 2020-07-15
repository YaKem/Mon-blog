<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 * This uses traditional id & password authentication - look at the gmail_xoauth.phps
 * example to see how to use XOAUTH2.
 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
 */

namespace PhpMailer;

//Import PHPMailer classes into the global namespace
use PhpMailer\PHPMailer;
use PhpMailer\SMTP;

require 'autoload.php';

class Email
{
    /**
     * @var string $mail email de destination
     */
    private $mail;

    public function __construct()
    {
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer();
    }

    /**
     * Envoie un email
     *
     * @param array $params
     * @return void
     */
    public function send(array $params): void
    {
        extract($params);
        
        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();
    
        //Enable SMTP debugging
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
    
        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
        // $this->mail->Host = 'localhost';
        // use
        // $this->mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
    
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = 587;
        // $this->mail->Port = 1025;
    
        //Set the encryption mechanism to use - STARTTLS or SMTPS
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
    
        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = 'jdoe.ad.blog@gmail.com';
    
        //Password to use for SMTP authentication
        $this->mail->Password = 'mdpjohndoe';
    
        //Set charset
        $this->mail->CharSet = 'UTF-8';
        
        //Set who the message is to be sent from
        $this->mail->setFrom('admin@myblog.com', 'My-Blog');
    
        //Set an alternative reply-to address
        // $this->mail->addReplyTo('replyto@example.com', 'First Last');
    
        //Set who the message is to be sent to
        $this->mail->addAddress($email, $name);
    
        //Set the subject line
        $this->mail->Subject = 'Réinitialisation du mot de passe';
    
        $message = file_get_contents($content);
        $message = str_replace('%token%', $token, $message);
        $message = str_replace('%email%', $email, $message);
        $message = str_replace('%name%', ucfirst(strtolower($name)), $message);

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML($message);
    
        //Replace the plain text body with one created manually
        // $this->mail->AltBody = 'This is a plain-text message body';
    
        $this->mail->AddEmbeddedImage('ASSETS/images/layout/logo.png', 'logo');

        //Attach an image file
        // $this->mail->addAttachment('ASSETS/images/mailer/phpmailer.png');
    
        //send the message, check for errors
        if (!$this->mail->send()) {
            echo 'Mailer Error: '. $this->mail->ErrorInfo;
        } else {
            echo 'Message sent!';
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    
        //Section 2: IMAP
        //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
        //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
        //You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
        //be useful if you are trying to get this working on a non-Gmail IMAP server.
    }
    
    function save_mail($mail)
    {
        //You can change 'Sent Mail' to any other folder or tag
        $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

        //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
        $imapStream = imap_open($path, $this->mail->Username, $this->mail->Password);

        $result = imap_append($imapStream, $path, $this->mail->getSentMIMEMessage());
        imap_close($imapStream);

        return $result;
    }
}