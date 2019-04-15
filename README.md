# About
Datalator - very simple test database populator.


## Installation

`composer require datalator/datalator --dev`

## Usage

### Test Populator Helper

#### Usage

```
protected function setUp()
{
    $this->databasePopulator = (new Datalator\Helper\TestPopulatorHelper())
        ->useSchemaName('default')
        ->useDataName('default')
        ->useSchemaPath('path/to/schema')
        ->useDataPath('path/to/data')
        ->populate();
}

protected function tearDown()
{
    $this->databasePopulator->rollback();
}
```


Note: `dumpConfiguratorInstance()` is useful if you want to extend or import other modules.
See [DatalatorFacadeTest](tests/Datalator/DatalatorFacadeTest.php) for more examples.


### Configuration

Example of `.datalator` configuration file.
```
schema = default
data = default
schemaPath = schema/
dataPath = data/
; all modules are loaded by default, you can select them manually here
; modules[] = module1 ;
; modules[] = module2 ;
```
Or you can specify all options from the command line.

### Usage
```
 vendor/bin/datalator 
  create    Create the test database. The database will dropped if it exists.
  drop      Drop the test database if it exists.
  populate  Populate the test database. The database will created / dropped if needed.
 ```
 
## Data Reader
Select by identity column (default is `id`).
```
$readerConfigurator = (new ReaderConfigurator())
    ->setSource('foo_one')
    ->setIdentityValue(1)
    ->setQueryColumn('foo_one_key');

$value = $this->databasePopulator->readValue($readerConfigurator);s

$this->assertEquals('foo-one', $value->getSchemaValue());
```

Change identity column to `foo_one_key`.
```
$readerConfigurator = (new ReaderConfigurator())
    ->setSource('foo_one')
    ->setIdentityValue('foo-one')
    ->setIdentityColumn('foo_one_key')
    ->setQueryColumn('foo_one_value');

$value = $this->databasePopulator->readValue($readerConfigurator);

$this->assertEquals('Foo One', $value->getDataValue());
```


## Data Sources
A database table is populated from a data source, which by default is a CSV file.
The schema is loaded from a SQL file.

The files are organised into folders called `modules`.

### Schema and Data Modules
The schema and data files are divided into modules. 
Each schema module has related data module.

Modules are set of tables that will be populated together in a specific order.

The organisation is very project specific.
You can have as many modules and they can have as many tables as you want.
Or you could just use one module for all of your tables.


#### schema module layout
Schema modules is just a folder with SQL files, containing `CREATE TABLE` statement for specific table.
The name of the SQL file is the name of the database table.

#### data module layout
Data module is a folder containing data source files in CSV format (by default).
The name of the CSV file is the name of the table.

Besides the data files, it also contains `module.ini` file which contains information
about tables managed by that module.

###### modules.ini example:
```
[tables]
tables[] = foo_table
```


#### _db module
There is only one special module called `_db`.
It consist the  database schema needed for creating and dropping whole database, 
as well as the connection settings.

It contains 3 files:

 - `create.sql`: SQL file with schema needed to create test database.
 - `drop.sql`: SQL file with schema needed to drop test database.
 - `database.ini`: INI file containing connection and modules settings.


###### database.ini example
```
[connection]
driver = pdo_mysql
dbname = test_database
host = localhost
user = testuser
password = testpassword
port = 3306

[modules]
load[] = foo
load[] = bar
load[] = buzz
...
```


## Tests
Run `vendor/bin/phpunit`. 

See modules examples under [tests/fixtures/database](https://github.com/oliwierptak/datalator/tree/master/tests/fixtures/database).
