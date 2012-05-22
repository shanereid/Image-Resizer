<?php defined('SYSPATH') or die('No direct access allowed.');

$connectionLocal = array(
    'hostname'   => '127.0.0.1',
    'database'   => 'resizer',
    'username'   => 'shane_local',
    'password'   => '7aY5mEDNxuZpKvZG',
    'persistent' => FALSE,
);

$connectionLocalWill = array(
    'hostname'   => '127.0.0.1:8889',
    'database'   => 'homebase_inspire',
    'username'   => 'will',
    'password'   => 'database',
    'persistent' => FALSE,
);

$connectionStaging = array(
    'hostname'   => 'localhost',
    'database'   => 'homebase_inspire',
    'username'   => 'admin',
    'password'   => '3unA97FWDEk6m',
    'persistent' => FALSE,
);

$connectionProd = array(
	'hostname'   => 'localhost',
	'database'   => 'homebase_inspire',
	'username'   => 'websites',
	'password'   => 'w3bsit3s',
	'persistant' => false,
);

$connections = array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 * array    variables    system variables as "key => value" pairs
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'   => '[DB_Host]',
			'database'   => '[DB_Name]',
			'username'   => '[DB_User]',
			'password'   => '[DB_Password]',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	)
);

switch ($_SERVER['SERVER_NAME']) {
    case 'resizer.local':
        $connections['default']['connection'] = $connectionLocal;
        break;
    case "homebase.will":
        $connections['default']['connection'] = $connectionLocalWill;
        break;
    case 'homebase.staging.maido.com':
        $connections['default']['connection'] = $connectionStaging;
        break;
    case 'homebaseinspire.maido.com':
    	$connections['default']['connection'] = $connectionProd;
};

return $connections;