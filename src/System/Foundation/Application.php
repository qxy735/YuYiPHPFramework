<?php namespace System\Foundation;

use System\Exception\Handler;
use System\Filesystem\File;
use System\Http\Request;
use System\Config\FileLoader;
use Closure;
use System\Routing\Router;
use System\Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use System\Support\Facades\Facade;

/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */
class Application extends Container
{
    /**
     * 环境变量
     *
     * @var
     */
    protected $env;

    /**
     * 是否启动
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * 启动回调方法
     *
     * @var array
     */
    protected $bootedCallbacks = array();

    /**
     * Http 请求处理类名
     *
     * @var string
     */
    protected static $requestClass = 'System\Http\Request';

    /**
     * 框架应用初始化
     *
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        // 注册绑定基础对象 Request And Container
        $this->registerBaseBindings($request ? : $this->buildNewRequest());

        // 注册路由
        $this->registerRouter();

        // 注册错误异常处理机制
        $this->registerBaseHandler();

        // 注册文件处理对象
        $this->registerFileSystem();
    }

    /**
     * 注册绑定基础对象
     *
     * @param $registered
     */
    protected function registerBaseBindings($registered)
    {
        // 注册 Request 实例对象到容器
        $this->instance('request', $registered);

        // 注册当前对象到容器
        $this->instance(get_parent_class(), $this);
    }

    /**
     * 注册路由对象实例
     */
    protected function registerRouter()
    {
        $this->instance('router', new Router());
    }

    /**
     * 注册错误异常处理机制
     */
    protected function registerBaseHandler()
    {
        // 实例 handler 对象
        $this->instance('handler', new Handler($this));

        foreach (array('Error', 'Exception', 'Shutdown') as $deal) {
            forward_static_call(array('System\Exception\Handler', "register{$deal}Handler"));
        }
    }

    /**
     * 注册文件处理对象
     */
    protected function registerFileSystem()
    {
        $this->instance('file', new File());
    }

    /**
     * 构造请求对象
     *
     * @return mixed
     */
    protected function buildNewRequest()
    {
        // 返回 Request 请求对象
        return forward_static_call(array(static::$requestClass, 'createFromGlobals'));
    }

    /**
     * 检测环境
     *
     * @param callable $closure
     *
     * @return mixed
     */
    public function detectEnvironment(Closure $closure)
    {
        return $this->env = call_user_func($closure, $this);
    }

    /**
     * 获取环境值
     *
     * @return string
     */
    public function getEnvironment()
    {
        // 获取系统环境信息
        $env = getenv('YUYI_ENV');

        if ($env) {
            switch ($env) {
                case APP_ENV_PRODUCTION :
                    return APP_ENV_PRODUCTION;
                case APP_ENV_PRE_RELEASE :
                    return APP_ENV_PRE_RELEASE;
                case APP_ENV_PRESURE_TEST :
                    return APP_ENV_PRESURE_TEST;
                case APP_ENV_TEST :
                    return APP_ENV_TEST;
                case APP_ENV_LOCAL :
                    return APP_ENV_LOCAL;
            }
        } else {
            // 如果没有指定环境变量，则通过 IP 来判断
            $serverIp = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : gethostbyname(gethostname());

            // 获取环境 IP 配置
            $ips = $this->getIpConfig();

            foreach ($ips as $env => $ip) {
                if ($env === APP_ENV_PRODUCTION && str_contains($serverIp, $ip)) {
                    return APP_ENV_PRODUCTION;
                } elseif ($env === APP_ENV_PRE_RELEASE && in_array($serverIp, $ip)) {
                    return APP_ENV_PRE_RELEASE;
                } elseif ($env === APP_ENV_PRESURE_TEST && in_array($serverIp, $ip)) {
                    return APP_ENV_PRESURE_TEST;
                } elseif ($env === APP_ENV_TEST && in_array($serverIp, $ip)) {
                    return APP_ENV_TEST;
                }
            }

            return APP_ENV_LOCAL;
        }
    }

    /**
     * 获取环境 IP 配置信息
     *
     * @return array
     */
    public function getIpConfig()
    {
        // 定义需要获取的 ip 配置文件路径
        $path = BASE_PATH . '/ipconfig.php';

        return $this['file']->load($path) ? : array();
    }

    /**
     * 目录路径绑定
     *
     * @param array $paths
     */
    public function bindInstallPaths(array $paths)
    {
        foreach ($paths as $key => $value) {
            $this->instance("path.{$key}", $value);
        }
    }

    /**
     * 获取启动文件
     *
     * @return string
     */
    public function getBootstrapFile()
    {
        return __DIR__ . '/start.php';
    }

    /**
     * 注册框架核心工具类别名
     */
    public function registerCoreContainerAliases()
    {
        $aliases = array(
            'app' => 'System\Foundation\Application',
            'request' => 'System\Http\Request',
            'config' => 'System\Config\Repository',
            'router' => 'System\Routing\Router',
            'log' => 'System\Log\Writer',
            'redirect' => 'System\Routing\Redirect',
            'session' => 'System\Http\Session',
            'image' => 'System\Filesystem\Image',
        );

        foreach ($aliases as $key => $alias) {
            $this->alias($key, $alias);
        }
    }

    /**
     * 自动创建项目目录
     */
    public function createApplicationDirectories()
    {
        // 线上环境不再做目录创建及检测
        if (APP_ENV_PRODUCTION === $this->env) {
            return;
        }

        // 定义需要创建的目录名称
        $directories = array(
            'controllers',
            'models',
            'views',
            'config',
            'storage/logs',
            'storage/compile',
            'storage/cache',
        );

        foreach ($directories as $directorie) {
            is_dir($path = APP_PATH . "/{$directorie}") or mkdir($path, 0777, true);
        }
    }

    /**
     * 获取配置文件对象
     *
     * @return FileLoader
     */
    public function getConfigLoader()
    {
        return new FileLoader(new File, $this['path.app'] . '/config');
    }

    /**
     * 框架启动处理
     *
     * @param $callback
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) {
            $this->fireAppCallbacks(array($callback));
        }
    }

    /**
     * 是否启动
     *
     * @return mixed
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * 执行应用
     *
     * @param array $callbacks
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * 运行框架
     *
     * @param SymfonyRequest $request
     *
     * @return mixed
     */
    public function run(SymfonyRequest $request = null)
    {
        $request = $request ? : $this['request'];

        $this->refreshRequest($request = Request::createFromBase($request));

        $this->boot();

        return $this->dispatch($request);
    }

    /**
     * 启动框架
     */
    protected function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->bootApplication();
    }

    /**
     * 启动框架应用程序
     */
    protected function bootApplication()
    {
        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    /**
     * 刷新请求
     *
     * @param Request $request
     */
    protected function refreshRequest(Request $request)
    {
        $this->instance('request', $request);

        Facade::clearResolvedInstance('request');
    }

    /**
     * 发送请求
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function dispatch(Request $request)
    {
        return $this['router']->dispatch($request);
    }

    /**
     * 获取环境
     *
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }
}