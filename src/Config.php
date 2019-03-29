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
 * Configuration class.
 */
abstract class Config implements ConfigInterface
{
    /**
     * Constructor, just call loadFromArray with overwrite flag.
     * 
     * @param array $config
     * @param bool $overwrite = false
     */
    public function __construct(array $config = [])
    {
        $this->loadFromArray($config, true);
    }
    
    /**
     * Implementation of \Countable
     * 
     * @return int
     */
    public function count(): int
    {
        return count(get_object_vars($this));
    }
    
    /**
     * Loads configuration from array, may be used to load defaults.
     * 
     * @param array $config
     * @param bool $overwrite = false
     */
    public function loadFromArray(array $config, bool $overwrite = false): void
    {
        foreach ($config as $name => $value) {
            if (!$overwrite && property_exists($this, $name)) {
                continue;
            }
            $this->$name = $value;
        }
    }
    
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
    public function loadFromPhp(string $path, bool $overwrite = false): void
    {
        $this->checkFileExistence($path);
        $this->loadFromArray($this->getArrayFromPhp($path), $overwrite);
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
        $this->loadFromArray($this->getArrayFromIni($path), $overwrite);
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
        $this->loadFromArray($this->getArrayFromJson($path), $overwrite);
    }
    
    /**
     * Gets subconfig with fields selected by prefix.
     * Fields in returned subconfig named without prefix and starts from low letter.
     * 
     * @param string $prefix
     * 
     * @return \PhpStrict\Config\ConfigInterface
     */
    public function getSlice(string $prefix): ConfigInterface
    {
        $slice = [];
        $prefixLen = strlen($prefix);
        $arr = get_object_vars($this);
        
        foreach ($arr as $name => $value) {
            if (0 === strpos($name, $prefix)) {
                $slice[lcfirst(substr($name, $prefixLen))] = $value;
            }
        }
        
        return new class($slice) extends Config {};
    }
    
    /**
     * Gets array from PHP file entries.
     * 
     * @param string $path
     * 
     * @throws \PhpStrict\Config\BadConfigException
     * 
     * @return array
     */
    protected function getArrayFromPhp(string $path): array
    {
        $config = include $path;
        
        if (!is_array($config) && !is_object($config)) {
            throw new BadConfigException();
        }
        
        if (is_object($config)) {
            $config = get_object_vars($config);
        }
        
        return $config;
    }
    
    /**
     * Gets array from INI file entries.
     * 
     * @param string $path
     * 
     * @throws \PhpStrict\Config\BadConfigException
     * 
     * @return array
     */
    protected function getArrayFromIni(string $path): array
    {
        $iniArr = null;
        
        try {
            $iniArr = parse_ini_file($path, false, INI_SCANNER_TYPED);
            $iniArr = array_change_key_case($iniArr, CASE_LOWER);
        } catch (\Throwable $e) {
            throw new BadConfigException($e->getMessage());
        }
        
        $arr = [];
        foreach ($iniArr as $name => $value) {
            $arr[lcfirst(str_replace('_', '', ucwords(str_replace('.', '_', $name), '_')))] = $value;
        }
        
        return $arr;
    }
    
    /**
     * Gets array from JSON file entries.
     * 
     * @param string $path
     * 
     * @throws \PhpStrict\Config\BadConfigException
     * 
     * @return array
     */
    protected function getArrayFromJson(string $path): array
    {
        $arr = [];
        
        try {
            $arr = get_object_vars(json_decode(file_get_contents($path)));
        } catch (\Throwable $e) {
            throw new BadConfigException($e->getMessage());
        }
        
        return $arr;
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
}
