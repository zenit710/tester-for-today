<?php

namespace Entity\Member;

use Acme\Entity\Member\MemberFilter;
use PHPUnit\Framework\TestCase;

class MemberFilterTest extends TestCase
{
    public function testToWhereClause()
    {
        $filter = new MemberFilter();

        $this->assertSame('', $filter->toWhereClause());

        $filter->setActive(false);
        $this->assertSame('WHERE active=0', $filter->toWhereClause());
    }
}
