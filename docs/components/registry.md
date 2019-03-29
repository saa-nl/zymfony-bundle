# Registry component

The registry component replaces the `Zend_Registry` instance with one that will first try to retrieve services from the Symfony container, before falling back on the legacy `Zend_Registry` container. This makes it so you can remove ZF1 bootstrapping while gradually removing `Zend_Registry` usage.

## Configuration

Available configuration parameters:

| Parameter | Type | Description                     |
| --------- | ---- | ------------------------------- |
| enabled   | bool | Enables the registry component. |

## Usage

You can simply use `Zend_Registry` as normal. Only the instance in `Zend_Registry` is changed, though it still extends `Zend_Registry`, so should be compatible. 

Please note that for Symfony services, only ones that are marked as public will be available. Please check the [Symfony documentation](https://symfony.com/doc/current/service_container/alias_private.html#marking-services-as-public-private) for more information.