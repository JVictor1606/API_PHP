<?php

namespace Services;

class Resource
{

    public static function collect(array $args)
    {
        if (!isset($args[0]) || !is_array($args)) {
            return json_encode(static::toArray($args[0]));
        }

        $response = [];

        foreach ($args as $arg) {
            $response[] = static::toArray($arg);
        }
        return $response;
    }

    public static function toArray($args)
    {
        return [];
    }
}
