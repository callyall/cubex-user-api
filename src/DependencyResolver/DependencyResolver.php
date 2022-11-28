<?php

namespace UserApi\DependencyResolver;

use Cubex\Cubex;
use Exception;
use ReflectionClass;

class DependencyResolver implements DependencyResolverInterface
{
  /**
   * @var Cubex
   */
  private $_cubex;

  public function __construct(Cubex $cubex)
  {
    $this->_cubex = $cubex;
  }

  /**
   * @param ReflectionClass $class
   * @param string|null     $varName
   *
   * @return string|null
   * @throws Exception
   */
  public function getAvailableAbstractForClass(ReflectionClass $class, ?string $varName = null): ?string
  {
    $parent = $class;

    while ($parent !== false)
    {
      if($this->_cubex->isAvailable($parent->getName()))
      {
        return $parent->getName();
      }

      $parent = $parent->getParentClass();
    }

    foreach($class->getInterfaceNames() as $interfaceName)
    {
      if($this->_cubex->isAvailable($interfaceName))
      {
        return $interfaceName;
      }
    }

    return $varName ? $this->getAvailableByName($varName) : null;
  }

  /**
   * @param string $name
   *
   * @return mixed|null
   * @throws Exception
   */
  public function getAvailableByName(string $name)
  {
    return $this->_cubex->isAvailable($name) ? $this->_cubex->retrieve($name) : null;
  }

  /**
   * @param ReflectionClass $class
   * @param string          $method
   *
   * @return array
   * @throws Exception
   */
  public function getDependencyInstances(ReflectionClass $class, string $method = '__construct'): array
  {
    $paramInstances = [];

    if(
      !$class->hasMethod($method)
      || empty($class->getMethod($method)->getParameters())
    )
    {
      return $paramInstances;
    }

    foreach($class->getMethod($method)->getParameters() as $constructorParam)
    {
      if(!$constructorParam->getType() || $constructorParam->getType()->isBuiltin())
      {
        $value = $this->getAvailableByName($constructorParam->getName());

        if(!$value && !$constructorParam->isOptional())
        {
          throw new Exception("Missing dependency {$constructorParam->getName()}");
        }

        $paramInstances[] = $value;
      }
      else
      {
        $abstract = $this->getAvailableAbstractForClass($constructorParam->getClass(), $constructorParam->getName());

        if(!$abstract && !$constructorParam->isOptional())
        {
          throw new Exception("Missing dependency {$constructorParam->getType()->getName()} {$constructorParam->getName()}");
        }

        $paramInstances[] = $this->_cubex->retrieve($abstract, [$constructorParam->getType()->getName()]);
      }
    }

    return $paramInstances;
  }
}
