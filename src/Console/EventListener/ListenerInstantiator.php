<?php

namespace Lmc\Steward\Console\EventListener;

use Nette\Reflection\AnnotationsParser;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Search and instantiate event-listeners for the commands
 */
class ListenerInstantiator
{
    /** @var string Searched pattern for listeners, used to exclude all other paths and speed up the search */
    protected $searchPathPattern = 'src/Console/EventListener';

    /**
     * Instantiate listeners in given directory and register them to given dispatcher
     */
    public function instantiate(EventDispatcher $dispatcher, string $dirToSearchForListeners)
    {
        $listeners = $this->searchListeners($dirToSearchForListeners);

        foreach ($listeners as $listener) {
            $r = new \ReflectionClass($listener);
            if ($r->implementsInterface('Symfony\\Component\\EventDispatcher\\EventSubscriberInterface')
                && !$r->isAbstract()
            ) {
                /** @var EventSubscriberInterface $listenerInstance */
                $listenerInstance = $r->newInstanceWithoutConstructor();
                $dispatcher->addSubscriber($listenerInstance);
            }
        }
    }

    /**
     * @return array Array of listener class names
     */
    protected function searchListeners(string $dir): array
    {
        /** @var Finder $files */
        $files = (new Finder())
            ->files()
            ->in($dir)
            ->path($this->searchPathPattern)
            ->name('*Listener.php');

        $listeners = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $listeners[] = key(AnnotationsParser::parsePhp(\file_get_contents($file->getRealPath())));
        }

        return $listeners;
    }

    /**
     * Set searched pattern path for event listeners.
     *
     * @internal Should be only overridden in testing.
     */
    public function setSearchPathPattern(string $searchPathPattern)
    {
        $this->searchPathPattern = $searchPathPattern;
    }
}
