<?php

use Stein\Framework\Attribute\Controller;
use Stein\Framework\Attribute\HttpGet;
use Stein\Framework\Attribute\Route;
use Stein\Framework\Attribute\RouteName;

#[Controller]
#[Route('/sample')]
class SampleController
{
    #[HttpGet()]
    #[RouteName('sample.route')]
    public function sampleMethod()
    {
        // Sample method
    }
}