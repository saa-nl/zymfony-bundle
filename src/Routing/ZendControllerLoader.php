<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\Routing;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\GlobResource;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ZendControllerLoader extends Loader
{
    public const PARAM_KEY_KEY = 'zfkey';
    public const PARAM_KEY_VAL = 'zfval';
    private const ROUTE_PARAMS_MAX = 10;

    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var \Zend_Filter_Word_CamelCaseToDash
     */
    private $camelCaseToDashFilter;

    /**
     * @var array
     */
    private $customRoutes;

    public function __construct(FileLocatorInterface $fileLocator, array $customRoutes)
    {
        $this->fileLocator = $fileLocator;
        $this->customRoutes = $customRoutes;
        $this->camelCaseToDashFilter = new \Zend_Filter_Word_CamelCaseToDash();
    }

    /**
     * @param string $resource
     * @param string $type
     * @return RouteCollection
     * @throws \ReflectionException
     */
    public function load($resource, $type = null)
    {
        $routeCollection = new RouteCollection();

        $directoryPath = $this->fileLocator->locate($resource);

        $customRouteIndex = 1;
        foreach ($this->customRoutes as $route => $controller) {
            $this->addRoute($routeCollection, 'zend_custom' . $customRouteIndex, $route, $controller);
            $customRouteIndex++;
        }

        foreach ($this->getControllerIterator($directoryPath) as $path => $info) {
            $class = $this->findClass($path);

            $reflector = new \ReflectionClass($class);
            $methods = $reflector->getMethods();

            list($controllerRoutePart) = explode('Controller', $reflector->getShortName());
            $controllerRoutePart = strtolower($this->camelCaseToDashFilter->filter($controllerRoutePart));

            foreach ($methods as $method) {
                if (strpos($method->getName(), 'Action') === false) {
                    continue;
                }

                $methodRoutePart = $this->getMethodRoutePart($method);
                $this->addRouteFromMethod($routeCollection, $controllerRoutePart, $method, $methodRoutePart);
            }

            if ($reflector->hasMethod('indexAction')) {
                // Has to be ordered last to not conflict with other routes' parameters
                $this->addRouteFromMethod($routeCollection, $controllerRoutePart, $reflector->getMethod('indexAction'));
            }
        }

        return $routeCollection;
    }

    /**
     * @param mixed
     * @param string|null
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return $type === 'zend' && is_string($resource) && is_dir($this->fileLocator->locate($resource));
    }

    private function getControllerIterator(string $path)
    {
        $resource =  new GlobResource($path, '', true);

        yield from $resource;
    }

    /**
     * From Symfony\Component\Routing\Loader\AnnotationFileLoader
     *
     * Returns the full class name for the first class in the file.
     *
     * @param string $file A PHP file path
     *
     * @return string|false Full class name if found, false otherwise
     */
    private function findClass($file)
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));

        if (1 === \count($tokens) && T_INLINE_HTML === $tokens[0][0]) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain PHP code. Did you forgot to add the "<?php" start tag at the beginning of the file?', $file));
        }

        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];

            if (!isset($token[1])) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1]) && \in_array($tokens[$i][0], array(T_NS_SEPARATOR, T_STRING))) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }

            if (T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($tokens[$j][1])) {
                        break;
                    }

                    if (T_DOUBLE_COLON === $tokens[$j][0] || T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!\in_array($tokens[$j][0], array(T_WHITESPACE, T_DOC_COMMENT, T_COMMENT))) {
                        break;
                    }
                }

                if (!$skipClassToken) {
                    $class = true;
                }
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }

    private function addRouteFromMethod(
        RouteCollection $routeCollection,
        string $controllerRoutePart,
        \ReflectionMethod $method,
        ?string $methodRoutePart = null
    ) {
        $path = '/' . $controllerRoutePart;
        $routeName = 'zend_' . $controllerRoutePart;
        if ($methodRoutePart !== null) {
            $path .= '/' . $methodRoutePart;
            $routeName .= '_' . $methodRoutePart;
        }

        $this->addRoute(
            $routeCollection,
            $routeName,
            $path,
            $method->getDeclaringClass()->getName() . '::' . $method->getName()
        );
    }

    private function buildRouteVars(): string
    {
        $routePath = '';
        for ($i = 1; $i <= self::ROUTE_PARAMS_MAX; $i++) {
            $routePath .= '/{' . self::PARAM_KEY_KEY . $i . '}/{' . self::PARAM_KEY_VAL . $i . '}';
        }

        return $routePath;
    }

    private function buildRouteDefaults(): array
    {
        $defaults = [];
        for ($i = 1; $i <= self::ROUTE_PARAMS_MAX; $i++) {
            $defaults[self::PARAM_KEY_KEY. $i] = null;
            $defaults[self::PARAM_KEY_VAL . $i] = null;
        }

        return $defaults;
    }

    private function addRoute(
        RouteCollection $routeCollection,
        string $routeName,
        string $path,
        string $controller
    ) {
        $route = new Route($path . $this->buildRouteVars(), ['_controller' => $controller]);
        $route->addDefaults($this->buildRouteDefaults());

        $routeCollection->add($routeName, $route);
    }

    private function getMethodRoutePart(\ReflectionMethod $method): string
    {
        $methodRoutePart = str_replace('Action', '', $method->getName());
        $methodRoutePart = str_replace(
            ' ',
            '-',
            trim(strtolower($this->camelCaseToDashFilter->filter($methodRoutePart)))
        );
        return $methodRoutePart;
    }
}
