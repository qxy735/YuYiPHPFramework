<?php namespace System\Http;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Config;
use System\Symfony\Component\HttpFoundation\ParameterBag;
use System\Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    /**
     * 创建请求
     *
     * @param SymfonyRequest $request
     *
     * @return SymfonyRequest
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        if ($request instanceof static) {
            return $request;
        }

        return with(new static)->duplicate(

            $request->query->all(), $request->request->all(), $request->attributes->all(),

            $request->cookies->all(), $request->files->all(), $request->server->all()
        );
    }

    /**
     * 获取模式下的 Uri
     *
     * @param $mode
     *
     * @return string
     */
    public function getModeUri($mode)
    {
        switch ($mode) {
            case URL_MODE_NORMAL :
                $pathInfo = $this->getNormalUri();
                break;
            case URL_MODE_PATHINFO :
                $pathInfo = parseUri($this->getPathInfo());
                break;
            case URL_MODE_COMP :
                $pathInfo = $this->getCompUri();
                break;
            default :
                $pathInfo = '/';
        }

        return $pathInfo;
    }

    /**
     * 获取普通模式 Uri
     *
     * @return string
     */
    public function getNormalUri()
    {
        $pathInfo = '/';

        $m = Config::get('config.url_module');

        if (isset($_GET[$m])) {
            $pathInfo .= $_GET[$m];

            unset($_GET[$m]);
        } else {
            return $pathInfo;
        }

        $a = Config::get('config.url_action');

        if (isset($_GET[$a])) {
            $pathInfo .= '/' . $_GET[$a];

            unset($_GET[$a]);
        }

        return $pathInfo;
    }

    /**
     * 获取兼容模式 Uri
     *
     * @return string
     */
    public function getCompUri()
    {
        $pathInfo = $this->getNormalUri();

        if ('/' === $pathInfo) {
            $pathInfo = parseUri($this->getPathInfo());
        }

        return $pathInfo;
    }

    /**
     * 获取所有请求参数
     *
     * @return array
     */
    public function all()
    {
        return array_merge_recursive($this->input(), $this->files->all());
    }

    /**
     * 请求参数是否存在
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 是否为空字符串
     *
     * @param  string $key
     *
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $boolOrArray = is_bool($this->input($key)) || is_array($this->input($key));

        return !$boolOrArray && trim((string)$this->input($key)) === '';
    }

    /**
     * 获取输入的参数数据
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();

        return array_get($input, $key, $default);
    }

    /**
     * 获取输入的资源
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }

    /**
     * 是否为 Json 格式
     *
     * @return bool
     */
    public function isJson()
    {
        return str_contains($this->header('CONTENT_TYPE'), '/json');
    }

    /**
     * 获取 Json 数据
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed|ParameterBag
     */
    public function json($key = null, $default = null)
    {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array)json_decode($this->getContent(), true));
        }

        if (is_null($key)) {
            return $this->json;
        }

        return array_get($this->json->all(), $key, $default);
    }

    /**
     * 获取头部信息
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed
     */
    public function header($key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }

    /**
     * 获取数据
     *
     * @param $source
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    protected function retrieveItem($source, $key, $default)
    {
        if (is_null($key)) {
            return $this->$source->all();
        } else {
            return $this->$source->get($key, $default, true);
        }
    }
}