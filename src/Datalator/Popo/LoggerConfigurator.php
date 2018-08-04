<?php

declare(strict_types = 1);

namespace Datalator\Popo;

/**
 * Code generated by POPO generator, do not edit.
 */
class LoggerConfigurator 
{
    /**
     * @var array
     */
    protected $data = array (
  'name' => NULL,
  'channels' => NULL,
);

    /**
     * @var array
     */
    protected $default = array (
  'name' => NULL,
  'channels' => NULL,
);

    /**
    * @var array
    */
    protected $propertyMapping = array (
  'name' => 'string',
  'channels' => 'array',
);

    /**
    * @var array
    */
    protected $collectionItems = array (
  'name' => '',
  'channels' => '\\Datalator\\Popo\\LoggerChannel',
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

    public function fromArray(array $data): \Datalator\Popo\LoggerConfigurator
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->popoGetValue('name');
    }

    /**
     * @param string|null $name
     *
     * @return self
     */
    public function setName(?string $name): \Datalator\Popo\LoggerConfigurator
    {
        $this->popoSetValue('name', $name);

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function requireName(): string
    {
        $this->assertPropertyValue('name');

        return (string)$this->popoGetValue('name');
    }

    /**
     * @return array|null \Datalator\Popo\LoggerChannel[]
     */
    public function getChannels(): ?array
    {
        return $this->popoGetValue('channels');
    }

    /**
     * @param array|null $channels \Datalator\Popo\LoggerChannel[]
     *
     * @return self \Datalator\Popo\LoggerChannel[]
     */
    public function setChannels(?array $channels): \Datalator\Popo\LoggerConfigurator
    {
        $this->popoSetValue('channels', $channels);

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     *
     * @return array \Datalator\Popo\LoggerChannel[]
     */
    public function requireChannels(): array
    {
        $this->assertPropertyValue('channels');

        return (array)$this->popoGetValue('channels');
    }


    
    /**
     * @param  \Datalator\Popo\LoggerChannel[]\Datalator\Popo\LoggerChannel $channelsItem
     *
     * @return self \Datalator\Popo\LoggerChannel[]
     */
    public function addChannel(\Datalator\Popo\LoggerChannel $item): \Datalator\Popo\LoggerConfigurator
    {
        $this->addCollectionItem('channels', $item);

        return $this;
    }

}
