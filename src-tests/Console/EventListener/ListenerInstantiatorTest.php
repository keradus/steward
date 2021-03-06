<?php declare(strict_types=1);

namespace Lmc\Steward\Console\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @covers \Lmc\Steward\Console\EventListener\ListenerInstantiator
 */
class ListenerInstantiatorTest extends TestCase
{
    /** @var ListenerInstantiator */
    protected $instantiator;

    protected function setUp(): void
    {
        $this->instantiator = new ListenerInstantiator();
        $this->instantiator->setSearchPathPattern('Fixtures/');
    }

    public function testShouldFindAndAttachListenersToDispatcher(): void
    {
        $dispatcher = new EventDispatcher();
        // There are no listeners on new dispatcher
        $this->assertEmpty($dispatcher->getListeners());

        $this->instantiator->instantiate($dispatcher, __DIR__);

        $listeners = $dispatcher->getListeners();
        $this->assertNotEmpty($listeners);
        $this->assertArrayHasKey('foo', $listeners);
    }
}
