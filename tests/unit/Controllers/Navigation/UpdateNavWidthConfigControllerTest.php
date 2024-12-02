<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers\Navigation;

use PhpMyAdmin\Config;
use PhpMyAdmin\Controllers\Navigation\UpdateNavWidthConfigController;
use PhpMyAdmin\Http\Factory\ServerRequestFactory;
use PhpMyAdmin\Message;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UpdateNavWidthConfigController::class)]
final class UpdateNavWidthConfigControllerTest extends AbstractTestCase
{
    #[DataProvider('validParamsProvider')]
    public function testValidParam(string $value, int $expected): void
    {
        $request = ServerRequestFactory::create()->createServerRequest('POST', 'https://example.com/')
            ->withParsedBody(['value' => $value]);

        $config = new Config();
        $responseRenderer = new ResponseRenderer();
        $controller = new UpdateNavWidthConfigController($responseRenderer, $config);
        $controller($request);

        self::assertSame($expected, $config->settings['NavigationWidth']);
        self::assertSame([], $responseRenderer->getJSONResult());
        self::assertTrue($responseRenderer->hasSuccessState(), 'Should be a successful response.');
    }

    /** @return iterable<array{string, int}> */
    public static function validParamsProvider(): iterable
    {
        yield ['0', 0];
        yield ['1', 1];
        yield ['240', 240];
    }

    /** @param string|string[] $value */
    #[DataProvider('invalidParamsProvider')]
    public function testInvalidParams(array|string $value): void
    {
        $request = ServerRequestFactory::create()->createServerRequest('POST', 'https://example.com/')
            ->withParsedBody(['value' => $value]);

        $config = new Config();
        $responseRenderer = new ResponseRenderer();
        $controller = new UpdateNavWidthConfigController($responseRenderer, $config);
        $controller($request);

        self::assertSame(
            ['message' => Message::error('Unexpected parameter value.')->getDisplay()],
            $responseRenderer->getJSONResult(),
        );
        self::assertFalse($responseRenderer->hasSuccessState(), 'Should be a failed response.');
    }

    /** @return iterable<array{string|string[]}> */
    public static function invalidParamsProvider(): iterable
    {
        yield [''];
        yield ['invalid'];
        yield [['invalid']];
        yield ['-1'];
    }

    public function testFailedConfigSaving(): void
    {
        $request = ServerRequestFactory::create()->createServerRequest('POST', 'https://example.com/')
            ->withParsedBody(['value' => '240']);

        $config = self::createStub(Config::class);
        $config->method('setUserValue')->willReturn(Message::error('Could not save configuration'));
        $responseRenderer = new ResponseRenderer();
        $controller = new UpdateNavWidthConfigController($responseRenderer, $config);
        $controller($request);

        self::assertSame(
            ['message' => Message::error('Could not save configuration')->getDisplay()],
            $responseRenderer->getJSONResult(),
        );
        self::assertFalse($responseRenderer->hasSuccessState(), 'Should be a failed response.');
    }
}
