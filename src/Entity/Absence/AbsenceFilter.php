<?php

namespace Acme\Entity\Absence;

/**
 * Class AbsenceFilter
 * @package Acme\Entity\Absence
 */
class AbsenceFilter
{
    /** @var bool|null */
    private $canceled = null;

    /** @var string|null */
    private $startsFrom;

    /** @var string|null */
    private $startsTo;

    /** @var string|null */
    private $endsFrom;

    /** @var string|null */
    private $endsTo;

    /** @var int|null */
    private $memberId;

    /** @var bool  */
    private $pristineWhere = true;

    /**
     * @return bool|null
     */
    public function getCanceled()
    {
        return $this->canceled;
    }

    /**
     * @return bool
     */
    public function hasCanceled(): bool
    {
        return !is_null($this->canceled);
    }

    /**
     * @param bool $canceled
     */
    public function setCanceled(bool $canceled)
    {
        $this->canceled = $canceled;
    }

    /**
     * @return string|null
     */
    public function getStartsFrom()
    {
        return $this->startsFrom;
    }

    /**
     * @return bool
     */
    public function hasStartsFrom(): bool
    {
        return !is_null($this->startsFrom);
    }

    /**
     * @param string $startsFrom
     */
    public function setStartsFrom(string $startsFrom)
    {
        $this->startsFrom = $startsFrom;
    }

    /**
     * @return string|null
     */
    public function getStartsTo()
    {
        return $this->startsTo;
    }

    /**
     * @return bool
     */
    public function hasStartsTo(): bool
    {
        return !is_null($this->startsTo);
    }

    /**
     * @param string $startsTo
     */
    public function setStartsTo(string $startsTo)
    {
        $this->startsTo = $startsTo;
    }

    /**
     * @return string|null
     */
    public function getEndsFrom()
    {
        return $this->endsFrom;
    }

    /**
     * @return bool
     */
    public function hasEndsFrom(): bool
    {
        return !is_null($this->endsFrom);
    }

    /**
     * @param string $endsFrom
     */
    public function setEndsFrom(string $endsFrom)
    {
        $this->endsFrom = $endsFrom;
    }

    /**
     * @return string|null
     */
    public function getEndsTo()
    {
        return $this->endsTo;
    }

    /**
     * @return bool
     */
    public function hasEndsTo(): bool
    {
        return !is_null($this->endsTo);
    }

    /**
     * @param string $endsTo
     */
    public function setEndsTo(string $endsTo)
    {
        $this->endsTo = $endsTo;
    }

    /**
     * @return int|null
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @return bool
     */
    public function hasMemberId(): bool
    {
        return !is_null($this->memberId);
    }

    /**
     * @param int $memberId
     */
    public function setMemberId(int $memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * @return bool
     */
    public function isAdjusted(): bool
    {
        return $this->hasCanceled()
            || $this->hasStartsFrom()
            || $this->hasStartsTo()
            || $this->hasEndsFrom()
            || $this->hasEndsTo()
            || $this->hasMemberId();
    }

    public function reset()
    {
        $this->canceled = null;
        $this->startsFrom = null;
        $this->startsTo = null;
        $this->memberId = null;
    }

    /**
     * @return string
     */
    public function toWhereClause(): string
    {
        $this->pristineWhere = true;
        $where = '';

        if (!$this->isAdjusted()) {
            return $where;
        }

        if ($this->hasCanceled()) {
            $where .= $this->getNextWhere(' canceled = ' . ($this->getCanceled() ? 1 : 0));
        }

        if ($this->hasStartsFrom()) {
            $where .= $this->getNextWhere(' date_from >= "' . $this->getStartsFrom() . '"');
        }

        if ($this->hasStartsTo()) {
            $where .= $this->getNextWhere(' date_from <= "' . $this->getStartsTo() . '"');
        }

        if ($this->hasEndsFrom()) {
            $where .= $this->getNextWhere(' date_to >= "' . $this->getEndsFrom() . '"');
        }

        if ($this->hasEndsTo()) {
            $where .= $this->getNextWhere(' date_to <= "' . $this->getEndsTo() . '"');
        }

        if ($this->hasMemberId()) {
            $where .= $this->getNextWhere(' member_id = ' . $this->getMemberId());
        }

        return $where;
    }

    /**
     * @param string $clause
     * @return string
     */
    private function getNextWhere(string $clause): string
    {
        $where = $this->pristineWhere ? ' WHERE' : ' AND';
        $this->pristineWhere = false;

        return $where . $clause;
    }
}