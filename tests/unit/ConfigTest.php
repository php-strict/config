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
        $this->assertEquals($data['arr'][1], ($config->arr)[1]);
        $this->assertEquals($data['obj'], $config->obj);
    }
    
    public function testConfig()
    {
        $data = $this->getDataArray();
        $this->testFields($this->getFilledConfig($data), $data);
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
}
