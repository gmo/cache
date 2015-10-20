<?php

namespace GMO\Cache\Tests\IntegrationTest;

use GMO\Cache\ArrayPredis;

class ArrayPredisTest extends PredisTest
{
    public function createClient()
    {
        return new ArrayPredis();
    }
}
