<?php

namespace Entity\Subscriber;

use Acme\Entity\Subscriber\SubscriberFilter;
use PHPUnit\Framework\TestCase;

class SubscriberFilterTest extends TestCase
{
    public function testToWhereClause()
    {
        $filter = new SubscriberFilter();

        $this->assertSame('', $filter->toWhereClause());

        $filter->setActive(false);
        $this->assertSame('WHERE active=0', $filter->toWhereClause());
    }
}
