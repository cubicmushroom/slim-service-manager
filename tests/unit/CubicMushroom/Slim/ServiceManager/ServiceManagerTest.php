<?php
namespace CubicMushroom\Slim\ServiceManager;

use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException;
use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException;
use PHPUnit_Framework_Exception;
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
     * Tests that the app is registered by default
     */
    public function testThatTheAppIsRegisteredByDefault()
    {
        $app = new Slim;

        new ServiceManager($app);

        $this->assertTrue(in_array('@app', $app->container->keys()));
    }


    /**
     * Tests that the app is registered if registerApp is true
     */
    public function testThatTheAppIsRegisteredIfRegisterAppIsTrue()
    {
        $app = new Slim;

        new ServiceManager($app, ['registerApp' => true]);

        $this->assertTrue(in_array('@app', $app->container->keys()));
    }


    /**
     * Tests if the app is not registered if registerApp is false
     */
    public function testIfTheAppIsNotRegisteredIfRegisterAppIsFalse()
    {

        $app = new Slim;

        new ServiceManager($app, ['registerApp' => false]);

        $this->assertTrue(!in_array('@app', $app->container->keys()));
    }


    /**
     * Tests setting up services
     */
    public function testSettingUpServices()
    {
        $serviceConfig = [
            'testService'                          => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService'
            ],
            'testServiceWithArguments'             => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c']
            ],
            'testServiceWithCalls'                 => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                'calls' => [
                    ['setThisProp', ['this value']],
                    ['setThatProp', ['that value']],
                ]
            ],
            'testServiceWithTags'                  => [
                'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                'tags'  => [
                    ['tag1'],
                    ['tag2', ['argument']],
                    ['tag2', ['another_argument']],
                    ['tag3', ['@testService', 123]],
                ]
            ],
            'testServiceWithCallsAndArguments'     => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c'],
                'calls'     => [
                    ['setThisProp', ['this value']],
                    ['setThatProp', ['that value']],
                ]
            ],
            'testServiceWithCallsArgumentsAndTags' => [
                'class'     => 'CubicMushroom\Slim\ServiceManager\TestService',
                'arguments' => [1, 2, 3, 'a', 'b', 'c'],
                'calls'     => [
                    ['setThisProp', ['this value']],
                    ['setThatProp', ['that value']],
                ],
                'tags'      => [
                    ['tag1'],
                ],
            ],
        ];

        $app = new Slim(['services' => $serviceConfig]);

        $sm = new ServiceManager($app);

        $services = $app->container->all();

        // testService
        $this->assertTrue(in_array('@testService', array_keys($services)));
        $testService = $app->container->get('@testService');
        $this->assertInstanceOf('CubicMushroom\Slim\ServiceManager\TestService', $testService);

        // testServiceWithArguments
        $this->assertTrue(in_array('@testServiceWithArguments', array_keys($services)));
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('@testServiceWithArguments')
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithArguments']['arguments'],
            $app->container->get('@testServiceWithArguments')->args
        );

        // testServiceWithCalls
        $this->assertTrue(in_array('@testServiceWithCalls', array_keys($services)));
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('@testServiceWithCalls')
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCalls']['calls'][0][1][0],
            $app->container->get('@testServiceWithCalls')->thisProp
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCalls']['calls'][1][1][0],
            $app->container->get('@testServiceWithCalls')->thatProp
        );

        // testServiceWithTags
        $this->assertTrue(in_array('@testServiceWithTags', array_keys($services)));
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('@testServiceWithTags')
        );
        /** @var ServiceDefinition $testServiceWithTagsDefinition */
        $testServiceWithTagsDefinition = $services['@testServiceWithTags'];
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\ServiceDefinition',
            $testServiceWithTagsDefinition
        );
        $testServiceWithTagsTags = $testServiceWithTagsDefinition->getTags();
        $this->assertArrayHasKey($serviceConfig['testServiceWithTags']['tags'][0][0], $testServiceWithTagsTags);
        $this->assertArrayHasKey($serviceConfig['testServiceWithTags']['tags'][1][0], $testServiceWithTagsTags);
        $this->assertEquals(
            $serviceConfig['testServiceWithTags']['tags'][1][1],
            $testServiceWithTagsTags['tag2'][0]->getArguments()
        );
        $this->assertArrayHasKey($serviceConfig['testServiceWithTags']['tags'][2][0], $testServiceWithTagsTags);
        $this->assertEquals(
            $serviceConfig['testServiceWithTags']['tags'][2][1],
            $testServiceWithTagsTags['tag2'][1]->getArguments()
        );
        $this->assertArrayHasKey($serviceConfig['testServiceWithTags']['tags'][3][0], $testServiceWithTagsTags);
        $this->assertEquals(
            $serviceConfig['testServiceWithTags']['tags'][3][1],
            $testServiceWithTagsTags['tag3'][0]->getArguments()
        );

        // testServiceWithCallsAndArguments
        $this->assertTrue(in_array('@testServiceWithCallsAndArguments', array_keys($services)));
        $this->assertInstanceOf(
            'CubicMushroom\Slim\ServiceManager\TestService',
            $app->container->get('@testServiceWithCallsAndArguments')
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCallsAndArguments']['arguments'],
            $app->container->get('@testServiceWithCallsAndArguments')->args
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCallsAndArguments']['calls'][0][1][0],
            $app->container->get('@testServiceWithCallsAndArguments')->thisProp
        );
        $this->assertEquals(
            $serviceConfig['testServiceWithCallsAndArguments']['calls'][1][1][0],
            $app->container->get('@testServiceWithCallsAndArguments')->thatProp
        );
    }


    /**
     * Tests that tagged services can be retrieved
     */
    public function testThatTaggedServicesCanBeRetrieved()
    {
        $serviceConfig = [
            'services' => [
                'testServiceOne'   => [
                    'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                    'tags'  => [
                        ['tag1'],
                        ['tag2', ['argument']],
                        ['tag2', ['another_argument']],
                    ]
                ],
                'testServiceTwo'   => [
                    'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                    'tags'  => [
                        ['tag1'],
                        ['tag3', ['@testService', 123]],
                    ]
                ],
                'testServiceThree' => [
                    'class' => 'CubicMushroom\Slim\ServiceManager\TestService',
                    'tags'  => [
                        ['tag2', ['argument']],
                    ]
                ],
            ],
        ];

        $app = new Slim($serviceConfig);

        $sc = new ServiceManager($app);

        $this->assertArrayHasKey('@testServiceOne', $sc->getTaggedServices('tag1'));
        $this->assertArrayHasKey('@testServiceTwo', $sc->getTaggedServices('tag1'));
        $this->assertArrayNotHasKey('@testServiceThree', $sc->getTaggedServices('tag1'));
        $this->assertArrayHasKey('@testServiceOne', $sc->getTaggedServices('tag2'));
        $this->assertArrayNotHasKey('@testServiceTwo', $sc->getTaggedServices('tag2'));
        $this->assertArrayHasKey('@testServiceThree', $sc->getTaggedServices('tag2'));
        $this->assertArrayNotHasKey('@testServiceOne', $sc->getTaggedServices('tag3'));
        $this->assertArrayHasKey('@testServiceTwo', $sc->getTaggedServices('tag3'));
        $this->assertArrayNotHasKey('@testServiceThree', $sc->getTaggedServices('tag3'));
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
     *
     * @expectedException PHPUnit_Framework_Exception
     * @expectedExceptionMessage Argument 2 passed to CubicMushroom\Slim\ServiceManager\ServiceManager::setupService()
     *                           must be of the type array
     */
    public function testThatAnExceptionIsThrownIfTheCallsConfigIsNotAnArray()
    {
        $app = new Slim(['services' => ['invalidCallService']]);

        new ServiceManager($app);
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


    /**
     * Tests that services are autoloaded by default
     */
    public function testThatServicesAreAutoloadedByDefault()
    {
        //$this->markTestIncomplete();
        $servicesConfig = [
            'services' => [
                'serviceOne'   => [
                    'class'     => __NAMESPACE__ . '\TestsServiceOne',
                    'arguments' => ['a', 'b', 'c', '@serviceTwo'],
                ],
                'serviceTwo'   => [
                    'class' => __NAMESPACE__ . '\TestsServiceTwo',
                    'calls' => [
                        ['setServiceThree', ['@ServiceThree']]
                    ]
                ],
                'serviceThree' => [
                    'class' => __NAMESPACE__ . '\TestsServiceThree'
                ],
            ]
        ];

        $app = new Slim($servicesConfig);

        new ServiceManager($app);

        foreach (array_keys($servicesConfig['services']) as $serviceName) {
            $this->assertArrayHasKey('@' . $serviceName, $app->container->all());
        }
    }


    /**
     * Tests that services are loaded if autoload option is true
     */
    public function testThatServicesAreLoadedIfAutoloadOptionIsTrue()
    {
        //$this->markTestIncomplete();
        $servicesConfig = [
            'services' => [
                'serviceOne'   => [
                    'class'     => __NAMESPACE__ . '\TestsServiceOne',
                    'arguments' => ['a', 'b', 'c', '@serviceTwo'],
                ],
                'serviceTwo'   => [
                    'class' => __NAMESPACE__ . '\TestsServiceTwo',
                    'calls' => [
                        ['setServiceThree', ['@ServiceThree']]
                    ]
                ],
                'serviceThree' => [
                    'class' => __NAMESPACE__ . '\TestsServiceThree'
                ],
            ]
        ];

        $app = new Slim($servicesConfig);

        new ServiceManager($app, ['autoload' => true]);

        foreach (array_keys($servicesConfig['services']) as $serviceName) {
            $this->assertArrayHasKey('@' . $serviceName, $app->container->all());
        }
    }


    /**
     * Tests that services are not loaded if autoload option is false
     */
    public function testThatServicesAreNotLoadedIfAutoloadOptionIsFalse()
    {
        //$this->markTestIncomplete();
        $servicesConfig = [
            'services' => [
                'serviceOne'   => [
                    'class'     => __NAMESPACE__ . '\TestsServiceOne',
                    'arguments' => ['a', 'b', 'c', '@serviceTwo'],
                ],
                'serviceTwo'   => [
                    'class' => __NAMESPACE__ . '\TestsServiceTwo',
                    'calls' => [
                        ['setServiceThree', ['@ServiceThree']]
                    ]
                ],
                'serviceThree' => [
                    'class' => __NAMESPACE__ . '\TestsServiceThree'
                ],
            ]
        ];

        $app = new Slim($servicesConfig);

        new ServiceManager($app, ['autoload' => false]);

        foreach (array_keys($servicesConfig['services']) as $serviceName) {
            $this->assertArrayNotHasKey($serviceName, $app->container->all());
        }
    }


    /**
     * Tests services are injected OK using @ notation
     */
    public function testServicesAreInjectedOkUsingAtSymbolNotation()
    {
        //$this->markTestIncomplete();
        $servicesConfig = [
            'services' => [
                'serviceOne'   => [
                    'class'     => __NAMESPACE__ . '\TestServiceOne',
                    'arguments' => ['a', 'b', 'c', '@serviceTwo'],
                ],
                'serviceTwo'   => [
                    'class' => __NAMESPACE__ . '\TestServiceTwo',
                    'calls' => [
                        ['setServiceThree', ['@serviceThree']]
                    ]
                ],
                'serviceThree' => [
                    'class' => __NAMESPACE__ . '\TestServiceThree'
                ],
            ]
        ];

        $app = new Slim($servicesConfig);

        new ServiceManager($app, ['autoload' => true]);

        /** @var TestServiceOne $serviceOne */
        $serviceOne = $app->container->get('@serviceOne');
        /** @var TestServiceTwo $serviceTwo */
        $serviceTwo = $serviceOne->constructorArgs[3];
        /** @var TestServiceThree $serviceThree */
        $serviceThree = $serviceTwo->testServiceThree;
        $this->assertInstanceOf($servicesConfig['services']['serviceTwo']['class'], $serviceTwo);
        $this->assertInstanceOf($servicesConfig['services']['serviceThree']['class'], $serviceThree);
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


class TestServiceOne
{

    /**
     * @var array
     */
    public $constructorArgs;


    /**
     * Stores it's arguments in $constructorArgs property
     */
    function __construct()
    {
        $this->constructorArgs = func_get_args();
    }
}


class TestServiceTwo
{

    /**
     * @var TestServiceThree
     */
    public $testServiceThree;


    public function setServiceThree(TestServiceThree $testServiceThree)
    {
        $this->testServiceThree = $testServiceThree;
    }
}


class TestServiceThree
{
}