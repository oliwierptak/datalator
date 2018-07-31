# About
Datalator - very simple test database populator.


## Installation

`composer require everon/datalator`

## Usage

###  Tests
You could use `setUp` of phpunit to instantiate your own database populator.

```
protected function setUp()
{
    $this->databasePopulator = (new Datalator\Helper\TestPopulator())
        ->useSchemaPath('path/to/schema')
        ->useDataPath('path/to/data')
        ->populate();
}
```

### vendor/bin/datalator
```
 vendor/bin/datalator 
  create    Create the test database. The database will dropped if it exists.
  drop      Drop the test database if it exists.
  populate  Populate the test database. The database will created / dropped if needed.
 ```

## Tests
Run `vendor/bin/phpunit`. 
