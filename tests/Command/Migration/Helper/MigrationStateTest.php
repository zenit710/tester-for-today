<?php

namespace Command\Migration\Helper;

use Acme\Command\Migration\Helper\MigrationState;
use Acme\Migration\Migration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MigrationStateTest extends TestCase
{
    /** @var MigrationState */
    private $state;

    /** @var \ReflectionProperty */
    private $failed;

    /** @var \ReflectionProperty */
    private $successful;

    /** @var MockObject */
    private $migMock;

    protected function setUp()
    {
        $this->state = new MigrationState();
        $this->failed = new \ReflectionProperty(MigrationState::class, 'failed');
        $this->failed->setAccessible(true);
        $this->successful = new \ReflectionProperty(MigrationState::class, 'successful');
        $this->successful->setAccessible(true);
        $this->migMock = $this->createMock(Migration::class);
    }

    public function testAddFailed()
    {
        $this->assertCount(0, $this->failed->getValue($this->state));

        $this->state->addFailed($this->migMock);
        $this->assertCount(1, $this->failed->getValue($this->state));
    }

    public function testHasSuccessful()
    {
        $this->assertFalse($this->state->hasSuccessful());

        $this->successful->setValue($this->state, [$this->migMock]);
        $this->assertTrue($this->state->hasSuccessful());
    }

    public function testGetSuccessful()
    {
        $this->assertCount(0, $this->state->getSuccessful());

        $this->successful->setValue($this->state, [$this->migMock]);
        $this->assertCount(1, $this->state->getSuccessful());
    }

    public function testHasFailed()
    {
        $this->assertFalse($this->state->hasFailed());

        $this->failed->setValue($this->state, [$this->migMock]);
        $this->assertTrue($this->state->hasFailed());
    }

    public function testAddSuccessful()
    {
        $this->assertCount(0, $this->successful->getValue($this->state));

        $this->state->addSuccessful($this->migMock);
        $this->assertCount(1, $this->successful->getValue($this->state));
    }

    public function testGetFailed()
    {
        $this->assertCount(0, $this->state->getFailed());

        $this->failed->setValue($this->state, [$this->migMock]);
        $this->assertCount(1, $this->state->getFailed());
    }

    public function test__toString()
    {
        $this->assertSame('No migrations made' . PHP_EOL, $this->state->__toString());

        $this->migMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('test_migration');

        $this->successful->setValue($this->state, [$this->migMock]);
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'Successful migrations'));
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'test_migration'));
        $this->assertFalse(strpos($this->state->__toString(), 'Failed migrations'));
        $this->assertGreaterThanOrEqual(1, substr_count($this->state->__toString(), 'test_migration'));

        $this->failed->setValue($this->state, [$this->migMock]);
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'Successful migrations'));
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'test_migration'));
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'Failed migrations'));
        $this->assertGreaterThanOrEqual(2, substr_count($this->state->__toString(), 'test_migration'));

        $this->successful->setValue($this->state, []);
        $this->assertFalse(strpos($this->state->__toString(), 'Successful migrations'));
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'test_migration'));
        $this->assertGreaterThanOrEqual(0, strpos($this->state->__toString(), 'Failed migrations'));
        $this->assertGreaterThanOrEqual(1, substr_count($this->state->__toString(), 'test_migration'));

    }
}
