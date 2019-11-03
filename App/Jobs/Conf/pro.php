<?php
/* product env */
return [
    'APP_NAME'               => APP_NAME,
    'SITE_DOMAIN'            => 'jobs.tt.com',
    'APP_TEMP_DIR'           => ROOT . '/App/' . APP_NAME . '/Runtime/Temp',
    'APP_LOG_DIR'            => ROOT . '/App/' . APP_NAME . '/Runtime/Log',
    'APP_PUBLIC_DIR'         => ROOT . '/Public',
    'APP_SESSION_AUTO_START' => true,
    'APP_DEBUG'              => [
        "ENABLE"        => false,
        "LOG"           => 1,
        "DISPLAY_ERROR" => 1,
    ],
    'TEMPLATE'               => [
        'view_path'    => ROOT . '/App/' . APP_NAME . '/View/', // 模板文件目录
        'cache_path'   => ROOT . '/App/' . APP_NAME . '/Runtime/View/',  // 编译后的模板文件缓存目录
        'view_suffix'  => 'html',        // 模板文件后缀
        'tpl_deny_php' => false, // 默认模板引擎是否禁用PHP原生代码
    ],
    'SWOOLE'                 => [
        "SERVER" => [
            "LISTEN"          => "0.0.0.0",
            'PORT'            => 9501,
            "SERVER_NAME"     => "jobs",
            "RUN_MODE"        => SWOOLE_PROCESS,    //不建议更改此项
            "CONTROLLER_POOL" => true,              //web或web socket模式有效
            "SERVER_TYPE"     => \Core\Swoole\Config::SERVER_TYPE_WEB,
            "SESSION_NAME"    => 'SESSION_NAME',
            "CONFIG"          => [
                'enable_static_handler' => true,
                'document_root'         => ROOT . '/Public',
                'worker_num'            => 4,
                'task_worker_num'       => 4,
                "task_max_request"      => 10,
                'max_request'           => 5000,
                'debug_mode'            => 0,
            ],
        ],
    ],
    /**
     * databases
     */
    'DATABASE'               => [
        'mysql' => [
            'listener' => true,
            'options'  => [
                'type'            => 'mysql',               // 数据库类型
                'hostname'        => 'mysql',               // 服务器地址
                'database'        => 'test',                // 数据库名
                'username'        => 'root',                // 用户名
                'password'        => '123456',              // 密码
                'hostport'        => '',                    // 端口
                'dsn'             => '',                    // 连接dsn
                'params'          => [],                    // 数据库连接参数
                'charset'         => 'utf8',                // 数据库编码默认采用utf8
                'prefix'          => 'jobs_',               // 数据库表前缀
                'debug'           => false,                 // 数据库调试模式
                'deploy'          => 0,                     // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
                'rw_separate'     => false,                 // 数据库读写是否分离 主从式有效
                'master_num'      => 1,                     // 读写分离后 主服务器数量
                'slave_no'        => '',                    // 指定从服务器序号
                'fields_strict'   => true,                  // 是否严格检查字段是否存在
                'resultset_type'  => '',                    // 数据集返回类型
                'auto_timestamp'  => false,                 // 自动写入时间戳字段
                'datetime_format' => 'Y-m-d H:i:s',         // 时间字段取出后的默认时间格式
                'sql_explain'     => false,                 // 是否需要进行SQL性能分析
                'builder'         => '',                    // Builder类
                'query'           => '\\think\\db\\Query',  // Query类(请勿删除)
                'break_reconnect' => true,                  // 是否需要断线重连
                'schema_path'     => '',                    // 数据字段缓存路径
                'class_suffix'    => false,                 // 模型类后缀
            ],
        ],
    ],
];
