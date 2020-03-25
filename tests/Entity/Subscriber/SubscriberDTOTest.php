<?php

namespace Entity\Subscriber;

use Acme\Entity\Subscriber\SubscriberDTO;
use PHPUnit\Framework\TestCase;

class SubscriberDTOTest extends TestCase
{
    public function testFromArray()
    {
        $subscriberData = [
            'id' => 1,
            'email' => 'janusz@nosacz.pl',
            'active' => 0
        ];

        $subscriber = SubscriberDTO::fromArray($subscriberData);

        $this->assertSame($subscriberData['id'], $subscriber->id);
        $this->assertSame($subscriberData['email'], $subscriber->email);
        $this->assertFalse($subscriber->active);
    }
}
