<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;

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

    public function resolve(): mixed
    {
        // this is necessary as access of superglobals seems not to work via dynamic variables
        return (match ($this->global) {
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
        })[$this->key] ?? null;
    }
}
