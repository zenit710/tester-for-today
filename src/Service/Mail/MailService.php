<?php

namespace Acme\Service\Mail;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class MailService
 * @package Acme\Service
 */
class MailService implements MailServiceInterface
{
    /** @var string */
    private $user;

    /** @var string */
    private $name;

    /** @var string */
    private $pass;

    /**
     * MailService constructor.
     * @param string $user
     * @param string $name
     * @param string $pass
     */
    public function __construct(string $user, string $name, string $pass)
    {
        $this->user = $user;
        $this->name = $name;
        $this->pass = $pass;
    }

    /**
     * @inheritDoc
     */
    public function create(): PHPMailer
    {
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $this->user;
        $mail->Password = $this->pass;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->From = $this->user;
        $mail->FromName = $this->name;

        return $mail;
    }
}