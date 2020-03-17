<?php

namespace Acme\Entity\Member;

/**
 * Class MemberDTO
 * @package Acme\Entity\Member
 */
class MemberDTO
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var bool */
    public $active;

    /**
     * @param array $arr
     * @return MemberDTO
     */
    public static function fromArray(array $arr): MemberDTO
    {
        $member = new MemberDTO();

        if (!empty($arr['id'])) {
            $member->id = $arr['id'];
        }
        if (!empty($arr['name'])) {
            $member->name = $arr['name'];
        }
        if (!empty($arr['active'])) {
            $member->active = !!$arr['active'];
        }

        return $member;
    }
}