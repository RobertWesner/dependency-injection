<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class SomeRandomAttribute {}
