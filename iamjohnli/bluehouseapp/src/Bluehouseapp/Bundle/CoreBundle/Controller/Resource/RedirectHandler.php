<?php



namespace Bluehouseapp\Bundle\CoreBundle\Controller\Resource;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Redirects helper.
 *
 */
class RedirectHandler
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Configuration $config, RouterInterface $router)
    {
        $this->router = $router;
        $this->config = $config;
    }

    /**
     * @param object $resource
     *
     * @return RedirectResponse
     */
    public function redirectTo($resource)
    {
        $parameters = $this->config->getRedirectParameters($resource);

        return $this->redirectToRoute(
            $this->config->getRedirectRoute('show'),
            $parameters
        );
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToIndex()
    {
        return $this->redirectToRoute($this->config->getRedirectRoute('index'), $this->config->getRedirectParameters());
    }

    /**
     * @param string $route
     * @param array  $data
     *
     * @return RedirectResponse
     */
    public function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer();
        }

        return $this->redirect($this->router->generate($route, $data));
    }

    /**
     * @param string  $url
     * @param integer $status
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToReferer()
    {
        return $this->redirect($this->config->getRequest()->headers->get('referer'));
    }
}
