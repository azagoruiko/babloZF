<?php

namespace BabloTest\Controller;

use Zend\Authentication\Storage\StorageInterface;


class StorageInterfaceMock implements StorageInterface {
    private $result;

    public function clear() {
        $this->result = null;
    }

    public function isEmpty() {
        return $this->result == null;
    }

    public function read() {
        echo "{$this->result} read\n";
        return $this->result;
    }

    public function write($contents) {
        echo "$contents written\n";
        $this->result = $contents;
    }

}
