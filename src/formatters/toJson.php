<?php

namespace Differ\formatters\toJson;

function toJson($diff)
{
    return json_encode($diff);
}
