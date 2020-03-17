<?php

namespace Acme\Command\Member;

use Acme\Command\AbstractCommand;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepositoryInterface;

/**
 * Class MemberList
 * @package Acme\Command\Member
 */
class MemberList extends AbstractCommand
{
    const NO_MEMBERS_MESSAGE = 'There are no members!' . PHP_EOL;

    /** @var string */
    protected $commandName = 'member:list';

    /** @var MemberRepositoryInterface */
    private $repository;

    /**
     * MemberList constructor.
     * @param MemberRepositoryInterface $repository
     */
    public function __construct(MemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function run(array $args): string
    {
        $this->mapArgs($args);

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $members = $this->repository->getAll();

        return $this->castMembersArrayToString($members);
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Print members list' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

    /**
     * @param MemberDTO[] $members
     * @return string
     */
    private function castMembersArrayToString(array $members): string
    {
        if (count($members) === 0) {
            return self::NO_MEMBERS_MESSAGE;
        }

        $columnsSizes = [
            'id' => 2,
            'name' => 4,
            'status' => 8,
        ];

        foreach ($members as $member) {
            $idLength = strlen(strval($member->id));
            $nameLength = strlen($member->name);

            if ($idLength > $columnsSizes['id']) {
                $columnsSizes['id'] = $idLength;
            }

            if ($nameLength > $columnsSizes['name']) {
                $columnsSizes['name'] = $nameLength;
            }
        }

        $outputArray = [
            [
                str_pad('id', $columnsSizes['id']),
                str_pad('name', $columnsSizes['name']),
                str_pad('status', $columnsSizes['status'])
            ]
        ];

        foreach ($members as $member) {
            $outputArray[] = [
                str_pad($member->id, $columnsSizes['id']),
                str_pad($member->name, $columnsSizes['name']),
                str_pad($member->active ? 'active' : 'inactive', $columnsSizes['status'])
            ];
        }

        $output = '';

        foreach ($outputArray as $entry) {
            $output .= join(" | ", $entry) . PHP_EOL;
        }

        return $output;
    }
}