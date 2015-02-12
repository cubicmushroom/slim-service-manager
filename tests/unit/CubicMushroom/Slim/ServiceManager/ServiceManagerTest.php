<?php
namespace CubicMushroom\Slim\ServiceManager;

use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException;
use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException;
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
     * Tests that the service manager registers itself as a service in the app with default name
     */
    public function testThatTheServiceManagerRegistersItselfAsAServiceInTheAppWithDefaultName()
    {
        $app = new Slim();

        new ServiceManager($app);

        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\ServiceManager',
            $app->container->get(ServiceManager::DEFAULT_SERVICE_NAME)
        );
    }


    /**
     * Tests that the service manager registers itself as a service in the app with different name
     */
    public function testThatTheServiceManagerRegistersItselfAsAServiceInTheAppWithDifferentName()
    {
        $app         = new Slim();
        $serviceName = 'test_service_manager';

        new ServiceManager($app, ['ownServiceName' => $serviceName]);

        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\ServiceManager',
            $app->container->get($serviceName)
        );
    }


    /**
     * Tests passing an invalid option to ServiceManager throws an exception
     *
     * @expectedException \CubicMushroom\Slim\ServiceManager\Exception\InvalidOptionException
     * @expectedExceptionMessage Invalid options 'invalidOption' passed
     * @expectedExceptionCode    500
     */
    public function testPassingAnInvalidOptionToServiceManagerThrowsAnException()
    {
        $app = new Slim();
        new ServiceManager($app, ['invalidOption' => 123]);
    }


    /**
     * Tests that the service is registered if 'registerAsService' option is not set
     */
    public function testThatTheServiceIsRegisteredIfRegisterAsServiceOptionIsNotSet()
    {
        $app = new Slim();

        new ServiceManager($app);

        $this->assertInstanceOf(
            '\CubicMushroom\Slim\ServiceManager\ServiceManager',
            $app->container->get(ServiceManager::DEFAULT_SERVICE_NAME)
        );
    }


    /**
     * Tests that the service is not registered if 'registerAsService' option is set to false
     */
    public function testThatTheServiceIsNotRegisteredIfRegisterAsServiceOptionIsSetToFalse()
    {
        $app = new Slim();

        new ServiceManager($app, ['registerAsService' => false]);

        $this->assertNull($app->container->get(ServiceManager::DEFAULT_SERVICE_NAME));
    }


    /**
     * Tests that the service is registered if 'registerAsService' option is set to true
     */
    public function testThatTheServiceIsRegisteredIfRegisterAsServiceOptionIsSetToTrue()
    {
        $app = new Slim();

        new ServiceManager($app, ['registerAsService' => true]);

        $this->assertInstanceOf(
            '\CubicMushroom\Slim\ServiceManager\ServiceManager',
            $app->container->get(ServiceManager::DEFAULT_SERVICE_NAME)
        );
    }


    /**
     * Tests that the registerSelfAsService() method works
     */
    public function testThatTheRegisterSelfAsServiceMethodWorks()
    {
        $app = new Slim();

        $serviceManager = new ServiceManager($app, ['registerAsService' => false]);

        $this->assertNull($app->container->get(ServiceManager::DEFAULT_SERVICE_NAME));

        $serviceManager->registerSelfAsService();

        $this->assertInstanceOf(
            '\CubicMushroom\Slim\ServiceManager\ServiceManager',
            $app->container->get(ServiceManager::DEFAULT_SERVICE_NAME)
        );
    }


    /**
     * Tests setting up services
     */
    public function testSettingUpServices()
    {
        $serviceConfig = [
            'testService'                      => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService'
            ],
            'testServiceWithArguments'         => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c']
            ],
            'testServiceWithCalls'             => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                'calls' => [
                    ['setThisProp', ['this value']],
                    ['setThatProp', ['that value']],
                ]
            ],
            'testServiceWithCallsAndArguments' => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c'],
                'calls'     => [
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
     * @expectedExceptionMessage
     * @expectedExceptionCode 500
     */
    public function testAnExceptionIsThrownIfTheServicesConfigIsNotAnArray()
    {
        try {
            $app = new Slim(['services' => 123]);
            new ServiceManager($app);
        } catch (\Exception $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertInstanceOf('CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException', $e);
        $this->assertContains('Service config not a valid array of service definitions', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
    }


    /**
     * Tests that an exception is thrown if the 'calls' config is not an array
     */
    public function testThatAnExceptionIsThrownIfTheCallsConfigIsNotAnArray()
    {
        $app = new Slim(['services' => ['invalidCallService']]);
        try {
            new ServiceManager($app);
        } catch (\Exception $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertInstanceOf('CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException', $e);
        $this->assertContains("Invalid config for '0' service", $e->getMessage());
        $this->assertEquals(500, $e->getCode());
    }


    /**
     * Tests an exception is thrown when no service class is provided
     */
    public function testAnExceptionIsThrownWhenNoServiceClassIsProvided()
    {
        $serviceDefinition = ['missingClass' => []];

        $app = new Slim(['services' => $serviceDefinition]);
        try {
            new ServiceManager($app);
        } catch (\Exception $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertInstanceOf('CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException', $e);
        /** @var InvalidServiceConfigException $e */
        $this->assertContains(
            "Invalid config for 'missingClass' service - Missing 'class' parameter'",
            $e->getMessage()
        );
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals($serviceDefinition['missingClass'], $e->getServiceConfig());
        $this->assertEquals('class', $e->getMissingParameter());
    }


    /**
     * Tests an exception is thrown if a 'calls' config entry is not valid
     */
    public function testAnExceptionIsThrownIfACallsConfigEntryIsNotValid()
    {
        $serviceDefinition = [
            'invalidCallService' => [
                'class' => '\CubicMushroom\Slim\ServiceManager\TestService',
                'calls' => ['invalidCallDefinition']
            ]
        ];
        $app               = new Slim(['services' => $serviceDefinition]);
        try {
            new ServiceManager($app);
        } catch (\Exception $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException',
            $e
        );
        /** @var InvalidServiceCallConfigException $e */
        $this->assertContains(
            "Invalid 'call' config for 'invalidCallService' service - Call config index '0'",
            $e->getMessage()
        );
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals($serviceDefinition['invalidCallService'], $e->getServiceConfig());
        $this->assertEquals($serviceDefinition['invalidCallService']['calls'][0], $e->getCallConfig());
    }


    /**
     * Tests an exception is thrown if a call config arguments are not an array
     */
    public function testAnExceptionIsThrownIfACallConfigArgumentsAreNotAnArray()
    {
        $serviceDefinition = [
            'invalidCallService' => [
                'class' => '\CubicMushroom\Slim\ServiceManager\TestService',
                'calls' => [['invalidCallDefinition', 123]]
            ]
        ];
        $app               = new Slim(['services' => $serviceDefinition]);
        try {
            new ServiceManager($app);
        } catch (\Exception $e) {
        }

        $this->assertTrue(isset($e));
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException',
            $e
        );
        /** @var InvalidServiceCallConfigException $e */
        $this->assertContains(
            "Invalid 'call' config for 'invalidCallService' service - Call config index '0' - Arguments must be an " .
            "array",
            $e->getMessage()
        );
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals($serviceDefinition['invalidCallService'], $e->getServiceConfig());
        $this->assertEquals($serviceDefinition['invalidCallService']['calls'][0], $e->getCallConfig());
        $this->assertEquals($serviceDefinition['invalidCallService']['calls'][0][1], $e->getInvalidArguments());
    }
}


class TestSlim
{

    public $container;

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