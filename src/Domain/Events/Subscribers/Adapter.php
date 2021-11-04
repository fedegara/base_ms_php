<?php


namespace App\Domain\Events\Subscribers;


use Cratia\ORM\DBAL\Adapter\Events\Events;
use Cratia\Rest\Dependencies\DebugBag;
use Doctrine\Common\EventSubscriber;
use stdClass;

/**
 * Class Adapter
 * @package App\Domain\Events\Subscribers
 */
class Adapter implements EventSubscriber
{
    /**
     * @var DebugBag
     */
    private $debugBag;


    public function __construct(DebugBag $debugBag)
    {
        $this->debugBag = $debugBag;
    }

    public function __call($name, $arguments)
    {
        $attach = new stdClass();
        $attach->event = $name;
        $attach->payload = $arguments;
        $this->debugBag->attach($attach);
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return
            [
                Events::ON_AFTER_QUERY,
                Events::ON_BEFORE_QUERY,
            ];
    }
}