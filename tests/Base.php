<?php

namespace App\Tests;

use App\App;
use App\Db;

abstract class Base extends \PHPUnit_Framework_TestCase
{

    /** @var \App\Db */
    protected $db;

    public function setUp()
    {
        $this->db = new Db();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");
        $this->db->query("DROP TABLE IF EXISTS `keywords`");
        $this->db->query("DROP TABLE IF EXISTS `items`");
        $this->db->query("DROP TABLE IF EXISTS `users`");
        $this->db->query("DROP TABLE IF EXISTS `groups`");
        $this->db->query("DROP TABLE IF EXISTS `user_groups`");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1");
        $this->db->install();
        if (!is_dir($this->dataDir())) {
            mkdir($this->dataDir());
        }
    }

    public function tearDown()
    {
        App::deleteDir($this->dataDir());
    }

    protected function dataDir()
    {
        return __DIR__.'/data';
    }
}
