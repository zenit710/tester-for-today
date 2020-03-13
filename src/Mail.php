<?php

namespace Acme;

use PHPMailer;

/**
 * Class Mail
 * @package Acme
 */
class Mail
{
    const SENDER_MAIL = 'entertainment.tester@gmail.com';
    const SENDER_NAME = 'Entertainment Tester';
    const PASS = 'zaq1@WSX';

    /** @var PHPMailer */
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = self::SENDER_MAIL;
        $this->mailer->Password = self::PASS;
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom(self::SENDER_MAIL, self::SENDER_NAME);
    }

    public function send($subject, $message, array $to) {
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $message;

        foreach ($to as $address) {
            $this->mailer->addBCC($address);
        }

        $this->mailer->send();
    }
}