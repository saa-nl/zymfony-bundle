# Routing component

The routing component registers your ZF1 controllers with the Symfony router. This means Symfony will execute your controller actions, providing a single entrypoint for your application for both your new Symfony controllers, and your legacy ZF1 controllers.

## Configuration

Available configuration parameters:

| Parameter     | Type  | Description                            |
| ------------- | ----- | -------------------------------------- |
| enabled       | bool  | Enables the routing component.         |
| custom_routes | array | Key-value array of custom routes.      |

### Custom routes

To replace custom routes that you would normally add to the Zend Router, you can add custom routes using the `custom_routes` configuration parameter. This is a key-value array, where the key is the Symfony-compatible route, and the value is the ZF1 action to call.

Example: `/my/custom/route/{someparam}: 'ZendController\MyController::someAction'`

Please note that the additional ZF1-compatible parameters are still added to the custom routes.

## Usage

Add the route configuration to your Symfony `routes.yaml`:

```yaml
app_zend:
  resource: ../application/controllers/
  type: zend
```

### BaseController

The routing component provides a `BaseController`. The BaseController makes sure ZF1 actions are called correctly, and can return a proper Symfony response.
All ZF1 controllers should extend this `BaseController` instead of the default `\Zend_Controller_Action`. 

The BaseController also needs a caller and optionally a renderer to function. You can configure these using the `setController` and `setRenderer` functions in the BaseController, which you can call using a listener on the `kernel.controller_arguments` event.

**Caller**

The caller should be the one to actually call the action in the ZF1 controller. You can use this to, for example, call the action with a different DI container, if you're using one. The interface allows to return a Symfony `Response` object, or `null`. If `null` is returned, the renderer is needed and will be called. The caller should implement the `ControllerActionCallerInterface`.

**Renderer**

The renderer should render and/or convert the Zend view to a Symfony response. You can, for example, render your Zend actions through Twig, or any other templating engine. As long as a Symfony `response` object is returned, it should work. The renderer should implement the `RendererInterface`.