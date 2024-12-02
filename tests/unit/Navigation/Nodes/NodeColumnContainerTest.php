<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Navigation\Nodes;

use PhpMyAdmin\Config;
use PhpMyAdmin\Navigation\Nodes\NodeColumnContainer;
use PhpMyAdmin\Navigation\NodeType;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NodeColumnContainer::class)]
final class NodeColumnContainerTest extends AbstractTestCase
{
    public function testColumnContainer(): void
    {
        $nodeColumnContainer = new NodeColumnContainer(new Config());
        self::assertSame('Columns', $nodeColumnContainer->name);
        self::assertSame(NodeType::Container, $nodeColumnContainer->type);
        self::assertFalse($nodeColumnContainer->isGroup);
        self::assertSame(['image' => 'pause', 'title' => 'Columns'], $nodeColumnContainer->icon);
        self::assertSame(
            [
                'text' => ['route' => '/table/structure', 'params' => ['db' => null, 'table' => null]],
                'icon' => ['route' => '/table/structure', 'params' => ['db' => null, 'table' => null]],
            ],
            $nodeColumnContainer->links,
        );
        self::assertSame('columns', $nodeColumnContainer->realName);
        self::assertCount(1, $nodeColumnContainer->children);
        self::assertArrayHasKey(0, $nodeColumnContainer->children);
        $newNode = $nodeColumnContainer->children[0];
        self::assertSame('New', $newNode->name);
        self::assertSame('New', $newNode->title);
        self::assertTrue($newNode->isNew);
        self::assertSame('new_column italics', $newNode->classes);
        self::assertSame(['image' => 'b_column_add', 'title' => 'New'], $newNode->icon);
        self::assertSame(
            [
                'text' => [
                    'route' => '/table/add-field',
                    'params' => ['field_where' => 'last', 'after_field' => '', 'db' => null, 'table' => null],
                ],
                'icon' => [
                    'route' => '/table/add-field',
                    'params' => ['field_where' => 'last', 'after_field' => '', 'db' => null, 'table' => null],
                ],
            ],
            $newNode->links,
        );
    }
}
