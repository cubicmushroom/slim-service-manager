<?php
namespace CubicMushroom\Slim\ServiceManager;

use Slim\Slim;

/**
 * Class ServiceManagerTest
 *
 * Tests the main ServiceManager class
 *
 * @package CubicMushroom\Slim\ServiceManager
 */
class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }


    protected function tearDown()
    {
    }


    /**
     * Tests setting up services
     */
    public function testSettingUpServices()
    {
        $serviceConfig = [
            'testService'              => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService'
            ],
            'testServiceWithArguments' => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c']
            ],
            'testServiceWithCalls'     => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                'calls' => [
                    ['setThisProp', ['this value']],
                    ['setThatProp', ['that value']],
                ]
            ],
            'testServiceWithCallsAndArguments' => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c'],
                'calls' => [
                    ['setThisProp', ['this value']],
                    ['setThatProp', ['that value']],
                ]
            ],
        ];

        $app = new Slim(['services' => $serviceConfig]);

        new ServiceManager($app);

        // testService
        $this->assertInstanceOf('CubicMushroom\Slim\ServiceManager\TestService', $app->container->get('testService'));

        // testServiceWithArguments
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('testServiceWithArguments')
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithArguments']['arguments'],
            $app->container->get('testServiceWithArguments')->args
        );

        // testServiceWithCalls
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('testServiceWithCalls')
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCalls']['calls'][0][1][0],
            $app->container->get('testServiceWithCalls')->thisProp
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCalls']['calls'][1][1][0],
            $app->container->get('testServiceWithCalls')->thatProp
        );

        // testServiceWithCallsAndArguments
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('testServiceWithCallsAndArguments')
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCallsAndArguments']['arguments'],
            $app->container->get('testServiceWithCallsAndArguments')->args
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCallsAndArguments']['calls'][0][1][0],
            $app->container->get('testServiceWithCallsAndArguments')->thisProp
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCallsAndArguments']['calls'][1][1][0],
            $app->container->get('testServiceWithCallsAndArguments')->thatProp
        );
    }


    /**
     * Tests an exception is thrown if the 'services' config is not an array
     *
     * @expectedException
     * @expectedExceptionMessage
     * @expectedExceptionCode
     */
    public function testAnExceptionIsThrownIfTheServicesConfigIsNotAnArray()
    {
        $this->markTestIncomplete();
    }


    /**
     * Tests that an exception is thrown if the 'calls' config is not an array
     *
     * @expectedException
     * @expectedExceptionMessage
     * @expectedExceptionCode
     */
    public function testThatAnExceptionIsThrownIfTheCallsConfigIsNotAnArray()
    {
        $this->markTestIncomplete();
    }


    /**
     * Tests that an exception is thrown if a 'calls' config entry does not have a correct setter value
     *
     * @expectedException
     * @expectedExceptionMessage
     * @expectedExceptionCode
     */
    public function testThatAnExceptionIsThrownIfACallsConfigEntryDoesNotHaveACorrectSetterValue()
    {
        $this->markTestIncomplete();
    }


    /**
     * Tests that an exception is thrown if a 'calls' config entry setter is not a callable method
     *
     * @expectedException
     * @expectedExceptionMessage
     * @expectedExceptionCode
     */
    public function testThatAnExceptionIsThrownIfACallsConfigEntrySetterIsNotACallableMethod()
    {
        
    }


    /**
     * Tests that an exception is thrown if a 'calls' config entry does not have a correct values array
     *
     * @expectedException
     * @expectedExceptionMessage
     * @expectedExceptionCode
     */
    public function testThatAnExceptionIsThrownIfACallsConfigEntryDoesNotHaveACorrectValuesArray()
    {
        $this->markTestIncomplete();
    }
}


/**
 * Class TestService
 *
 * Service class to test with
 *
 * @package CubicMushroom\Slim\ServiceManager
 */
class TestService
{

    /**
     * @var array
     */
    public $args;

    /**
     * @var mixed
     */
    public $thisProp;

    /**
     * @var mixed
     */
    public $thatProp;


    /**
     * Stores the arguments in the $args property
     */
    public function __construct()
    {
        $this->args = func_get_args();
    }


    /**
     * @param mixed $thisProp
     */
    public function setThisProp($thisProp)
    {
        $this->thisProp = $thisProp;
    }


    /**
     * @param mixed $thatProp
     */
    public function setThatProp($thatProp)
    {
        $this->thatProp = $thatProp;
    }
}