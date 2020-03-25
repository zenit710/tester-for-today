<?php

namespace Entity\Member;

use Acme\Entity\Member\MemberDTO;
use PHPUnit\Framework\TestCase;

class MemberDTOTest extends TestCase
{
    public function testFromArray()
    {
        $memberData = [
            'id' => 1,
            'name' => 'Janusz',
            'active' => 0
        ];

        $member = MemberDTO::fromArray($memberData);

        $this->assertSame($memberData['id'], $member->id);
        $this->assertSame($memberData['name'], $member->name);
        $this->assertFalse($member->active);
    }
}
