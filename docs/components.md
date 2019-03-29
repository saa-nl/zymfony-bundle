# Components

## Routing

The routing component registers your ZF1 controllers with the Symfony router. This means Symfony will execute your controller actions, providing a single entrypoint for your application for both your new Symfony controllers, and your legacy ZF1 controllers.

[Documentation](components/routing.md)

## Registry

The registry component replaces the `Zend_Registry` instance with one that will first try to retrieve services from the Symfony container, before falling back on the legacy `Zend_Registry` container. This makes it so you can remove ZF1 bootstrapping while gradually removing `Zend_Registry` usage.

[Documentation](components/registry.md)

## Request bridge

The request bridge provies an easy way to go between Symfony requests and Zend requests, which may be useful in your migration.

[Documentation](components/request_bridge.md)