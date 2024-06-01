<?php

return [
    'enable_sql_log' => env('ENABLE_SQL_LOG', false),
    'sql_log_level' => env('SQL_LOG_LEVEL', 'debug'),
    'sql_log_channel' => env('SQL_LOG_CHANNEL', 'sql-query-log'),
];
