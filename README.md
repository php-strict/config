# Config

[![Software License][ico-license]](LICENSE.txt)

Storage and loader for configuration object.
Extends [Struct](https://github.com/php-strict/struct) functionality with loading methods.
Allows load saved configuration from PHP, INI, JSON file.

## Requirements

*   PHP >= 7.1
*   php-strict/struct

## Install

Install with [Composer](http://getcomposer.org):
    
```bash
composer require php-strict/config
```

## Usage

Define your own application configuration class by extending Config class:

```php
use PhpStrict\Config\Config

class AppConfig extends Config
{
    /**
     * Project root
     * 
     * @var string
     */
    public $root = '/';
    
    /**
     * Debug enable|disable
     * 
     * @var bool
     */
    public $debug = false;
    
    /**
     * Database settings
     */
    public $dbServer = '';
    public $dbUser = '';
    public $dbPassword = '';
    public $dbName = '';
    public $dbCharset = '';
    public $dbTablePrefix = ''
    
    /*
     * another configuration fields here
     */
    
}
```

Create and fill your configuration object with data from saved configuration file:

```php
$config = new AppConfig();
$config->loadFromFile('config.ini');
```

Use configuration object fields directly on demand:

```php
mysqli::__construct(
    $config->dbServer, 
    $config->dbUser, 
    $config->dbPassword, 
    $config->dbName
);
```

## Tests

To execute the test suite, you'll need [Codeception](https://codeception.com/).

```bash
vendor\bin\codecept run
```

[ico-license]: https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat-square
