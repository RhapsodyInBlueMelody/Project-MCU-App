<?php
namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';
    
    // Initialize as empty array first
    public array $default = [];

    public function __construct()
    {
        // Set default connection details in constructor
        $this->default = [
            'DSN'          => '',
            'hostname'     => 'localhost:3306',
            'username'     => 'alfr7592_faiz',
            'password'     => 'R8JkjsbSLjzU',
            'database'     => 'alfr7592_mcu',
            'DBDriver'     => 'MySQLi',
            'DBPrefix'     => '',
            'pConnect'     => false,
            'DBDebug'      => (ENVIRONMENT !== 'production'),
            'charset'      => 'utf8mb4',
            'DBCollat'     => 'utf8mb4_general_ci',
            'swapPre'      => '',
            'encrypt'      => false,
            'compress'     => false,
            'strictOn'     => false,
            'failover'     => [],
            'port'         => 3306,
            'numberNative' => false,
            'foundRows'    => false,
            'dateFormat'   => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ];

        parent::__construct();

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}