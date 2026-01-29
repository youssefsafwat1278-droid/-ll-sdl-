<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\SqlServerConnector as BaseSqlServerConnector;
use PDO;

class SqlServerConnector extends BaseSqlServerConnector
{
    public function getOptions(array $config)
    {
        // Avoid unsupported PDO attributes on some sqlsrv driver builds.
        return [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    }
}
