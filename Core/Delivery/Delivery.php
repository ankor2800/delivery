<?php
namespace Core\Delivery;

class Delivery
{
    const LIMIT = 60;
    const SPEED = 60;
    const LIMIT_COUNT = 3;

    /**
     * Get max wait time for order
     * @param object $order
     * @return float|int wait time
     */
    public static function getWaitTime($order)
    {
        return $order->finish + self::LIMIT - ($order->cooking + $order->route / self::SPEED);
    }

    /**
     * Get orders with a valid wait
     * @param float $waitTime wait time
     * @param array $orders orders objects array
     * @return array
     */
    public static function checkWait($waitTime, $orders)
    {
        foreach ($orders as $key => &$order) {
            if ($order->finish < $waitTime) {
                if (count($order->children) > 0) {
                    foreach ($order->children as $childKey => $child) {
                        if ($child->finish > $waitTime) {
                            unset($order->children[$childKey]);
                        }
                    }
                }
            } else {
                unset($orders[$key]);
            }
        }

        return $orders;
    }

    /**
     * Get maximum ready time
     * @param array $orders orders objects array
     * @return integer
     */
    public static function getMaxTime($orders)
    {
        foreach ($orders as $order) {
            if ($order->children) {
                foreach ($order->children as $child) {
                    $arTime[] = $child->finish;
                }
            }

            $arTime[] = $order->finish;
        }

        return max($arTime);
    }

    /**
     * Calculate delivery time
     * @param int $route distance between orders
     * @return float|int
     */
    public static function getDeliveryTime($route)
    {
        return $route / self::SPEED;
    }

    /**
     * Validate delivery order
     * @param int $time maximum ready time
     * @param object $location location start route
     * @param object $order order object
     * @return bool
     */
    public static function isValidDelivery($time, $location, $order)
    {
        $deliveryTime = Delivery::getDeliveryTime(
            \Core\Location\Location::getRoute(
                $location,
                $order->location
            )
        );

        return ($time - $order->coming + $deliveryTime < self::LIMIT);
    }
}
