<?php

namespace Acme;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class Mail
 * @package Acme
 */
class Mail extends PHPMailer
{
    const SENDER_MAIL = 'entertainment.tester@gmail.com';
    const SENDER_NAME = 'Entertainment Tester';
    const PASS = 'zaq1@WSX';

    /**
     * Mail constructor.
     */
    public function __construct() {
        parent::__construct();
        
        $this->isSMTP();
        $this->Host = 'smtp.gmail.com';
        $this->SMTPAuth = true;
        $this->Username = self::SENDER_MAIL;
        $this->Password = self::PASS;
        $this->SMTPSecure = 'tls';
        $this->Port = 587;
        $this->CharSet = 'UTF-8';
        $this->From = self::SENDER_MAIL;
        $this->FromName = self::SENDER_NAME;
    }
}