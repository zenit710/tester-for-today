<?php

namespace Acme\Entity\Member;

/**
 * Class MemberFilter
 * @package Acme\Entity\Member
 */
class MemberFilter
{
    /** @var bool|null */
    private $active = null;

    /**
     * @return bool|null
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function hasActive(): bool
    {
        return !is_null($this->active);
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isAdjusted(): bool
    {
        return $this->hasActive();
    }

    public function reset()
    {
        $this->active = null;
    }

    /**
     * @return string
     */
    public function toWhereClause(): string
    {
        $where = '';

        if ($this->hasActive()) {
            $where = 'WHERE active=' . ($this->getActive() ? 1 : 0);
        }

        return $where;
    }
}