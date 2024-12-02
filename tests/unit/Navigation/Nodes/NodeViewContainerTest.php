<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Navigation\Nodes;

use PhpMyAdmin\Config;
use PhpMyAdmin\Navigation\Nodes\NodeViewContainer;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NodeViewContainer::class)]
class NodeViewContainerTest extends AbstractTestCase
{
    /**
     * Test for __construct
     */
    public function testConstructor(): void
    {
        $parent = new NodeViewContainer(new Config());
        self::assertSame(
            [
                'text' => ['route' => '/database/structure', 'params' => ['tbl_type' => 'view', 'db' => null]],
                'icon' => ['route' => '/database/structure', 'params' => ['tbl_type' => 'view', 'db' => null]],
            ],
            $parent->links,
        );
        self::assertSame('views', $parent->realName);
        self::assertStringContainsString('viewContainer', $parent->classes);
    }
}
