<?php
declare(strict_types=1);

namespace Ekvio\Integration\Extractor\Tests;

use Ekvio\Integration\Extractor\DataFromCsv;
use PHPUnit\Framework\TestCase;

/**
 * Class UsersFromCsvTest
 * @package Ekvio\Integration\Extractor\Tests
 */
class DataFromCsvTest extends TestCase
{
    public function testGetRecordsFromString()
    {
        $string = <<<EOF
"parent","child","title"
"parentA","childA","titleA"
EOF;
        $extractor = DataFromCsv::fromString($string);
        $extracted = $extractor->extract(['delimiter' => ',', 'offset' => 0]);

        $this->assertIsArray($extracted);
        $this->assertEquals(
            [1 => ['parent' => 'parentA', 'child' => 'childA', 'title' => 'titleA']],
            $extracted
        );
    }
}