<?php
declare(strict_types=1);

namespace Ekvio\Integration\Extractor;

use Ekvio\Integration\Contracts\Extractor;
use League\Csv\Exception;
use League\Csv\Reader;

/**
 * Class UsersFromCsv
 * @package Ekvio\Integration\Extractor
 */
class UsersFromCsv implements Extractor
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * UsersFromCsv constructor.
     */
    private function __construct(){}

    /**
     * @param string $path
     * @param string $mode
     * @param null $context
     * @return static
     */
    public static function fromFile(string $path, string $mode = 'r', $context = null): self
    {
        $self = new self();
        $self->reader = Reader::createFromPath($path, $mode, $context);

        return $self;
    }

    /**
     * @param string $content
     * @return static
     */
    public static function fromString(string $content = ''): self
    {
        $self = new self();
        $self->reader = Reader::createFromString($content);

        return $self;
    }

    /**
     * @param string $delimiter
     * @return $this
     * @throws Exception
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->reader->setDelimiter($delimiter);
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     * @throws Exception
     */
    public function setHeaderOffset(int $offset): self
    {
        $this->reader->setHeaderOffset($offset);
        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    public function extract(array $options = []): array
    {
        return iterator_to_array($this->reader->getRecords($options));
    }
}