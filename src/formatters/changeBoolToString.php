<?php

namespace Differ\formatters\changeBoolToString;

function changeBoolToString($value)
{
    if (is_bool($value)) {
        return ($value) ? "true" : "false";
    } else {
        return $value;
    }
}
