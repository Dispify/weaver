[![Build Status](https://travis-ci.org/Dispify/weaver.svg?branch=master)](https://travis-ci.org/Dispify/weaver)
[![codecov](https://codecov.io/gh/Dispify/weaver/branch/master/graph/badge.svg)](https://codecov.io/gh/Dispify/weaver)
[![GitHub license](https://img.shields.io/github/license/Dispify/weaver)](https://github.com/Dispify/weaver/blob/master/LICENSE)

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/dispify/weaver)
![PSR-11 Support](https://img.shields.io/badge/psr--11-ok-green)

Weaver
=
The simple Dependency Injection container with autoconfiguration and autowiring. Inspired by 
[The DependencyInjection Component](https://symfony.com/doc/current/components/dependency_injection.html).

This library can help you to configure a lot of services for an example for a module test. 

Getting started
-
Before using Weaver in your project, add it to your composer.json file:
```shell script
$ composer require dispify/weaver
```

Usage
-
> :warning: When you register any service it is registered with specified class name neither implemented interfaces nor class aliases

> :warning: When the service has been instantiated Weaver overwrites all services which names are equals to
> the implemented interfaces, or the class aliases of the service with this instance of the service.

```php
class A {}
class B {
    public function __construct(A $a) {}
}

$weaver = new \Dispify\Weaver\Weaver();
$weaver->weave(B::class, $arguments = []); // register dependency
$weaver->get(B::class); // instantiate and return object of B. The service of A is created via autowiring  
```

Features
-
##### Global shared parameters & Manual instantiated external services
The pre-defined parameters and the services can be provided manually. 
Any scalar value will be always registered as a named parameter.\
Any object will be registered as a service with its class name, its parents class names, implemented interfaces and 
its declared class aliases (requires to include package "dispify/class_aliases") 

##### Lazy instantiating

When you register any service you should provide a class name of the service and an array of the arguments of the constructor.
The service will be instantiated only when this service will be requested by another service or an external caller.

The array of the arguments can be both an array of key-value and an indexed array.

##### Autoconfiguration

When service is requested to be instantiated the first action is determination of constructor's arguments via reflection.
Weaver tries to resolve each argument with existent data such as arguments, services, shared parameters or default values.

Weaver uses next ways to resolve the determined argument:
- Pick the argument by the name from the array of the arguments if it exists;
- Pick the argument by the index from the array of the arguments if it exists;
- Get the argument as a service if it is an object;
- Pick the argument by the name from the shared parameters array if it exists;
- Use a default value of the argument if it is specified;

The variadic argument is ignored by design. It will be able to change later.

##### Autowiring

When Weaver is resolving the argument which is an object it gets a service with a class name of the argument. 

If the service is not registered than Weaver tries to register this service with the class name.
> :warning: If the class name does not exist or there is an interface is specified than the exception will be thrown.

Next Weaver is instantiating this service via the workflow of the lazy instantiating.

PSR-11 compatibility
=
Weaver is compatible with PSR-11, so you can pass any string as an identifier of the service when you are getting or checking existence the service.
But you cannot register the service with a random string as the identifier. The class name or interface is allowed.
