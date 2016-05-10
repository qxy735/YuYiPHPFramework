<?php namespace System\Support;
/**
 * YuYi WorkRoom
 *
 * @version 1.1.0
 * @author QIXIEYU
 */

class Str
{
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) !== false) return true;
        }

        return false;
    }
}