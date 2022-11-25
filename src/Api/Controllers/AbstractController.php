<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use ReflectionClass;
use UserApi\Context\UserApiContext;

abstract class AbstractController extends Controller
{
  /**
   * @param UserApiContext $c
   * @param mixed          $handler
   * @param mixed          $response
   *
   * @return bool
   * @throws \ReflectionException|\Exception
   */
  protected function _processHandler($c, $handler, &$response): bool
  {
    while(is_callable($handler))
    {
      $handler = $handler(...$this->_loadDependencies($c, $handler));
    }

    return parent::_processHandler($c, $handler, $response);
  }

  /**
   * @param UserApiContext $c
   * @param callable       $handler
   *
   * @return array
   * @throws \ReflectionException
   */
  protected function _loadDependencies($c, $handler)
  {
    $params = (new \ReflectionClass($handler[0]))
      ->getMethod($handler[1])
      ->getParameters();

    $instances = [];

    foreach($params as $param)
    {
      if(!$param->getType())
      {
        $instances[] = $this->getContext()->routeData()->getAlnum($param->getName());
        continue;
      }

      $type = $param->getType()->getName();
      if($this->getContext() instanceof $type)
      {
        $instances[] = $this->getContext();
      }
      else
      {
        if($param->getType()->isBuiltin())
        {
          switch($param->getType()->getName())
          {
            case 'int':
              $instances[] = $this->getContext()->routeData()->getInt($param->getName());
              break;
            case 'bool':
              $instances[] = $this->getContext()->routeData()->getBoolean($param->getName());
              break;
            case 'float':
              $instances[] = $this->getContext()->routeData()->getDigits($param->getName());
              break;
            case 'string':
            default:
              $instances[] = $this->getContext()->routeData()->getAlnum($param->getName());
          }
        }
        else
        {
          $className = $param->getType()->getName();
          if(!$c->getCubex()->isAvailable($className))
          {
            $reflection = new ReflectionClass($className);

            $className = $reflection->getParentClass()
              ? $reflection->getParentClass()->getName()
              : $reflection->getInterfaceNames()[0];
          }
          $instances[] = $c->getCubex()->retrieve($className, [$param->getType()->getName()]);
        }
      }
    }

    return $instances;
  }
}
