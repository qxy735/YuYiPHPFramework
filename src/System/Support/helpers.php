<?php
/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author  QIXIEYU
 */

if (!function_exists('str_contains')) {
    /**
     * 字符串包含指定内容检测
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    function str_contains($haystack, $needle)
    {
        return System\Support\Str::contains($haystack, $needle);
    }
}

if (!function_exists('array_except')) {
    /**
     * 获取不同数组值
     *
     * @param $array
     * @param $keys
     *
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array)$keys));
    }
}

if (!function_exists('array_set')) {
    /**
     * 设置数组值
     *
     * @param $array
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('array_get')) {
    /**
     * 获取数组中的指定键值
     *
     * @param      $array
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('object_get')) {
    /**
     * 获取对象中的指定内容
     *
     * @param      $object
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (!function_exists('data_get')) {
    /**
     * 获取数组或对象的指定内容
     *
     * @param      $target
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return value($default);
                }

                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('value')) {
    /**
     * 获取值
     *
     * @param $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('app_path')) {
    /**
     * 获取应用项目目录路径
     *
     * @param string $path
     *
     * @return string
     */
    function app_path($path = '')
    {
        return (app('path.base') . ($path ? '/' . $path : $path)) . '/' . APP_NAME;
    }
}

if (!function_exists('base_path')) {
    /**
     * 获取项目根目录路径
     *
     * @param string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return (app('path.base') . ($path ? '/' . $path : $path));
    }
}

if (!function_exists('app')) {
    /**
     * 获取 Application 应用对象
     *
     * @param null $make
     *
     * @return mixed
     */
    function app($make = null)
    {
        if (!is_null($make)) {
            return app()->make($make);
        }

        return System\Support\Facades\Facade::getFacadeApplication();
    }
}

if (!function_exists('storage_path')) {
    /**
     * 获取 storage 目录路径
     *
     * @param string $path
     *
     * @return string
     */
    function storage_path($path = '')
    {
        return app('path.storage') . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('with')) {
    /**
     * 获取对象
     *
     * @param $object
     *
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}

if (!function_exists('parseUri')) {
    /**
     * 解析 Uri 地址
     *
     * @param $uri
     *
     * @return string
     */
    function parseUri($uri)
    {
        if (strlen($uri) <= 1) {
            return $uri;
        }

        return trim($uri, '/');
    }
}

if (!function_exists('env')) {
    /**
     * 获取环境
     *
     * @return mixed
     */
    function env()
    {
        return app()->getEnv();
    }
}

if (!function_exists('go')) {
    /**
     * Url 地址跳转
     *
     * @param        $url
     * @param int    $time
     * @param string $notice
     */
    function Go($url, $time = 0, $notice = '')
    {
        if (!headers_sent()) {
            0 == $time ? header('location:' . $url) : header("refresh:{$time};url={$url}");
        } else {
            echo "<meta http-equiv='refresh' content='{$time};url={$url}'>";
        }

        $time and exit($notice);
    }
}
