<?php

namespace Service\Mail;

use Acme\Service\Mail\MailService;
use PHPUnit\Framework\TestCase;

class MailServiceTest extends TestCase
{
    public function testCreate()
    {
        $user = 'abc@gmail.com';
        $userName = 'ABC';
        $pass = 'secret';

        $service = new MailService($user, $userName, $pass);
        $mail = $service->create();

        $this->assertSame($user, $mail->From);
        $this->assertSame($userName, $mail->FromName);
        $this->assertSame($pass, $mail->Password);
        $this->assertSame($user, $mail->Username);
    }
}
