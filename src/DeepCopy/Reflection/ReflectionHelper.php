<?php

namespace DeepCopy\Reflection;

use DeepCopy\Exception\PropertyException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ReflectionHelper
{
    /**
     * @var ContainerInterface
     */
    protected $ctn;

    /**
     * JavascriptTester constructor.
     *
     * @param ContainerInterface $ctn
     */
    public function __construct(ContainerInterface $ctn)
    {
        $this->ctn = $ctn;
    }

    /**
     * Retrieves all properties (including private ones), from object and all its ancestors.
     *
     * Standard \ReflectionClass->getProperties() does not return private properties from ancestor classes.
     *
     * @author muratyaman@gmail.com
     * @see http://php.net/manual/en/reflectionclass.getproperties.php
     *
     * @param \ReflectionClass $ref
     * @return \ReflectionProperty[]
     */
    public static function getProperties(\ReflectionClass $ref)
    {
        $props = $ref->getProperties();
        $propsArr = array();

        foreach ($props as $prop) {
            $propertyName = $prop->getName();
            $propsArr[$propertyName] = $prop;
        }

        if ($parentClass = $ref->getParentClass()) {
            $parentPropsArr = self::getProperties($parentClass);
            foreach ($propsArr as $key => $property) {
                $parentPropsArr[$key] = $property;
            }

            return $parentPropsArr;
        }

        return $propsArr;
    }

    /**
     * Retrieves a JSON object as array from an URL.
     *
     * @param string $url
     *
     * @return \ReflectionClass
     * @throws PropertyException
     */
    public function getHttpReflection($url)
    {
        // TODO

        return null;
    }

    /**
     * Retrieves property by name from object and all its ancestors.
     *
     * @param object|string $object
     * @param string $name
     *
     * @return \ReflectionProperty
     * @throws PropertyException
     */
    public static function getProperty($object, $name)
    {
        $reflection = is_object($object) ? new \ReflectionObject($object) : new \ReflectionClass($object);
        if ($reflection->hasProperty($name)) {
            return $reflection->getProperty($name);
        }

        if ($parentClass = $reflection->getParentClass()) {
            return self::getProperty($parentClass->getName(), $name);
        }

        throw new PropertyException(
            sprintf(
                'The class "%s" doesn\'t have a property with the given name: "%s"',
                is_object($object) ? get_class($object) : $object,
                $name
            )
        );
    }
}
