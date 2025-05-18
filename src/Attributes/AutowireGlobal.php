<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class AutowireGlobal implements AutowireInterface
{
    /**
     * @param 'GLOBALS'|'_SERVER'|'_GET'|'_POST'|'_FILES'|'_COOKIE'|'_SESSION'|'_REQUEST'|'_ENV'|string $global
     * @param string $key
     */
    public function __construct(
        private string $global,
        private string $key,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        // this is necessary as access of superglobals seems not to work via dynamic variables
        $global = (match ($this->global) {
            'GLOBALS' => $GLOBALS,
            '_SERVER' => $_SERVER,
            '_GET' => $_GET,
            '_POST' => $_POST,
            '_FILES' => $_FILES,
            '_COOKIE' => $_COOKIE,
            '_SESSION' => $_SESSION,
            '_REQUEST' => $_REQUEST,
            '_ENV' => $_ENV,
            default => ${$this->global},
        });

        if (!isset($global[$this->key])) {
            throw new AutowireException(sprintf(
                'Missing key "%s" in global $%s.',
                $this->key,
                $this->global,
            ));
        }

        return $global[$this->key];
    }
}
