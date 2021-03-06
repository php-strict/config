<?php
/**
 * PHP Strict.
 * 
 * @copyright   Copyright (C) 2018 - 2019 Enikeishik <enikeishik@gmail.com>. All rights reserved.
 * @author      Enikeishik <enikeishik@gmail.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace PhpStrict\Config;

/**
 * Configuration interface.
 */
interface ConfigInterface extends \Countable
{
    public const EXT_PHP = ['php'];
    public const EXT_INI = ['ini', 'cfg', 'config', 'env'];
    public const EXT_JSON = ['json', 'jsn', 'js'];
    
    /**
     * Loads configuration from array, may be used to load defaults.
     * 
     * @param array $config             name/value entries
     * @param bool $overwrite = false   overwrite existings configuration entries
     */
    public function loadFromArray(array $config, bool $overwrite = false): void;
    
    /**
     * Loads configuration from file. Polymorphic behaviour depends on file extension.
     * 
     * @param string $path              path to file
     * @param bool $overwrite = false   overwrite existings configuration entries
     * 
     * @throws \PhpStrict\Config\FileTypeNotSupportedException
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromFile(string $path, bool $overwrite = false): void;
    
    /**
     * Loads configuration from PHP file.
     * 
     * @param string $path              path to file
     * @param bool $overwrite = false   overwrite existings configuration entries
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromPhp(string $path, bool $overwrite = false): void;
    
    /**
     * Loads configuration from INI file.
     * 
     * @param string $path              path to file
     * @param bool $overwrite = false   overwrite existings configuration entries
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromIni(string $path, bool $overwrite = false): void;
    
    /**
     * Loads configuration from JSON file.
     * 
     * @param string $path              path to file
     * @param bool $overwrite = false   overwrite existings configuration entries
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromJson(string $path, bool $overwrite = false): void;
    
    /**
     * Gets subconfib with fields selected by prefix.
     * 
     * @param string $prefix
     * 
     * @return \PhpStrict\Config\ConfigInterface
     */
    public function getSlice(string $prefix): ConfigInterface;
}
