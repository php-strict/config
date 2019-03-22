<?php
/**
 * PHP Strict.
 * 
 * @copyright   Copyright (C) 2018 - 2019 Enikeishik <enikeishik@gmail.com>. All rights reserved.
 * @author      Enikeishik <enikeishik@gmail.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace PhpStrict\Config;

use \PhpStrict\Struct\Struct;

/**
 * Configuration class.
 */
abstract class Config extends Struct implements ConfigInterface
{
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
    public function loadFromFile(string $path, bool $overwrite = false): void
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        
        if (in_array($ext, static::EXT_PHP)) {
            $this->loadFromPhp($path, $overwrite);
            return;
        } elseif (in_array($ext, static::EXT_INI)) {
            $this->loadFromIni($path, $overwrite);
            return;
        } elseif (in_array($ext, static::EXT_JSON)) {
            $this->loadFromJson($path, $overwrite);
            return;
        }
        
        throw new FileTypeNotSupportedException('File ' . $path . ' type not supported');
    }
    
    /**
     * Loads configuration from PHP file.
     * 
     * PHP file must contain return statement returning associated array 
     * which be used to fill config entries.
     * 
     * @param string $path
     * @param bool $overwrite = false
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromPhp(string $path, $overwrite = false): void
    {
        $this->checkFileExistence($path);
        
        $config = include $path;
        if (!is_array($config) && !is_object($config)) {
            throw new BadConfigException();
        }
        
        if (is_object($config)) {
            $config = get_object_vars($config);
        }
        
        $this->loadArray($config, $overwrite);
    }
    
    /**
     * Loads configuration from INI file.
     * 
     * @param string $path
     * @param bool $overwrite = false
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromIni(string $path, bool $overwrite = false): void
    {
        $this->checkFileExistence($path);
        
        $arr = null;
        try {
            $arr = parse_ini_file($path, false, INI_SCANNER_TYPED);
        } catch (\Throwable $e) {
            throw new BadConfigException($e->getMessage());
        }
        if (!is_array($arr)) {
            throw new BadConfigException();
        }
        $arr = array_change_key_case($arr, CASE_LOWER);
        
        $arr = [];
        foreach ($arr as $name => $value) {
            $arr[lcfirst(str_replace('_', '', ucwords(str_replace('.', '_', $name), '_')))] = $value;
        }
        
        $this->loadArray($arr, $overwrite);
    }
    
    /**
     * Loads configuration from JSON file.
     * 
     * @param string $path
     * @param bool $overwrite = false
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromJson(string $path, bool $overwrite = false): void
    {
        $this->checkFileExistence($path);
        
        try {
            $arr = get_object_vars(json_decode(file_get_contents($path)));
        } catch (\Throwable $e) {
            throw new BadConfigException($e->getMessage());
        }
        
        $this->loadArray($arr, $overwrite);
    }
    
    /**
     * Check file existence.
     * 
     * @param string $path
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     */
    protected function checkFileExistence(string $path): void
    {
        if (!file_exists($path)) {
            throw new FileNotExistsException('File ' . $path . ' not exists');
        }
    }
    
    /**
     * Loads configuration from array.
     * 
     * @param array $config
     * @param bool $overwrite = false
     */
    protected function loadArray(array $config, $overwrite = false): void
    {
        foreach ($config as $name => $value) {
            if (!$overwrite && property_exists($this, $name)) {
                continue;
            }
            $this->$name = $value;
        }
    }
}
