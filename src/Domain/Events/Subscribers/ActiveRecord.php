<?php

declare(strict_types=1);

namespace App\Domain\Events\Subscribers;

use Cratia\ORM\Model\Events\Events;
use Cratia\Rest\Dependencies\DebugBag;
use Cratia\Rest\Dependencies\ErrorBag;
use Doctrine\Common\EventSubscriber;
use stdClass;

/**
 * Class EventSubscriberAdapter
 * @package Tests\Cratia\ORM\DBAL
 */
class ActiveRecord implements EventSubscriber
{
    /**
     * @var DebugBag
     */
    private $debugBag;

    /**
     * @var ErrorBag
     */
    private $errorBag;

    /**
     * EventSubscriberAdapter constructor.
     * @param DebugBag $debugBag
     * @param ErrorBag $errorBag
     */
    public function __construct(DebugBag $debugBag, ErrorBag $errorBag)
    {
        $this->debugBag = $debugBag;
        $this->errorBag = $errorBag;
    }

    public function __call($name, $arguments)
    {
        $attach = new stdClass();
        $attach->event = $name;
        $attach->payload = $arguments;
        $this->debugBag->attach($attach);
    }

    /**
     * @return DebugBag
     */
    public function getDebugBag(): DebugBag
    {
        return $this->debugBag;
    }

    /**
     * @return ErrorBag
     */
    public function getErrorBag(): ErrorBag
    {
        return $this->errorBag;
    }



    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return
            [
                //Events::ON_ERROR,
                Events::ON_MODEL_CREATED,
                Events::ON_MODEL_UPDATED,
                Events::ON_MODEL_DELETED,
                Events::ON_MODEL_LOADED,
                Events::ON_MODEL_READ,
            ];
    }
}
