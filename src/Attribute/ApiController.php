<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Indicate that the class is used to serve HTTP API responses.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ApiController extends Controller {}
