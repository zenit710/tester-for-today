<?php

namespace Acme\Command\Subscriber;

use Acme\Command\AbstractCommand;
use Acme\Entity\Subscriber\SubscriberDTO;
use Acme\Entity\Subscriber\SubscriberFilter;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SubscriberList
 * @package Acme\Command\Subscriber
 */
class SubscriberList extends AbstractCommand
{
    const NO_SUBSCRIBERS_MESSAGE = 'There are no subscribers!' . PHP_EOL;
    const ARG_ACTIVE = 'active';

    /** @var string */
    protected $commandName = 'subscriber:list';

    /** @var SubscriberRepositoryInterface */
    private $repository;

    /**
     * SubscriberList constructor.
     * @param LoggerInterface $logger
     * @param SubscriberRepositoryInterface $repository
     */
    public function __construct(LoggerInterface $logger, SubscriberRepositoryInterface $repository)
    {
        parent::__construct($logger);
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

        $filter = new SubscriberFilter();

        if ($this->hasArg(self::ARG_ACTIVE)) {
            $filter->setActive($this->getArg(self::ARG_ACTIVE));
        }

        $subscribers = $this->repository->getAll($filter);

        return $this->castSubscribersArrayToString($subscribers);
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Print subscribers list' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --active=[0|1] - list only active/inactive members" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

    /**
     * @param SubscriberDTO[] $subscribers
     * @return string
     */
    private function castSubscribersArrayToString(array $subscribers): string
    {
        if (count($subscribers) === 0) {
            return self::NO_SUBSCRIBERS_MESSAGE;
        }

        $columnsSizes = [
            'id' => 2,
            'email' => 5,
            'status' => 8,
        ];

        foreach ($subscribers as $subscriber) {
            $idLength = strlen(strval($subscriber->id));
            $nameLength = strlen($subscriber->email);

            if ($idLength > $columnsSizes['id']) {
                $columnsSizes['id'] = $idLength;
            }

            if ($nameLength > $columnsSizes['email']) {
                $columnsSizes['email'] = $nameLength;
            }
        }

        $outputArray = [
            [
                str_pad('id', $columnsSizes['id']),
                str_pad('email', $columnsSizes['email']),
                str_pad('status', $columnsSizes['status'])
            ]
        ];

        foreach ($subscribers as $subscriber) {
            $outputArray[] = [
                str_pad($subscriber->id, $columnsSizes['id']),
                str_pad($subscriber->email, $columnsSizes['email']),
                str_pad($subscriber->active ? 'active' : 'inactive', $columnsSizes['status'])
            ];
        }

        $output = '';

        foreach ($outputArray as $entry) {
            $output .= join(" | ", $entry) . PHP_EOL;
        }

        return $output;
    }
}