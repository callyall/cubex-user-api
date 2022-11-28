<?php

namespace UserApi\DependencyResolver;

use Cubex\Cubex;
use Exception;
use ReflectionClass;

interface DependencyResolverInterface
{
  public function __construct(Cubex $cubex);

  /**
   * @param ReflectionClass $class
   * @param string|null     $varName
   *
   * @return string|null
   * @throws Exception
   */
  public function getAvailableAbstractForClass(ReflectionClass $class, ?string $varName = null): ?string;

  /**
   * @param string $name
   *
   * @return mixed|null
   * @throws Exception
   */
  public function getAvailableByName(string $name);

  /**
   * @param ReflectionClass $class
   * @param string          $method
   *
   * @return array
   * @throws Exception
   */
  public function getDependencyInstances(ReflectionClass $class, string $method = '__construct'): array;
}
