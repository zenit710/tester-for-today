<?php

namespace Entity\Absence;

use Acme\Entity\Absence\AbsenceFilter;
use PHPUnit\Framework\TestCase;

class AbsenceFilterTest extends TestCase
{
    public function testToWhereClause()
    {
        $filter = new AbsenceFilter();

        $this->assertSame('', $filter->toWhereClause());

        $filter->setMemberId(1);
        $this->assertSame(' WHERE member_id = 1', $filter->toWhereClause());

        $filter->setCanceled(false);
        $this->assertSame(' WHERE canceled = 0 AND member_id = 1', $filter->toWhereClause());
    }
}
