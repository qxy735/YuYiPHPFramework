<?php namespace System\Http;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

use System\Support\Facades\Config;
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
}