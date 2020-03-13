<?php

namespace Acme\Command\Tester;

use Acme\Command\AbstractCommand;
use Acme\Entity\Tester\TesterDTO;
use Acme\Entity\Tester\TesterRepositoryInterface;

/**
 * Class TesterList
 * @package Acme\Command\Tester
 */
class TesterList extends AbstractCommand
{
    /** @var string */
    protected $commandName = 'tester:list';

    /** @var TesterRepositoryInterface */
    private $repository;

    /**
     * TesterAdd constructor.
     * @param TesterRepositoryInterface $repository
     */
    public function __construct(TesterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function run(array $args): string
    {
        $this->mapArgs($args);

        $testers = $this->repository->getAll();

        return $this->castTestersArrayToString($testers);
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

    /**
     * @param TesterDTO[] $testers
     * @return string
     */
    private function castTestersArrayToString(array $testers): string
    {
        $columnsSizes = [
            'id' => 2,
            'name' => 4,
            'status' => 8,
        ];

        foreach ($testers as $tester) {
            $idLength = strlen(strval($tester->id));
            $nameLength = strlen($tester->name);

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

        foreach ($testers as $tester) {
            $outputArray[] = [
                str_pad($tester->id, $columnsSizes['id']),
                str_pad($tester->name, $columnsSizes['name']),
                str_pad($tester->active ? 'active' : 'inactive', $columnsSizes['status'])
            ];
        }

        $output = '';

        foreach ($outputArray as $entry) {
            $output .= join(" | ", $entry) . PHP_EOL;
        }

        return $output;
    }
}