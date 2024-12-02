<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests;

use PhpMyAdmin\Config;
use PhpMyAdmin\Current;
use PhpMyAdmin\ListDatabase;
use PhpMyAdmin\UserPrivilegesFactory;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListDatabase::class)]
class ListDatabaseTest extends AbstractTestCase
{
    /**
     * ListDatabase instance
     */
    private ListDatabase $object;

    /**
     * SetUp for test cases
     */
    protected function setUp(): void
    {
        parent::setUp();

        $dbi = $this->createDatabaseInterface();
        $config = new Config();
        $config->selectedServer['DisableIS'] = false;
        $config->selectedServer['only_db'] = ['single\\_db'];
        $this->object = new ListDatabase($dbi, $config, new UserPrivilegesFactory($dbi));
    }

    /**
     * Test for ListDatabase::exists
     */
    public function testExists(): void
    {
        $dbi = $this->createDatabaseInterface();
        $config = new Config();
        $config->selectedServer['DisableIS'] = false;
        $config->selectedServer['only_db'] = ['single\\_db'];
        $arr = new ListDatabase($dbi, $config, new UserPrivilegesFactory($dbi));
        self::assertTrue($arr->exists('single_db'));
    }

    public function testGetList(): void
    {
        $dbi = $this->createDatabaseInterface();
        $config = new Config();
        $config->selectedServer['DisableIS'] = false;
        $config->selectedServer['only_db'] = ['single\\_db'];
        $arr = new ListDatabase($dbi, $config, new UserPrivilegesFactory($dbi));

        Current::$database = 'db';
        self::assertSame(
            [['name' => 'single_db', 'is_selected' => false]],
            $arr->getList(),
        );

        Current::$database = 'single_db';
        self::assertSame(
            [['name' => 'single_db', 'is_selected' => true]],
            $arr->getList(),
        );
    }

    /**
     * Test for checkHideDatabase
     */
    public function testCheckHideDatabase(): void
    {
        Config::getInstance()->selectedServer['hide_db'] = 'single\\_db';
        self::assertEquals(
            $this->callFunction(
                $this->object,
                ListDatabase::class,
                'checkHideDatabase',
                [],
            ),
            '',
        );
    }
}
