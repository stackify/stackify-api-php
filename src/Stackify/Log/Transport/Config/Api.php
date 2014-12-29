<?php

namespace Stackify\Log\Transport\Config;

class Api
{
    const API_BASE_URL = 'https://api.stackify.com';
    const API_CALL_LOGS = '/Log/Save';
    const API_MAX_TIME = 5;
    const API_VERSION_HEADER = 'V1';
}