<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

function parseData($data, $format): array
{
    if (mb_strtolower($format) == "json") {
        $result = json_decode($data, true);
    } elseif (mb_strtolower($format) == "yaml" || mb_strtolower($format) == "yml") {
        $result = Yaml::parse($data);
    } else {
        throw new \Exception("Format {$format} is not correct . Must be JSON or YAML\n");
    }

    return $result;
}
