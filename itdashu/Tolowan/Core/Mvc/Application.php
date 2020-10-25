<?php
namespace Core\Mvc;

use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application as Papplication;
use Phalcon\Mvc\Application\Exception;

class Application extends Papplication
{
    public function callMacro($name, $arguments = array())
    {
        return $this->volt->callMacro($name, $arguments);
    }

    public function handle($uri = null)
    {
        $this->uri = $uri;
        $dependencyInjector = $this->_dependencyInjector;
        if (!is_object($dependencyInjector)) {
            throw new Exception("A dependency injection object is required to access internal services");

        }

        $eventsManager = $this->_eventsManager;

        /**
         * Call boot event,$this allow the developer to perform initialization actions
         */
        if (is_object($eventsManager)) {
            if ($eventsManager->fire('application:boot', $this) === false) {
                return false;
            }
        }
        if (is_object($eventsManager)) {
            $eventsManager->fire("application:beforeRouter", $this);
        }

        $router = $dependencyInjector->getShared("router");

        /**
         * Handle the URI pattern (if any)
         */
        $router->handle($this->uri);

        /**
         * If the router doesn't return a valid module we use the default module
         */
        $moduleName = $router->getModuleName();
        if (!$moduleName) {
            $moduleName = $this->_defaultModule;
        }
        if ($moduleName) {
            $module = $this->getModules($moduleName);
        }

        /**
         * Check whether use implicit views or not
         */
        $implicitView = $this->_implicitView;

        if ($implicitView === true) {
            $view = $dependencyInjector->getShared("view");
        }

        /**
         * We get the parameters from the router and assign them to the $dispatcher
         * Assign the values passed from the router
         */
        $dispatcher = $dependencyInjector->getShared("dispatcher");
        $dispatcher->setModuleName($router->getModuleName());
        $dispatcher->setNamespaceName($router->getNamespaceName());
        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());
        /*
         *调试404
        $ss = $router->getMatchedRoute();
        echo $ss->getName() . '<br>';
        echo $dispatcher->getNamespaceName() . "<br>";
        echo $dispatcher->getControllerName() . '<br>';
        echo $dispatcher->getActionName() . '<br>';
         */
        /**
         * Start the view component (start output buffering)
         */
        if ($implicitView === true) {
            $view->start();
        }

        /**
         * Calling beforeHandleRequest
         */
        if (is_object($eventsManager)) {
            if ($eventsManager->fire("application:beforeHandleRequest", $this, $dispatcher) === false) {
                return false;
            }
        }

        /**
         * The $dispatcher must return an object
         */
        $controller = $dispatcher->dispatch();

        /**
         * Get the latest value returned by an action
         */
        $possibleResponse = $dispatcher->getReturnedValue();

        if (is_bool($possibleResponse) && $possibleResponse == false) {
            $response = $dependencyInjector->getShared("response");
        } else {
            if (is_object($possibleResponse)) {
                /**
                 * Check if the returned object is already a response
                 */
                $returnedResponse = $possibleResponse instanceof ResponseInterface;
            } else {
                $returnedResponse = false;
            }
            /**
             * Calling afterHandleRequest
             */
            if (is_object($eventsManager)) {
                $eventsManager->fire("application:afterHandleRequest", $this, $controller);
            }

            /**
             * If the $dispatcher returns an object we try to render the view in auto-rendering mode
             */
            if ($returnedResponse === false) {
                if ($implicitView === true) {
                    if (is_object($controller)) {

                        $renderStatus = true;

                        /**
                         * This allows to make a custom view render
                         */
                        if (is_object($eventsManager)) {
                            $renderStatus = $eventsManager->fire("application:viewRender", $this, $view);
                        }

                        /**
                         * Check if the view process has been treated by the developer
                         */
                        if ($renderStatus !== false) {

                            /**
                             * Automatic render based on the latest controller executed
                             */
                            $view->render(
                                $dispatcher->getControllerName(),
                                $dispatcher->getActionName(),
                                $dispatcher->getParams()
                            );
                        }
                    }
                }
            }

            /**
             * Finish the view component (stop output buffering)
             */
            if ($implicitView === true) {
                $view->finish();
            }

            if ($returnedResponse === false) {

                $response = $dependencyInjector->getShared("response");
                if ($implicitView === true) {

                    /**
                     * The content returned by the view is passed to the response service
                     */
                    $response->setContent($view->getContent());
                }

            } else {

                /**
                 * We don't need to create a response because there is one already created
                 */
                $response = $possibleResponse;
            }
        }

        /**
         * Calling beforeSendResponse
         */
        if (is_object($eventsManager)) {
            $eventsManager->fire("application:beforeSendResponse", $this, $response);
        }

        /**
         * Headers and Cookies are automatically send
         */
        $response->sendHeaders();
        $response->sendCookies();

        /**
         * Return the response
         */
        return $response;
    }
}
