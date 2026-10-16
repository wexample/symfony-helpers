<?php

namespace Wexample\SymfonyHelpers\Class;

/**
 * Fork from ArrayToTextTable
 *
 * @author      Mathieu Viossat <mathieu@viossat.fr>
 * @copyright   Copyright (c) 2015 Mathieu Viossat
 * @license     http://opensource.org/licenses/MIT
 * @link        https://github.com/MathieuViossat/arraytotexttable
 */
class ArrayToTextTable
{
    final public const ALIGN_LEFT = STR_PAD_RIGHT;
    final public const ALIGN_CENTER = STR_PAD_BOTH;
    final public const ALIGN_RIGHT = STR_PAD_LEFT;

    protected const TOP_LEFT = '┌';
    protected const TOP_RIGHT = '┐';
    protected const BOTTOM_LEFT = '└';
    protected const BOTTOM_RIGHT = '┘';
    protected const VERTICAL = '│';
    protected const HORIZONTAL = '─';
    protected const CROSS = '┼';
    protected const VERTICAL_RIGHT = '├';
    protected const VERTICAL_LEFT = '┤';
    protected const HORIZONTAL_DOWN = '┬';
    protected const HORIZONTAL_UP = '┴';

    protected array $keys;
    protected array $widths;
    protected string $indentation;
    protected string $displayKeys;
    protected bool $upperKeys;
    protected int $keysAlignment;
    protected int $valuesAlignment;
    protected $formatter;

    public function __construct(protected array $data = [])
    {
        $this->setData($data)
            ->setIndentation('')
            ->setDisplayKeys('auto')
            ->setUpperKeys(true)
            ->setKeysAlignment(self::ALIGN_CENTER)
            ->setValuesAlignment(self::ALIGN_LEFT)
            ->setFormatter(null);
    }

    public function __toString()
    {
        return $this->getTable();
    }

    public function getTable($data = null): string
    {
        if (! is_null($data)) {
            $this->setData($data);
        }

        $data = $this->prepare();
        $i = $this->indentation;

        $displayKeys = $this->displayKeys;
        if ($displayKeys === 'auto') {
            $displayKeys = false;
            foreach ($this->keys as $key) {
                if (! is_int($key)) {
                    $displayKeys = true;

                    break;
                }
            }
        }

        $table = $i.$this->line(static::TOP_LEFT, static::HORIZONTAL, static::HORIZONTAL_DOWN, static::TOP_RIGHT).PHP_EOL;

        if ($displayKeys) {
            $keysRow = array_combine($this->keys, $this->keys);
            if ($this->upperKeys) {
                $keysRow = array_map('mb_strtoupper', $keysRow);
            }
            $table .= $i.implode(PHP_EOL, $this->row($keysRow, $this->keysAlignment)).PHP_EOL;

            $table .= $i.$this->line(static::VERTICAL_RIGHT, static::HORIZONTAL, static::CROSS, static::VERTICAL_LEFT).PHP_EOL;
        }

        foreach ($data as $row) {
            $table .= $i.implode(PHP_EOL, $this->row($row, $this->valuesAlignment)).PHP_EOL;
        }

        return $table . ($i.$this->line(static::BOTTOM_LEFT, static::HORIZONTAL, static::HORIZONTAL_UP, static::BOTTOM_RIGHT).PHP_EOL);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getIndentation(): string
    {
        return $this->indentation;
    }

    public function getDisplayKeys(): string
    {
        return $this->displayKeys;
    }

    public function getUpperKeys(): bool
    {
        return $this->upperKeys;
    }

    public function getKeysAlignment(): int
    {
        return $this->keysAlignment;
    }

    public function getValuesAlignment(): int
    {
        return $this->valuesAlignment;
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function setData(?array $data): static
    {
        if (! is_array($data)) {
            $data = [];
        }

        $arrayData = [];
        foreach ($data as $row) {
            if (is_array($row)) {
                $arrayData[] = $row;
            } elseif (is_object($row)) {
                $arrayData[] = get_object_vars($row);
            }
        }

        $this->data = $arrayData;

        return $this;
    }

    public function setIndentation(string $indentation): static
    {
        $this->indentation = $indentation;

        return $this;
    }

    public function setDisplayKeys(string $displayKeys): static
    {
        $this->displayKeys = $displayKeys;

        return $this;
    }

    public function setUpperKeys(bool $upperKeys): static
    {
        $this->upperKeys = $upperKeys;

        return $this;
    }

    public function setKeysAlignment(int $keysAlignment): static
    {
        $this->keysAlignment = $keysAlignment;

        return $this;
    }

    public function setValuesAlignment(int $valuesAlignment): static
    {
        $this->valuesAlignment = $valuesAlignment;

        return $this;
    }

    public function setFormatter(?callable $formatter): static
    {
        $this->formatter = $formatter;

        return $this;
    }

    protected function line(
        $left,
        $horizontal,
        $link,
        $right
    ): string {
        $line = $left;
        foreach ($this->keys as $key) {
            $line .= str_repeat($horizontal, $this->widths[$key] + 2).$link;
        }

        if (mb_strlen($line) > mb_strlen($left)) {
            $line = mb_substr($line, 0, -mb_strlen($horizontal));
        }

        return $line.$right;
    }

    protected function row(
        $row,
        $alignment
    ): array {
        $data = [];
        $height = 1;
        foreach ($this->keys as $key) {
            $data[$key] = isset($row[$key]) ? static::valueToLines($row[$key]) : [''];
            $height = max($height, count($data[$key]));
        }

        $rowLines = [];
        for ($i = 0; $i < $height; $i++) {
            $rowLine = [];
            foreach ($data as $key => $value) {
                $rowLine[$key] = $value[$i] ?? '';
            }
            $rowLines[] = $this->rowLine($rowLine, $alignment);
        }

        return $rowLines;
    }

    protected function rowLine(
        $row,
        $alignment
    ): false|string {
        $line = static::VERTICAL;

        foreach ($row as $key => $value) {
            $line .= ' '.static::mb_str_pad($value, $this->widths[$key], ' ', $alignment).' '.static::VERTICAL;
        }

        if (empty($row)) {
            $line .= static::VERTICAL;
        }

        return $line;
    }

    protected function prepare(): array
    {
        $this->keys = [];
        $this->widths = [];

        $data = $this->data;

        if ($this->formatter instanceof \Closure) {
            foreach ($data as &$row) {
                array_walk($row, $this->formatter, $this);
            }
            unset($row);
        }

        foreach ($data as $row) {
            $this->keys = array_merge($this->keys, array_keys($row));
        }
        $this->keys = array_unique($this->keys);

        foreach ($this->keys as $key) {
            $this->setWidth($key, $key);
        }

        foreach ($data as $row) {
            foreach ($row as $columnKey => $columnValue) {
                $this->setWidth($columnKey, $columnValue);
            }
        }

        return $data;
    }

    protected static function countCJK($string): false|int
    {
        return preg_match_all('/[\p{Han}\p{Katakana}\p{Hiragana}\p{Hangul}]/u', $string);
    }

    protected function setWidth(
        $key,
        $value
    ): void {
        if (! isset($this->widths[$key])) {
            $this->widths[$key] = 0;
        }

        foreach (static::valueToLines($value) as $line) {
            $width = mb_strlen($line) + self::countCJK($line);
            if ($width > $this->widths[$key]) {
                $this->widths[$key] = $width;
            }
        }
    }

    protected static function valueToLines($value): array
    {
        return explode("\n", $value);
    }

    protected static function mb_str_pad(
        $input,
        $pad_length,
        $pad_string = ' ',
        $pad_type = STR_PAD_RIGHT,
        $encoding = null
    ): string {
        $encoding = $encoding === null ? mb_internal_encoding() : $encoding;
        $pad_before = $pad_type === STR_PAD_BOTH || $pad_type === STR_PAD_LEFT;
        $pad_after = $pad_type === STR_PAD_BOTH || $pad_type === STR_PAD_RIGHT;
        $pad_length -= mb_strlen($input, $encoding) + self::countCJK($input);
        $target_length = $pad_before && $pad_after ? $pad_length / 2 : $pad_length;

        $repeat_times = ceil($target_length / mb_strlen($pad_string, $encoding));
        $repeated_string = str_repeat($pad_string, max(0, $repeat_times));
        $before = $pad_before ? mb_substr($repeated_string, 0, floor($target_length), $encoding) : '';
        $after = $pad_after ? mb_substr($repeated_string, 0, ceil($target_length), $encoding) : '';

        return $before.$input.$after;
    }
}
