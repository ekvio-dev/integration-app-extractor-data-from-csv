<?php
declare(strict_types=1);

namespace Ekvio\Integration\Extractor;

use Ekvio\Integration\Contracts\Extractor;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use RuntimeException;

/**
 * Class UsersFromCsv
 * @package Ekvio\Integration\Extractor
 */
class DataFromCsv implements Extractor
{
    private const DEFAULT_HEADER_OFFSET = 0;
    private const DEFAULT_DELIMITER = ';';
    private const CREATE_FROM_PATH = 'path';
    private const CREATE_FROM_STRING = 'string';
    private const USE_KEYS = false;

    /**
     * @var string
     */
    private $delimiter = self::DEFAULT_DELIMITER;
    /**
     * @var int
     */
    private $headerOffset = self::DEFAULT_HEADER_OFFSET;
    /**
     * @var bool
     */
    private $useKeys = self::USE_KEYS;
    /**
     * @var Statement
     */
    private $statement;

    /**
     * @var array
     */
    private $options = [
        'from' => self::CREATE_FROM_PATH,
        'content' => '',
        'path' => null,
        'mode' => 'r',
        'context' => null,
    ];

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
        $self->options['from'] = self::CREATE_FROM_PATH;
        $self->options['path'] = $path;
        $self->options['mode'] = $mode;
        $self->options['context'] = $context;

        return $self;
    }

    /**
     * @param string $content
     * @return static
     */
    public static function fromString(string $content = ''): self
    {
        $self = new self();
        $self->options['from'] = self::CREATE_FROM_STRING;
        $self->options['content'] = $content;

        return $self;
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function delimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @param int|null $offset
     * @return $this
     */
    public function headerOffset(?int $offset): self
    {
        $this->headerOffset = $offset;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function useKeys(bool $flag): self
    {
        $this->useKeys = $flag;
        return $this;
    }

    /**
     * @param Statement $statement
     * @return $this
     */
    public function statement(Statement $statement): self
    {
        $this->statement = $statement;
        return $this;
    }

    /**
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function extract(array $options = []): array
    {
        $reader = $this->buildReader();
        $header = $options['header'] ?? [];

        if($this->statement) {
            $processed = $this->statement->process($reader);
            return iterator_to_array($processed->getRecords($header), $this->useKeys);
        }

        return iterator_to_array($reader->getRecords($header), $this->useKeys);
    }

    /**
     * @return Reader
     * @throws Exception
     */
    private function buildReader(): Reader
    {
        switch ($this->options['from']) {
            case self::CREATE_FROM_PATH:
                $reader = Reader::createFromPath($this->options['path'], $this->options['mode'], $this->options['context']);
                break;
            case self::CREATE_FROM_STRING:
                $reader = Reader::createFromString($this->options['content']);
                break;
            default:
                throw new RuntimeException(sprintf('Unknown %s for Reader instance',$this->options['from']));
        }

        return $reader
            ->setDelimiter($this->delimiter)
            ->setHeaderOffset($this->headerOffset);
    }
}