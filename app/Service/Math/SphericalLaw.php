<?php

declare(strict_types=1);

namespace App\Service\Math;

class SphericalLaw
{
    private float $mean_earth_radius = 6371.009;

    public function __construct(
        private readonly float $latitude_point_a,
        private readonly float $longitude_point_a,
        private readonly float $latitude_point_b,
        private readonly float $longitude_point_b,
    )
    {

    }

    private function calculateCentralAngle(): float
    {
        $angle  =
            sin(deg2rad($this->latitude_point_a)) *
            sin(deg2rad($this->latitude_point_b)) +
            cos(deg2rad($this->latitude_point_a)) *
            cos(deg2rad($this->latitude_point_b)) *
            cos(deg2rad($this->longitude_point_b - $this->longitude_point_a));

        return acos($angle);
    }

    public function distance(): float
    {
        return round($this->calculateCentralAngle() * $this->mean_earth_radius, 2);
    }
}