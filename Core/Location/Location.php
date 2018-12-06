<?php
namespace Core\Location;

class Location
{
    const MIN = -1000;
    const MAX = 1000;
    const CLUSTER = 300;

    /**
     * Generate location object coordinates
     * @param int|null $first
     * @param int|null $second
     * @return object
     */
    public static function generateLocation($first = null, $second = null)
    {
        return (object) [
            'axisX' => ($first !== null) ? $first : rand(self::MIN, self::MAX),
            'axisY' => ($second !== null) ? $second : rand(self::MIN, self::MAX),
        ];
    }

    /**
     * Get the distance between points
     * @param object $firstPoint location first point
     * @param object $secondPoint location second point
     * @return float|int
     */
    public static function getRoute($firstPoint, $secondPoint)
    {
        return abs(self::getAxisX($firstPoint) - self::getAxisX($secondPoint))
            + abs(self::getAxisY($firstPoint) - self::getAxisY($secondPoint));
    }

    /**
     * Get nearby orders by location
     * @param object $location location
     * @param array $orders requested orders
     * @return mixed $arNear
     */
    public static function getNearPoint($location, $orders)
    {
        foreach ($orders as $key => $order) {
            $route = self::getRoute($location, $order->location);

            if ($route <= self::CLUSTER) {
                $arNear[$key] = $order;
            }
        }

        return $arNear;
    }

    /**
     * Get X point location
     * @param object $location location coordinates
     * @return int
     */
    protected static function getAxisX($location)
    {
        return $location->axisX;
    }

    /**
     * Get Y point location
     * @param object $location location coordinates
     * @return int
     */
    protected static function getAxisY($location)
    {
        return $location->axisY;
    }
}
