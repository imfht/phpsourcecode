<?php
/**
 * @package   OpenCart
 * @author    Daniel Kerr
 * @copyright Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license   https://opensource.org/licenses/GPL-3.0
 * @author    Daniel Kerr
 * @see       https://www.opencart.com
 */

/**
 * URL class.
 */
class Url {
	/** @var string */
	private $url;
	/** @var Controller[] */
	private $rewrite = array();

	/**
	 * Constructor.
	 *
	 * @param string $url
	 * @param string $ssl Unused
	 */
	public function __construct($url, $ssl = '') {
		$this->url = $url;
	}

	/**
	 *
	 *
	 * @param Controller $rewrite
	 *
	 * @return void
	 */
	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}

	/**
	 *
	 *
	 * @param string          $route
	 * @param string|string[] $args
	 *
	 * @return string
	 */
	public function link($route, $args = '', $auto_admin_token = true) {
		$url = $this->url . 'index.php?route=' . (string)$route;

        // Add user_token to admin link if it's not passed in
        if ($auto_admin_token && is_admin() && $user_token = array_get(session()->data, 'user_token')) {
            if (is_array($args) && !in_array('user_token', $args)) {
                $args['user_token'] = $user_token;
            } else if (!str_contains($args, 'user_token')) {
                $args .= '&user_token=' . $user_token;
            }
        }

		if ($args) {
			if (is_array($args)) {
				$url .= '&amp;' . http_build_query($args);
			} else {
				$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
			}
		}

		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}

        if ($route == 'common/home') {
            $url = str_replace('index.php?route=common/home&amp;', '?', $url);
            $url = str_replace('index.php?route=common/home', '', $url);
        }
		return $url;
	}

    public function imageLink($imagePath)
    {
        return 'image/' . $imagePath;
    }

    public function cssLink($cssPath)
    {
        return 'catalog/view/' . $cssPath . '.css';
    }

    public function jsLink($jsPath)
    {
        return 'catalog/view/' . $jsPath . '.js';
    }

    public function getQueries()
    {
        return $this->getQueriesExclude();
    }

    public function getQueriesExclude($queries = [])
    {
        $queries[] = 'route'; // No need to get route
        $results = [];
        foreach (request()->get as $key => $value) {
            if (in_array($key, $queries)) {
                continue;
            }
            if (!empty($value)) {
                $results[$key] = $value;
            }
        }
        return $results;
    }

    public function getQueriesOnly($queries = [])
    {
        $results = [];
        if (!$queries) {
            return $results;
        }

        foreach ($queries as $key) {
            if ($value = array_get(request()->get, $key)) {
                if (!empty($value)) {
                    $results[$key] = $value;
                }
            }
        }
        return $results;
    }
}
