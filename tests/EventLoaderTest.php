<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests;

use ArrayObject;
use PhpSoftBox\Events\EventDispatcher;
use PhpSoftBox\Events\EventLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

use function bin2hex;
use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function random_bytes;
use function rmdir;
use function sys_get_temp_dir;
use function unlink;

#[CoversClass(EventLoader::class)]
final class EventLoaderTest extends TestCase
{
    /**
     * Проверяет загрузку нескольких конфигураций событий из директории.
     */
    #[Test]
    public function testLoadsListenersFromDirectory(): void
    {
        $dir = sys_get_temp_dir() . '/psb-events-' . bin2hex(random_bytes(4));
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            $this->fail('Unable to create temp directory: ' . $dir);
        }

        $fileA = $dir . '/app.php';
        $fileB = $dir . '/users.php';

        $contentsA = <<<'PHP'
<?php

return static function (\PhpSoftBox\Events\EventDispatcher $events): void {
    $events->listen(\stdClass::class, static function (): void {});
};
PHP;

        $contentsB = <<<'PHP'
<?php

return static function (\PhpSoftBox\Events\EventDispatcher $events): void {
    $events->listen(\ArrayObject::class, static function (): void {});
};
PHP;

        file_put_contents($fileA, $contentsA);
        file_put_contents($fileB, $contentsB);

        try {
            $dispatcher = new EventDispatcher();
            $loader     = new EventLoader($dir);

            $loader->load($dispatcher);

            $definitions = $dispatcher->definitions();

            $this->assertArrayHasKey(stdClass::class, $definitions);
            $this->assertArrayHasKey(ArrayObject::class, $definitions);
            $this->assertCount(1, $definitions[stdClass::class]);
            $this->assertCount(1, $definitions[ArrayObject::class]);
        } finally {
            if (is_dir($dir)) {
                if (is_file($fileA)) {
                    unlink($fileA);
                }
                if (is_file($fileB)) {
                    unlink($fileB);
                }
                rmdir($dir);
            }
        }
    }
}
