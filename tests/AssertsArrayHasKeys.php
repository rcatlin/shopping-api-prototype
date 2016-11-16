<?php

namespace Tests;

trait AssertsArrayHasKeys
{
    public function assertArrayHasKeys(array $keys, array $data)
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $data);
        }
    }
}
