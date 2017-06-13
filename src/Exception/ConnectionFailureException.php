<?php
namespace GMO\Cache\Exception;

use Gmo\Common\Deprecated;

Deprecated::cls('\GMO\Cache\Exception\ConnectionFailureException');

class ConnectionFailureException extends CacheException {}
