<?php

declare(strict_types=1);

namespace Salette\Http;

use SimpleXMLElement;

class XmlReader
{
    /**
     * The parsed XML element
     */
    protected SimpleXMLElement $xml;

    /**
     * Create a new XML reader instance
     */
    public function __construct(string $xmlContent)
    {
        $this->xml = simplexml_load_string($xmlContent);

        if ($this->xml === false) {
            throw new \InvalidArgumentException('Invalid XML content provided');
        }
    }

    /**
     * Get a value using dot notation
     */
    public function value(string $path)
    {
        $parts = explode('.', $path);
        $current = $this->xml;

        foreach ($parts as $part) {
            if (is_numeric($part)) {
                // Handle array-like access
                $index = (int) $part;
                if (isset($current[$index])) {
                    $current = $current[$index];
                } else {
                    return null;
                }
            } else {
                // Handle element access
                if (isset($current->$part)) {
                    $current = $current->$part;
                } else {
                    return null;
                }
            }
        }

        return new XmlValue($current);
    }

    /**
     * Get all values for a path
     */
    public function values(string $path): array
    {
        $parts = explode('.', $path);
        $current = $this->xml;

        foreach ($parts as $part) {
            if (is_numeric($part)) {
                $index = (int) $part;
                if (isset($current[$index])) {
                    $current = $current[$index];
                } else {
                    return [];
                }
            } else {
                if (isset($current->$part)) {
                    $current = $current->$part;
                } else {
                    return [];
                }
            }
        }

        // If we have multiple elements, return them as an array
        if ($current->count() > 0) {
            $result = [];
            foreach ($current as $element) {
                $result[] = (string) $element;
            }

            return $result;
        }

        return [(string) $current];
    }

    /**
     * Get the sole value (first element if multiple exist)
     */
    public function sole(?string $path = null)
    {
        if ($path === null) {
            return (string) $this->xml;
        }

        $values = $this->values($path);

        return $values[0] ?? null;
    }

    /**
     * Get the raw SimpleXMLElement
     */
    public function getXml(): SimpleXMLElement
    {
        return $this->xml;
    }
}
