<?php

namespace GMO\Cache;

use Gmo\Common\Deprecated;

Deprecated::cls('\GMO\Cache\ArrayPredis', null, '\Gmo\Common\Cache\ArrayPredis');

/**
 * @deprecated
 */
class ArrayPredis extends \Gmo\Common\Cache\ArrayPredis
{
}
