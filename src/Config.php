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
     * Loads configuration from PHP file.
     * 
     * @param string $path
     * @param bool $overwrite = false
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromPhp(string $path, $overwrite = false): void
    {
        if (!file_exists($path)) {
            throw new FileNotExistsException('File not ' . $path . ' exists');
        }
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
        if (!file_exists($path)) {
            throw new FileNotExistsException('File not ' . $path . ' exists');
        }
        
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
