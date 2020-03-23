<?php

namespace Acme\Service\Mail;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Interface MailServiceInterface
 * @package Acme\Service\Mail
 */
interface MailServiceInterface
{
    /**
     * @return PHPMailer
     */
    public function create(): PHPMailer;
}