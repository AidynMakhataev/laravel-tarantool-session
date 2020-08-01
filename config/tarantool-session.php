<?php

return [
    'host'          =>  env('TARANTOOL_SESSION_HOST'),
    'user'          =>  env('TARANTOOL_SESSION_USER'),
    'password'      =>  env('TARANTOOL_SESSION_PASSWORD'),
    'space'         =>  env('TARANTOOL_SESSION_SPACE', 'sessions'),
];
