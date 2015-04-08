<?php

namespace Doctrine\Tests\DBAL\Functional\Driver;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\Tests\DbalFunctionalTestCase;
use Doctrine\Tests\TestUtil;

class PDOPgsqlConnectionTest extends DbalFunctionalTestCase
{
    protected function setUp()
    {
        if ( ! extension_loaded('pdo_pgsql')) {
            $this->markTestSkipped('pdo_pgsql is not loaded.');
        }

        parent::setUp();

        if ( ! $this->_conn->getDatabasePlatform() instanceof PostgreSqlPlatform) {
            $this->markTestSkipped('PDOPgsql only test.');
        }
    }

    /**
     * @dataProvider charset
     *
     * @param string $charset
     */
    public function testPdoPgsqlConnectionWithCharset($charset)
    {
        $params = $this->_conn->getParams();
        $params['charset'] = $charset;

        $connection = DriverManager::getConnection(
            $params,
            $this->_conn->getConfiguration(),
            $this->_conn->getEventManager()
        );

        $this->assertEquals($charset, $connection->query("SHOW client_encoding")->fetch(\PDO::FETCH_COLUMN));
    }

    public function charset()
    {
       return array(
           array("UTF8"),
           array("LATIN1")
       );
    }
}