<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Shared\Kernel;

use Spryker\Shared\Kernel\Locator\LocatorInterface;

abstract class AbstractLocator implements LocatorInterface
{

    /**
     * @var string
     */
    protected $bundle;

    /**
     * @var string
     */
    protected $layer;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var string
     */
    protected $application;

    /**
     * @throws \Exception
     */
    final public function __construct()
    {
        if ($this->application === null) {
            throw new \Exception('Properties missing for: ' . get_class($this));
        }
    }

    /**
     * @param string $bundle
     *
     * @return object
     */
    abstract public function locate($bundle);

}