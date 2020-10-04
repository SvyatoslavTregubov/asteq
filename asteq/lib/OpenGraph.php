<?php

namespace Asteq;

class OpenGraph
{
    private static $_tags = [];

    public static function show()
    {
        global $APPLICATION;

        echo $APPLICATION->AddBufferContent(["\Asteq\OpenGraph", "get"]);
    }

    public static function get()
    {
        $result = "";

        foreach (self::$_tags as $type => $tags)
            foreach ($tags as $key => $value)
                $result .= '<meta property="' . $type . ':' . $key . '" content="' . $value . '"/>';

        return $result;
    }

    public static function add($type, $key, $value)
    {
        if (!key_exists($type, self::$_tags))
            self::$_tags[$type] = [];

        self::$_tags[$type][$key] = $value;
    }
}
