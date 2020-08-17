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
class DataFromCsv implements Extractor
{
    private const DEFAULT_HEADER_OFFSET = 0;
    private const VALUE_DELIMITER = ';';
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
     * @throws Exception
     */
    public static function fromFile(string $path, string $mode = 'r', $context = null): self
    {
        $self = new self();
        $self->reader = Reader::createFromPath($path, $mode, $context);
        $self->defaultInit();

        return $self;
    }

    /**
     * @param string $content
     * @return static
     * @throws Exception
     */
    public static function fromString(string $content = ''): self
    {
        $self = new self();
        $self->reader = Reader::createFromString($content);
        $self->defaultInit();;

        return $self;
    }

    /**
     * @throws Exception
     */
    private function defaultInit(): void
    {
        $this->reader->setHeaderOffset(self::DEFAULT_HEADER_OFFSET);
        $this->reader->setDelimiter(self::VALUE_DELIMITER);
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
        $useKeys = true;
        if(isset($options['use_keys'])) {
            $useKeys = (bool) $options['use_keys'];
        }
        return iterator_to_array($this->reader->getRecords($options), $useKeys);
    }
}