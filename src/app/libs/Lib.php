<?php


namespace easyadmin\app\libs;


use think\app\Url;

class Lib
{
    public static function formatUrl($url, $params)
    {

        if ($url instanceof Url) {
            $url->suffix('');
            $url = (string)$url;
        }
        if (substr($url, 0, 11) === 'javascript:') {
            return $url;
        } elseif (substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://') {
            $url = $url . '?' . http_build_query($params);
        } elseif (substr($url, 0, 6) === 'window' || substr($url, 0, 7) === 'history') {
            $url = 'javascript:' . $url;
        } else if (stripos($url, '?') !== false) {
            $url .= '&' . http_build_query($params);
        } else {
            $url = url($url, $params);
        }
        return $url;
    }


    public function getArrayValue(array $arr, string $field, $default = null)
    {
        if (array_key_exists($field, $arr) && $arr[$field]) {
            return $arr[$field];
        } else {
            return $default;
        }

    }

    /**
     * 渲染模板的时候,没有传递参数,取当前访问的方法
     * @param string $path
     * @return mixed|string
     */
    public function getViewPath($path = '')
    {
        if ($path) return $path;
        return request()->controller() . ':' . request()->action();
    }

}
