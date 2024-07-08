<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Indicate that the class is used to serve HTTP responses.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Controller {}
