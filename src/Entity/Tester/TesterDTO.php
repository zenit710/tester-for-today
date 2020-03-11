<?php

namespace Acme\Entity\Tester;

/**
 * Class TesterDTO
 * @package Acme\Entity\Tester
 */
class TesterDTO
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var bool */
    public $active;

    /**
     * @param array $arr
     * @return TesterDTO
     */
    public static function fromArray(array $arr): TesterDTO {
        $tester = new TesterDTO();

        if (!empty($arr['id'])) {
            $tester->id = $arr['id'];
        }
        if (!empty($arr['name'])) {
            $tester->name = $arr['name'];
        }
        if (!empty($arr['active'])) {
            $tester->active = !!$arr['active'];
        }

        return $tester;
    }
}