<?php namespace System\Routing;

use System\Http\Request;
use System\Support\Facades\Config;
use Closure;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */
class Router
{
    /**
     * 请求方式
     *
     * @var array
     */
    public static $verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS');

    /**
     * 路由规则
     *
     * @var array
     */
    protected $rules = array();

    /**
     * 路由过滤规则
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Get 请求处理
     *
     * @param $uri
     * @param $action
     *
     * @return $this
     */
    public function get($uri, $action)
    {
        return $this->addRoute(array('GET', 'HEAD'), $uri, $action);
    }

    /**
     * POST 请求处理
     *
     * @param $uri
     * @param $action
     *
     * @return $this
     */
    public function post($uri, $action)
    {
        return $this->addRoute(array('POST'), $uri, $action);
    }

    /**
     * 任意请求处理
     *
     * @param $uri
     * @param $action
     *
     * @return $this
     */
    public function any($uri, $action)
    {
        return $this->addRoute(self::$verbs, $uri, $action);
    }

    /**
     * 路由组处理
     *
     * @param          $filter
     * @param callable $closure
     */
    public function group($filter, Closure $closure)
    {
        // 获取当前组前路由规则
        $oldRules = $this->getRules();

        call_user_func($closure);

        // 获取当前组后路由规则
        $rules = $this->getRules();

        // 重置路由规则
        array_walk($rules, function (&$rule, $action) use ($oldRules, $filter) {
            if (!isset($oldRules[$action])) {
                $rule['filter'] = $filter;
            }
        });

        // 添加路由规则
        $this->setRules($rules);
    }

    /**
     * 增加路由过滤处理规则
     *
     * @param          $name
     * @param callable $closure
     */
    public function filter($name, Closure $closure)
    {
        $this->filters[$name] = $closure;
    }

    /**
     * 路由过滤处理
     *
     * @param $name
     *
     * @return mixed
     */
    public function filterCall($name)
    {
        if (!isset($this->filters[$name])) {
            return false;
        }

        return call_user_func($this->filters[$name]);
    }


    /**
     * 添加路由规则
     *
     * @param $methods
     * @param $uri
     * @param $action
     *
     * @return $this
     */
    protected function addRoute($methods, $uri, $action)
    {
        $uri = parseUri($uri);

        $this->rules[$uri]['method'] = $methods;

        $this->rules[$uri]['action'] = $action;

        return $this;
    }

    /**
     * 移除路由规则
     */
    public function removeRules()
    {
        $this->rules = array();
    }

    /**
     * 获取路由规则
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * 设置路由规则
     *
     * @param array $rule
     */
    public function setRules(array $rule = array())
    {
        $this->rules = $rule;
    }

    /**
     * 路由执行
     *
     * @param Request $request
     *
     * @return $this|bool
     */
    public function dispatch(Request $request)
    {
        // 获取路由过则
        $rules = $this->getRules();

        // 获取 url 处理模式
        $mode = Config::get('config.url_mode');

        // 获取指定模式下的 Uri
        $pathInfo = parseUri($request->getModeUri($mode));

        if (!$rules) {
            return trigger_error('Please Define Your Routing Address First.', E_USER_ERROR);
        }

        // 获取当前请求方法
        $method = $request->getMethod();

        $route = array_get($rules, $pathInfo, '');

        // 判断对应的路由处理地址是否存在
        if (!$route) {
            return trigger_error('Routing Address Not Found !', E_USER_ERROR);
        }

        // 判断请求方法是否允许
        if (!in_array($method, $route['method'])) {
            return trigger_error('Request Method Not Allow !', E_USER_ERROR);
        }

        // 获取路由处理方法
        $action = $route['action'];

        // 前置路由过滤处理
        if (isset($route['filter']['before'])) {
            $this->filterCall($route['filter']['before']);
        }

        if ($action instanceof \Closure) {
            call_user_func($action);
        } else {
            if (!$action) {
                return trigger_error('Request Deal Method Not Exists !', E_USER_ERROR);
            }

            $action = explode('@', $action);

            $controller = $action[0];

            $method = array_get($action, 1, 'index');

            $class = new $controller();

            $class->{$method}();
        }

        // 后置路由过滤处理
        if (isset($route['filter']['after'])) {
            $this->filterCall($route['filter']['after']);
        }

        return $this;
    }
}