<?php
declare(strict_types=1);

namespace Ekvio\Integration\Extractor\Tests;

use Ekvio\Integration\Extractor\DataFromCsv;
use League\Csv\Statement;
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
"parent"|"child"|"title"
"parentA"|"childA"|"titleA"
EOF;
        $extractor = DataFromCsv::fromString($string)
            ->delimiter('|')
            ->headerOffset(0)
            ->useKeys(true);
        $extracted = $extractor->extract();

        $this->assertIsArray($extracted);
        $this->assertEquals(
            ['parent' => 'parentA', 'child' => 'childA', 'title' => 'titleA'],
            array_shift($extracted)
        );
    }

    public function testGetRecordsWithStatement()
    {
        $string = <<<EOF
"parent"|"child"|"title"
"parentA"|"childA"|"titleA"
"parentB"|"childB"|"titleB"
"parentC"|"childC"|"titleC"
EOF;

        $extractor = DataFromCsv::fromString($string)
            ->delimiter('|')
            ->headerOffset(0)
            ->statement((new Statement())->offset(2)->limit(1));

        $extracted = $extractor->extract();
        $this->assertIsArray($extracted);
        $this->assertEquals(
            ['parent' => 'parentC', 'child' => 'childC', 'title' => 'titleC'],
            array_shift($extracted)
        );
    }
}