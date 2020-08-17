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
        $extractor = DataFromCsv::fromString($string)->setDelimiter(',')->setHeaderOffset(0);

        $this->assertIsArray($extractor->extract());
        $this->assertEquals(
            [1 => ['parent' => 'parentA', 'child' => 'childA', 'title' => 'titleA']],
            $extractor->extract()
        );
    }
}