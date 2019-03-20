<?php
/**
 * PHP Strict.
 * 
 * @copyright   Copyright (C) 2018 - 2019 Enikeishik <enikeishik@gmail.com>. All rights reserved.
 * @author      Enikeishik <enikeishik@gmail.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace PhpStrict\Config;

use \PhpStrict\Struct\StructInterface;

/**
 * Configuration interface.
 */
interface ConfigInterface extends StructInterface
{
    /**
     * Loads configuration from PHP file.
     * 
     * @param string $path              path to INI file
     * @param bool $overwrite = false   overwrite existings configuration entries
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromPhp(string $path, bool $overwrite = false): void;
    
    /**
     * Loads configuration from INI file.
     * 
     * @param string $path              path to INI file
     * @param bool $overwrite = false   overwrite existings configuration entries
     * 
     * @throws \PhpStrict\Config\FileNotExistsException
     * @throws \PhpStrict\Config\BadConfigException
     */
    public function loadFromIni(string $path, bool $overwrite = false): void;
}
