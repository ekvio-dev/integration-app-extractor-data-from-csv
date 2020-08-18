<?php
declare(strict_types=1);

namespace Ekvio\Integration\Extractor;

use Ekvio\Integration\Contracts\Extractor;
use League\Csv\Exception;
use League\Csv\Reader;
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
    private const USE_KEYS = true;

    /**
     * @var array
     */
    private $options = [
        'from' => self::CREATE_FROM_PATH,
        'content' => '',
        'path' => null,
        'mode' => 'r',
        'context' => null
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
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function extract(array $options = []): array
    {
        $options = array_replace_recursive($this->options, $options);
        $reader = $this->buildReader($options);

        return iterator_to_array($reader->getRecords($options['header'] ?? []), $options['user_keys'] ?? self::USE_KEYS);
    }

    /**
     * @param array $options
     * @return Reader
     * @throws Exception
     */
    private function buildReader(array $options): Reader
    {
        switch ($options['from']) {
            case self::CREATE_FROM_PATH:
                $reader = Reader::createFromPath($options['path'], $options['mode'], $options['context']);
                break;
            case self::CREATE_FROM_STRING:
                $reader = Reader::createFromString($options['content']);
                break;
            default:
                throw new RuntimeException(sprintf('Unknown %s for Reader instance', $options['from']));
        }

        $reader->setDelimiter($options['delimiter'] ?? self::DEFAULT_DELIMITER);
        $reader->setHeaderOffset($options['offset'] ?? self::DEFAULT_HEADER_OFFSET);

        return $reader;
    }
}