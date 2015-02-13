[![Build Status](https://travis-ci.org/cubicmushroom/slim-service-loader.svg?branch=master)](https://travis-ci.org/cubicmushroom/slim-service-loader)

Slim Framework Service Loader
=============================

The Service Loader is used to prepare services for a Slim Framework app based on service config.


Usage
-----

Here's a quick example of how to use the Service Loader...

    // Define the services 
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
    
    $serviceManagerOptions = [
        'registerAsService' => true,              // default value
        'ownServiceName'    => 'service_manager', // default value
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

2. Then all you need to do is instantiate the ServiceManager object, passing in the App, with the optional config options.  This example shows
   the default values for the config options. 
    
        $serviceManagerOptions = [
            'registerAsService' => true,              // default value
            'ownServiceName'    => 'service_manager', // default value
        ];
        
        new ServiceManager($app, $serviceManagerOptions);


Options Details
---------------

Here's an explanation of each of the options...


### registerAsService

Default: true

If set to true the service managed will register itself as a service using the value of the `ownServiceName` setting.


### ownServiceName

Default: 'service_manager'

The name to use if/when registering the Service Manager as a service.