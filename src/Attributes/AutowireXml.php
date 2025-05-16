<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use Exception;
use JsonException;
use RobertWesner\DependencyInjection\AbstractBuffered;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use SimpleXMLElement;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireXml extends AbstractBuffered implements FileBasedAutowireInterface
{
    public function __construct(
        private readonly string $filename,
        private readonly string $xPath,
    ) {}

    public function resolve(bool $buffered = false): array
    {
        if (!file_exists($this->filename) && !$this->getBuffer()->has($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing XML file "%s".',
                $this->filename,
            ));
        }

        if ($buffered && $this->getBuffer()->has($this->filename)) {
            $xml = $this->getBuffer()->get($this->filename);
        } else {
            try {
                $xml = new SimpleXMLElement(file_get_contents($this->filename));
                if ($buffered) {
                    $this->getBuffer()->set($this->filename, $xml);
                }
            } catch (Exception $exception) {
                throw new AutowireException(sprintf(
                    'Invalid XML file "%s".',
                    $this->filename,
                ), previous: $exception);
            }
        }

        $result = $xml->xpath($this->xPath);
        if ($result === false) {
            throw new AutowireException(sprintf(
                'Could not find XPath "%s" in XML file "%s".',
                $this->xPath,
                $this->filename,
            ));
        }

        return $result;
    }
}
