<?php

namespace UserApi\Api\Entities;

class CustomerEntity implements \JsonSerializable
{
  /** @var int */
  private $id;
  /** @var string */
  private $name;

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @param int $id
   *
   * @return CustomerEntity
   */
  public function setId(int $id): CustomerEntity
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   *
   * @return CustomerEntity
   */
  public function setName(string $name): CustomerEntity
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return array
   */
  public function jsonSerialize(): array
  {
    return [
      'id'   => $this->id,
      'name' => $this->name,
    ];
  }
}
