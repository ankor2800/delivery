<?php
namespace Core\Order;

use \Core\Location\Location as Location;

class Order
{
    const COUNT = 5;
    const COMING = [
        'min' => 1,
        'max' => 30
    ];
    const COOKING = [
        'min' => 10,
        'max' => 30
    ];

    protected $order;

    /**
     * Order constructor.
     * @param array $order
     */
    public function __construct(
        array $order = []
    )
    {
        $this->order = $order;
    }

    /**
     * Get orders
     * @return array
     */
    public function getOrders()
    {
        if (!$this->order) {
            $this->generate();
        }

        $this->sortOrders();

        return $this->clusters();
    }

    /**
     * Generate orders
     * @return array
     */
    protected function generate()
    {
        for ($count = 0; $count < self::COUNT; $count++) {
            $coming = $this->generateComing(
                self::COMING
            ) + $this->order[$count-1]->coming;

            $cooking = $this->generateCooking(self::COOKING);

            $location = Location::generateLocation();

            $this->order[] = (object)[
                'coming' => $coming,
                'cooking' => $cooking,
                'location' => $location,
                'route' =>  Location::getRoute(
                    Location::generateLocation(0,0),
                    $location
                ),
                'finish' => $coming + $cooking,
            ];
        }

        return $this->order;
    }

    /**
     * Generate order coming time
     * @param array $coming range time
     * @return int
     */
    protected function generateComing($coming)
    {
        return rand($coming['min'], $coming['max']);
    }

    /**
     * Generate order cooking time
     * @param array $cooking range time
     * @return int
     */
    protected function generateCooking($cooking)
    {
        return rand($cooking['min'], $cooking['max']);
    }

    /**
     * Sort orders by end cooking time
     * @return array
     */
    protected function sortOrders()
    {
        usort($this->order, function($a,$b)
        {
            return $a->finish - $b->finish;
        });

        return $this->order;
    }

    /**
     * Grouping orders by location
     * @return array
     */
    protected function clusters()
    {
        $temp = $this->order;

        foreach ($this->order as $key => &$order) {
            unset($temp[$key]);

            $nearOrder = Location::getNearPoint(
                $order->location,
                $temp
            );

            if (count($nearOrder) > 0) {
                foreach (array_keys($nearOrder) as $key) {
                    unset($this->order[$key]);
                }

                $order->children = $nearOrder;
            }
        }

        return $this->order;
    }
}
