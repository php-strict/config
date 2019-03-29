<?php
use \PhpStrict\Config\Config as AbstractConfig;
use \PhpStrict\Config\ConfigInterface;
use \PhpStrict\Config\FileNotExistsException;
use \PhpStrict\Config\FileTypeNotSupportedException;
use \PhpStrict\Config\BadConfigException;

class Config extends AbstractConfig
{
    public $int = 0;
    public $flt = 0.0;
    public $bln = false;
    public $str = '';
    public $arr = [];
    public $obj = null;
}

class ConfigTest extends \Codeception\Test\Unit
{
    /**
     * @param string $expectedExceptionClass
     * @param callable $call = null
     */
    protected function expectedException(string $expectedExceptionClass, callable $call = null)
    {
        try {
            $call();
        } catch (\Exception $e) {
            $this->assertEquals($expectedExceptionClass, get_class($e));
            return;
        }
        if ('' != $expectedExceptionClass) {
            $this->fail('Expected exception not throwed');
        }
    }
    
    /**
     * @return array
     */
    protected function getDataArray(): array
    {
        return [
            'int' => 1,
            'flt' => 2.3,
            'str' => 'test',
            'bln' => true,
            'arr' => ['value1', 'value2', 'value3'],
            'obj' => (object) ['field1' => 'value1', 'field2' => 'value2'],
        ];
    }
    
    protected function getFilledConfig(array $data): Config
    {
        $config = new Config($data);
        $config->obj = $data['obj'];
        return $config;
    }
    
    protected function testFields(ConfigInterface $config, array $data)
    {
        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals($data['int'], $config->int);
        $this->assertEquals($data['flt'], $config->flt);
        $this->assertEquals($data['str'], $config->str);
        $this->assertEquals($data['bln'], $config->bln);
        $this->assertCount(count($data['arr']), $config->arr);
        $this->assertEquals($data['arr'], $config->arr);
        if (0 < count($data['arr'])) {
            $this->assertEquals($data['arr'][1], ($config->arr)[1]);
        }
        $this->assertEquals($data['obj'], $config->obj);
    }
    
    public function testConfig()
    {
        $data = $this->getDataArray();
        $this->testFields($this->getFilledConfig($data), $data);
    }
    
    public function testCount()
    {
        $config = new Config();
        $this->assertCount(6, $config);
        
        $config = new class([
            'field1' => 1,
            'field2' => '2',
            'field3' => 3.14,
            'field4' => true,
            'field5' => [],
            'field6' => null,
            'field7' => 0,
            'field8' => '',
            'field9' => false,
        ]) extends AbstractConfig {};
        $this->assertCount(9, $config);
    }
    
    public function testLoadFromArray()
    {
        $data = $this->getDataArray();
        $config = new Config();
        $defaults = get_object_vars($config);
        
        $config->loadFromArray($data);
        $this->testFields($config, $defaults);
        
        $config->loadFromArray($data, true);
        $this->testFields($config, $data);
    }
    
    /**
     * @return array
     */
    public function testLoadFromFileData(): array
    {
        return [
            ['config.php'],
            ['config.ini'],
            ['config.json'],
        ];
    }
    
    /**
     * @dataProvider testLoadFromFileData
     */
    public function testLoadFromFile($file)
    {
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromFile(__DIR__ . '/../_data/' . $file);
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromFile(__DIR__ . '/../_data/' . $file, true);
        $this->assertEquals(true, $config->debug);
    }
    
    public function testLoadFromFileNotSupported()
    {
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $this->expectedException(
            FileTypeNotSupportedException::class, 
            function () use ($config) {
                $config->loadFromFile(__DIR__ . '/../_data/config.none');
            }
        );
    }
    
    /**
     * @return array
     */
    public function testLoadFromFileNotExistsData(): array
    {
        return [
            ['config-not-exists.php'],
            ['config-not-exists.ini'],
            ['config-not-exists.json'],
        ];
    }
    
    /**
     * @dataProvider testLoadFromFileNotExistsData
     */
    public function testLoadFromFileNotExists($file)
    {
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $this->expectedException(
            FileNotExistsException::class, 
            function () use ($config, $file) {
                $config->loadFromFile(__DIR__ . '/../_data/' . $file);
            }
        );
    }
    
    public function testLoadFromPhp()
    {
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromPhp(__DIR__ . '/../_data/' . 'config.php');
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromPhp(__DIR__ . '/../_data/' . 'config.php', true);
        $this->assertEquals(true, $config->debug);
        
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromPhp(__DIR__ . '/../_data/' . 'config-obj.php');
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromPhp(__DIR__ . '/../_data/' . 'config-obj.php', true);
        $this->assertEquals(true, $config->debug);
        
        $this->expectedException(
            FileNotExistsException::class, 
            function () use ($config) {
                $config->loadFromPhp(__DIR__ . '/../_data/config-not-exists.php');
            }
        );
        
        $this->expectedException(
            BadConfigException::class, 
            function () use ($config) {
                $config->loadFromPhp(__DIR__ . '/../_data/config.ini');
            }
        );
    }
    
    public function testLoadFromIni()
    {
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromIni(__DIR__ . '/../_data/' . 'config.ini');
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromIni(__DIR__ . '/../_data/' . 'config.ini', true);
        $this->assertEquals(true, $config->debug);
        
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $this->expectedException(
            FileNotExistsException::class, 
            function () use ($config) {
                $config->loadFromIni(__DIR__ . '/../_data/config-not-exists.ini');
            }
        );
        
        $this->expectedException(
            BadConfigException::class, 
            function () use ($config) {
                $config->loadFromIni(__DIR__ . '/../_data/config.php');
            }
        );
    }
    
    public function testLoadFromJson()
    {
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromJson(__DIR__ . '/../_data/' . 'config.json');
        $this->assertEquals(false, $config->debug);
        
        $config->loadFromJson(__DIR__ . '/../_data/' . 'config.json', true);
        $this->assertEquals(true, $config->debug);
        
        $config = new Config();
        $config->debug = false;
        $this->assertEquals(false, $config->debug);
        
        $this->expectedException(
            FileNotExistsException::class, 
            function () use ($config) {
                $config->loadFromJson(__DIR__ . '/../_data/config-not-exists.json');
            }
        );
        
        $this->expectedException(
            BadConfigException::class, 
            function () use ($config) {
                $config->loadFromJson(__DIR__ . '/../_data/config.php');
            }
        );
    }
    
    public function testGetSlice()
    {
        $config = new Config([
            'prefixOneValueOne'     => 1,
            'prefixOneValueTwo'     => 2,
            'prefixOneValueThree'   => 3,
            'prefixTwoValueOne'     => 'one',
            'prefixTwoValueTwo'     => 'two',
            'prefixTwoValueThree'   => 'three',
            'prefixTwoValueFour'    => 'four',
        ]);
        
        $configOne = $config->getSlice('prefixOne');
        $this->assertCount(3, $configOne);
        
        $configTwo = $config->getSlice('prefixTwo');
        $this->assertCount(4, $configTwo);
    }
}
