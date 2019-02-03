<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

class Registry extends \Zend_Registry
{
    /**
     * @var Container
     */
    private static $container;

    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * @param mixed $index
     * @return mixed|object
     * @throws \Exception
     */
    public function offsetGet($index)
    {
        if (self::$container === null || self::$container->has($index) === false) {
            return parent::offsetGet($index);
        }

        return self::$container->get($index);
    }

    /**
     * @param string $index
     * @return bool
     */
    public function offsetExists($index)
    {
        if (self::$container !== null && self::$container->has($index)) {
            return true;
        }

        return parent::offsetExists($index);
    }
}
