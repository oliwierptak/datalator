<?php

declare(strict_types = 1);

namespace Datalator\Popo;

/**
 * Code generated by POPO generator, do not edit.
 * https://packagist.org/packages/popo/generator
 */
class SchemaConfigurator 
{
    /**
     * @var array
     */
    protected $data = array (
  'loadedModules' => 
  array (
  ),
);

    /**
     * @var array
     */
    protected $default = array (
  'loadedModules' => 
  array (
  ),
);

    /**
     * @var array
     */
    protected $propertyMapping = array (
  'databaseConfigurator' => '\\Datalator\\Popo\\DatabaseConfigurator',
  'schemaName' => 'string',
  'sqlCreate' => 'string',
  'sqlDrop' => 'string',
  'loadedModules' => 'array',
);

    /**
     * @var array
     */
    protected $collectionItems = array (
  'databaseConfigurator' => '',
  'schemaName' => '',
  'sqlCreate' => '',
  'sqlDrop' => '',
  'loadedModules' => 'string',
);

    /**
     * @var array
     */
    protected $updateMap = [];

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    protected function popoGetValue(string $property)
    {
        if (!isset($this->data[$property])) {
            $className = trim($this->propertyMapping[$property]);
            if ($className !== ''  && class_exists($className)) {
                $this->data[$property] = new $className();
            } else {
                return null;
            }
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

        $this->updateMap[$property] = true;
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
            $data[$key] = $this->default[$key] ?? null;

            if (isset($this->data[$key])) {
                $value = $this->data[$key];

                if ($this->collectionItems[$key] !== '') {
                    if (\is_array($value) && \class_exists($this->collectionItems[$key])) {
                        foreach ($value as $popo) {
                            if (\method_exists($popo, 'toArray')) {
                                $data[$key][] = $popo->toArray();
                            }
                        }
                    }
                } else {
                    $data[$key] = $value;
                }

                if (\is_object($value) && \method_exists($value, 'toArray')) {
                    $data[$key] = $value->toArray();
                }
            }
        }

        return $data;
    }

    public function fromArray(array $data): \Datalator\Popo\SchemaConfigurator
    {
        $result = [];
        foreach ($this->propertyMapping as $key => $type) {
            $result[$key] = null;
            if (\array_key_exists($key, $this->default)) {
                $result[$key] = $this->default[$key];
            }
            if (\array_key_exists($key, $data)) {
                if ($this->isCollectionItem($key, $data)) {
                    foreach ($data[$key] as $popoData) {
                        $popo = new $this->collectionItems[$key]();
                        if (\method_exists($popo, 'fromArray')) {
                            $popo->fromArray($popoData);
                        }
                        $result[$key][] = $popo;
                    }
                } else {
                    $result[$key] = $data[$key];
                }
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

    protected function isCollectionItem(string $key, array $data): bool
    {
        return $this->collectionItems[$key] !== '' &&
            \is_array($data[$key]) &&
            \class_exists($this->collectionItems[$key]);
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
     * @param string $propertyName
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function addCollectionItem(string $propertyName, $value): void
    {
        $type = \trim(\strtolower($this->propertyMapping[$propertyName]));
        $collection = $this->popoGetValue($propertyName) ?? [];

        if (!\is_array($collection) || $type !== 'array') {
            throw new \InvalidArgumentException('Cannot add item to non array type: ' . $propertyName);
        }

        $collection[] = $value;

        $this->popoSetValue($propertyName, $collection);
    }

    
    /**
     * @return \Datalator\Popo\DatabaseConfigurator|null
     */
    public function getDatabaseConfigurator(): ?\Datalator\Popo\DatabaseConfigurator
    {
        return $this->popoGetValue('databaseConfigurator');
    }

    /**
     * @param \Datalator\Popo\DatabaseConfigurator|null $databaseConfigurator
     *
     * @return self
     */
    public function setDatabaseConfigurator(?\Datalator\Popo\DatabaseConfigurator $databaseConfigurator): \Datalator\Popo\SchemaConfigurator
    {
        $this->popoSetValue('databaseConfigurator', $databaseConfigurator);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return \Datalator\Popo\DatabaseConfigurator
     */
    public function requireDatabaseConfigurator(): \Datalator\Popo\DatabaseConfigurator
    {
        $this->assertPropertyValue('databaseConfigurator');

        return $this->popoGetValue('databaseConfigurator');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasDatabaseConfigurator(): bool
    {
        return $this->updateMap['databaseConfigurator'] ?? false;
    }

    /**
     * @return string|null
     */
    public function getSchemaName(): ?string
    {
        return $this->popoGetValue('schemaName');
    }

    /**
     * @param string|null $schemaName
     *
     * @return self
     */
    public function setSchemaName(?string $schemaName): \Datalator\Popo\SchemaConfigurator
    {
        $this->popoSetValue('schemaName', $schemaName);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requireSchemaName(): string
    {
        $this->assertPropertyValue('schemaName');

        return (string)$this->popoGetValue('schemaName');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasSchemaName(): bool
    {
        return $this->updateMap['schemaName'] ?? false;
    }

    /**
     * @return string|null
     */
    public function getSqlCreate(): ?string
    {
        return $this->popoGetValue('sqlCreate');
    }

    /**
     * @param string|null $sqlCreate
     *
     * @return self
     */
    public function setSqlCreate(?string $sqlCreate): \Datalator\Popo\SchemaConfigurator
    {
        $this->popoSetValue('sqlCreate', $sqlCreate);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requireSqlCreate(): string
    {
        $this->assertPropertyValue('sqlCreate');

        return (string)$this->popoGetValue('sqlCreate');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasSqlCreate(): bool
    {
        return $this->updateMap['sqlCreate'] ?? false;
    }

    /**
     * @return string|null
     */
    public function getSqlDrop(): ?string
    {
        return $this->popoGetValue('sqlDrop');
    }

    /**
     * @param string|null $sqlDrop
     *
     * @return self
     */
    public function setSqlDrop(?string $sqlDrop): \Datalator\Popo\SchemaConfigurator
    {
        $this->popoSetValue('sqlDrop', $sqlDrop);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requireSqlDrop(): string
    {
        $this->assertPropertyValue('sqlDrop');

        return (string)$this->popoGetValue('sqlDrop');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasSqlDrop(): bool
    {
        return $this->updateMap['sqlDrop'] ?? false;
    }

    /**
     * @return array|null
     */
    public function getLoadedModules(): ?array
    {
        return $this->popoGetValue('loadedModules');
    }

    /**
     * @param array|null $loadedModules
     *
     * @return self
     */
    public function setLoadedModules(?array $loadedModules): \Datalator\Popo\SchemaConfigurator
    {
        $this->popoSetValue('loadedModules', $loadedModules);

        return $this;
    }

    /**
     * Throws exception if value is null.
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    public function requireLoadedModules(): array
    {
        $this->assertPropertyValue('loadedModules');

        return (array)$this->popoGetValue('loadedModules');
    }

    /**
     * Returns true if value was set to any value, ignores defaults.
     *
     * @return bool
     */
    public function hasLoadedModules(): bool
    {
        return $this->updateMap['loadedModules'] ?? false;
    }


    
    /**
     * @param string $item
     *
     * @return self
     */
    public function addLoadedModule(string $item): \Datalator\Popo\SchemaConfigurator
    {
        $this->addCollectionItem('loadedModules', $item);

        return $this;
    }

}
