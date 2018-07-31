<?php

declare(strict_types = 1);

namespace Datalator\Popo;

/**
 * Code generated by POPO generator, do not edit.
 */
class DatabaseConfigurator 
{
    /**
     * @var array
     */
    protected $data = array (
  'connection' => NULL,
  'modules' => 
  array (
  ),
);

    /**
     * @var array
     */
    protected $default = array (
  'connection' => NULL,
  'modules' => 
  array (
  ),
);

    /**
    * @var array
    */
    protected $propertyMapping = array (
  'connection' => '\\Datalator\\Popo\\DatabaseConnectionConfigurator',
  'modules' => 'array',
);

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    protected function popoGetValue(string $property)
    {
        if (!isset($this->data[$property])) {
            return null;
        }

        return $this->data[$property];
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return void
     */
    protected function popoSetValue(string $property, $value): void
    {
        $this->data[$property] = $value;
    }

    /**
     * @return array
     */
    protected function getPropertyNames(): array
    {
        return \array_keys(
            $this->propertyMapping
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this->propertyMapping as $key => $type) {
            $data[$key] = null;

            if (isset($this->data[$key])) {
                $value = $this->data[$key];
                $data[$key] = $value;

                if (\is_object($value) && \method_exists($value, 'toArray')) {
                    $data[$key] = $value->toArray();
                }
            }
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \Datalator\Popo\DatabaseConfigurator
     */
    public function fromArray(array $data): \Datalator\Popo\DatabaseConfigurator
    {
        $result = [];
        foreach ($this->propertyMapping as $key => $type) {
            $result[$key] = null;
            if (\array_key_exists($key, $this->default)) {
                $result[$key] = $this->default[$key];
            }
            if (\array_key_exists($key, $data)) {
                $result[$key] = $data[$key];
            }

            if (\is_array($result[$key]) && \class_exists($type)) {
                $popo = new $type();
                if (\method_exists($popo, 'fromArray')) {
                    $popo->fromArray($result[$key]);
                }
                $result[$key] = $popo;
            }
        }

        $this->data = $result;

        return $this;
    }

    /**
     * @param string $property
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    protected function assertPropertyValue(string $property): void
    {
        if (!isset($this->data[$property])) {
            throw new \UnexpectedValueException(\sprintf(
                'Required value of "%s" has not been set',
                $property
            ));
        }
    }

    
    /**
     * @return \Datalator\Popo\DatabaseConnectionConfigurator|null
     */
    public function getConnection(): ?\Datalator\Popo\DatabaseConnectionConfigurator
    {
        return $this->popoGetValue('connection');
    }

    /**
     * @param \Datalator\Popo\DatabaseConnectionConfigurator|null $connection
     *
     * @return self
     */
    public function setConnection(?\Datalator\Popo\DatabaseConnectionConfigurator $connection): \Datalator\Popo\DatabaseConfigurator
    {
        $this->popoSetValue('connection', $connection);

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     *
     * @return \Datalator\Popo\DatabaseConnectionConfigurator
     */
    public function requireConnection(): \Datalator\Popo\DatabaseConnectionConfigurator
    {
        $this->assertPropertyValue('connection');

        return $this->popoGetValue('connection');
    }

    /**
     * @return array|null
     */
    public function getModules(): ?array
    {
        return $this->popoGetValue('modules');
    }

    /**
     * @param array|null $modules
     *
     * @return self
     */
    public function setModules(?array $modules): \Datalator\Popo\DatabaseConfigurator
    {
        $this->popoSetValue('modules', $modules);

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    public function requireModules(): array
    {
        $this->assertPropertyValue('modules');

        return (array)$this->popoGetValue('modules');
    }

}
