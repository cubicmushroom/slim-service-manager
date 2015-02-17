[![Build Status](https://travis-ci.org/cubicmushroom/slim-service-manager.svg?branch=master)](https://travis-ci.org/cubicmushroom/slim-service-manager)

Slim Framework Service Manager
==============================

The Service Loader is used to prepare services for a Slim Framework app based on service config.

The name of all services registered with the service manager are all prefixed with an '@' symbol.  Therefore, as service 
name of `this_service` will be registered as `@this_service`.


Usage
-----

Here's a quick example of how to use the Service Loader...

    // Define the services 
    $config = [
        'services' => [
            'serviceOne' => [
                'class' => 'ServiceOne',
                'arguments' => ['a', 'b', 'c', '@serviceTwo'],
                'tags' => [
                    ['tagName', ['tagArg1', 'tagArg2' => 'tagsArg2Value']]
                ],
            ],
            'serviceTwo' => [
                'class' => 'Namespace\ServiceTwo',
                'calls' => [
                    ['setServiceThree', ['@ServiceThree']]
                ]
            ],
            'serviceThree' => [
                'class' => 'Namespace\ServiceThree'
            ],
        ]
    ];

    $app = new Slim($config);
    
    $serviceManagerOptions = [
        'registerAsService' => true,              // default value
        'ownServiceName'    => 'service_manager', // default value
        'autoload'          => true               // default value
        'registerApp'       => true               // default value
    ];
    
    new ServiceManager($app, $serviceManagerOptions);


1. First of all, add a `'services'` element to the config that you pass into your Slim object.

        $config = [
            'services' => [
                'serviceOne' => [
                    'class' => 'ServiceOne',
                    'arguments' => ['a', 'b', 'c', '@serviceTwo'],
                ],
                'serviceTwo' => [
                    'class' => 'Namespace\ServiceTwo',
                    'calls' => [
                        ['setServiceThree', ['@ServiceThree']]
                    ]
                ],
                'serviceThree' => [
                    'class' => 'Namespace\ServiceThree'
                ],
            ]
        ];
    
        $app = new Slim($config);

2. Then all you need to do is instantiate the ServiceManager object, passing in the App, with the optional config 
   options.  This example shows the default values for the config options. 
    
        $serviceManagerOptions = [
            'registerAsService' => true,              // default value
            'ownServiceName'    => 'service_manager', // default value
            'autoload'          => true               // default value
            'registerApp'       => true               // default value
        ];
        
        new ServiceManager($app, $serviceManagerOptions);


Service Definition
------------------

The Service definition should be an array of items, with the key as the name the service will be registered under, and 
the value an array of settings as follows...

### class

Require

The full class name to be used to instantiate the service object.


### arguments

Optional

An array of arguments that will be passed to the service object constructor.

String values beginning with an '@' will be substituted with the service with the name matching the string, less the 
leading '@'.


### calls

Optional

An array containing details of each additional method to be called on the initialised service object.  Each entry in the 
array should be an array with the first item being the name of the method to be called, and an optional second array 
item that will be used as the arguments for the method call.

As with the `arguments`, any string that begins with an '@' within the arguments array will be substituted with the 
service registered with the name matching the string, less the leading '@'.


### tags

Optional

An array of tags that can be used to filter service definitions by.

Each entry in the array should contain the tag name as it's first item, and an optional array of parameters for the 
second item.

You can then retrieve services tagged with a tag using `$serviceManager->getTaggedServices('tagName')`.


Arguments
---------

Here's an explanation of each of the options...

### Options

The second argument to the constructor...


#### options.autoload

Whether to automatically register the services during instantiation


#### options.ownServiceName

Default: 'service_manager'

The name to use if/when registering the Service Manager as a service.



#### options.registerApp
Default: true

If set to true the service manager will register the app as a service using the key `@app`.


#### options.registerAsService

Default: true

If set to true the service manager will register itself as a service using the value of the `ownServiceName` setting.


ServiceManager Methods
----------------------

The following methods are available on the ServiceManager...

### registerApp()

Registers the app as a service.  Only needs to be called if `options.registerApp` setting is false, otherwise this is 
called automatically.

### setupServices()

Loads all the services.  This is called automatically if `options.autoload` is set to false


### registerSelfAsService()

Registers the ServiceManager as a service using the `options.ownServiceName` value (or the default).  This is 
automatically called if `options.registerAsService` is true.
 
 
### getService($serviceName)

Returns the service with the given name.


### getTaggedServices($tagName)

Returns an array of services that are tagged with the given tag.

The returns array will contain the registered service name, prefixed with an '@' as the key, and the ServiceDefinition 
object as the value.


Other Classes
-------------

### ServiceDefinition

This class is used when registering a service.  It's used over a regular closure to allow the service name and config to
be stored in the object.
 
The `__invoke()` method is used to instantiate the service object when requested.


### MethodCallDefinition

This class stores any calls on the method on instantiation, as defined by the `calls` part of the service definition.


### Tag

This class stores the name and arguments of each tag as defined by the `tags` part of the service definition. 