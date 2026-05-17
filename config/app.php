<?php

return [
    'name' => 'Nepal Bulletin',
    'url' => 'http://localhost/newsportal',
    'env' => 'development',
    'debug' => true,
    'openai_api_key' => getenv('OPENAI_API_KEY') ?: '',
];
