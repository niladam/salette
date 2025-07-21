<?php

declare(strict_types=1);

namespace Salette\Http;

use SimpleXMLElement;

class XmlValue
{
    /**
     * The XML element
     */
    protected SimpleXMLElement $element;

    /**
     * Create a new XML value instance
     */
    public function __construct(SimpleXMLElement $element)
    {
        $this->element = $element;
    }

    /**
     * Get the sole value (first element if multiple exist)
     */
    public function sole()
    {
        return (string) $this->element;
    }

    /**
     * Get the string value
     */
    public function __toString(): string
    {
        return (string) $this->element;
    }

    /**
     * Get the raw SimpleXMLElement
     */
    public function getElement(): SimpleXMLElement
    {
        return $this->element;
    }
}
