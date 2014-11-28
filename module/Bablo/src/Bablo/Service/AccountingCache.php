<?php

namespace Bablo\Service;

interface AccountingCache {
    function put($key, $data);
    function get($key);
    function invalidate($iserId);
}
