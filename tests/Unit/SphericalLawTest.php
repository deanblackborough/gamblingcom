<?php

namespace Tests\Unit;

use App\Service\Math\SphericalLaw;
use PHPUnit\Framework\TestCase;

class SphericalLawTest extends TestCase
{
    /**
     * The spherical code uses types, so we skip testing it throws an exception
     * when incorrect data is passed in, that is a job for the type checker.
     * We simply test the maths works as expected
     */
    public function testZeroDistanceWhenPointAAndPointBMatch(): void
    {
        $math = new SphericalLaw(
            53.3340285,
            -6.2535495,
            53.3340285,
            -6.2535495
        );
        $this->assertSame($math->distance(), 0.0);
    }

    public function testKnownDistance(): void
    {
        $math = new SphericalLaw(
            53.339428,
            -6.257664,
            51.5007, // Big Ben
            0.1246 // Big Ben
        );
        $this->assertSame($math->distance(), 478.44);
    }
}
