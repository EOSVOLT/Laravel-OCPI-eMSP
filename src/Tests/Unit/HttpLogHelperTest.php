<?php

namespace Ocpi\Tests\Unit;

use Ocpi\Support\Helpers\HttpLogHelper;
use Ocpi\Tests\TestCase;

class HttpLogHelperTest extends TestCase
{
    public function testEmptyBodyIsNotLogged(): void
    {
        $this->assertSame('', HttpLogHelper::body(null));
        $this->assertSame('', HttpLogHelper::body(''));
        $this->assertSame('', HttpLogHelper::body([]));
        $this->assertSame('', HttpLogHelper::body('[]'));
    }

    public function testJsonBodyIsPrettyPrintedOnANewLine(): void
    {
        $expected = PHP_EOL . json_encode(['status_code' => 1000, 'url' => 'https://a/b'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->assertSame($expected, HttpLogHelper::body('{"status_code":1000,"url":"https://a/b"}'));
        $this->assertSame($expected, HttpLogHelper::body(['status_code' => 1000, 'url' => 'https://a/b']));
    }

    public function testNonJsonBodyIsLoggedAsIs(): void
    {
        $this->assertSame(PHP_EOL . '<html>Bad Gateway</html>', HttpLogHelper::body("<html>Bad Gateway</html>\n"));
    }

    public function testBodyIsTruncatedAtMaxLength(): void
    {
        $body = HttpLogHelper::body(str_repeat('a', 50), 10);

        $this->assertSame(PHP_EOL . str_repeat('a', 10) . PHP_EOL . '... [truncated, 50 characters in total]', $body);
    }

    public function testZeroMaxLengthMeansNoLimit(): void
    {
        $this->assertSame(PHP_EOL . str_repeat('a', 50), HttpLogHelper::body(str_repeat('a', 50), 0));
    }

    public function testRequestIdTag(): void
    {
        $this->assertSame(' [request_id: abc]', HttpLogHelper::requestIdTag('abc'));
    }
}
