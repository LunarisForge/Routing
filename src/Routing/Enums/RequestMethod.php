<?php

namespace LunarisForge\Routing\Enums;

enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
}
