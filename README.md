# Config

[![Software License][ico-license]](LICENSE.txt)
[![Build Status][ico-travis]][link-travis]
[![codecov][ico-codecov]][link-codecov]
[![Codacy Badge][ico-codacy]][link-codacy]

Storage and loader for configuration object.
Allows be initialised with defaults and load saved configuration from PHP, INI, JSON file.

## Requirements

*   PHP >= 7.1

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

Example of configuration file content:

```ini
DEBUG=true

DB_SERVER=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=testproject
DB_CHARSET=utf8
DB_TABLE_PREFIX=

CACHE=true
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

Get subconfig by fields prefix:

```php
$dbConfig = $config->getSlice('db');

mysqli::__construct(
    $dbConfig->server, 
    $dbConfig->user, 
    $dbConfig->password, 
    $dbConfig->name
);
```

## Tests

To execute the test suite, you'll need [Codeception](https://codeception.com/).

```bash
vendor\bin\codecept run
```

[ico-license]: https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/php-strict/config/master.svg?style=flat-square
[link-travis]: https://travis-ci.org/php-strict/config
[ico-codecov]: https://codecov.io/gh/php-strict/config/branch/master/graph/badge.svg
[link-codecov]: https://codecov.io/gh/php-strict/config
[ico-codacy]: https://api.codacy.com/project/badge/Grade/9348a8df8ccf47bcb5b6c11695b7cac3
[link-codacy]: https://www.codacy.com/app/php-strict/config?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=php-strict/config&amp;utm_campaign=Badge_Grade
