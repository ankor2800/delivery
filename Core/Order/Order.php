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
    protected $slice;
    protected $clone;

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
            $this->generate()->sortOrders();
        }

        return $this->order;
    }

    /**
     * Get clone
     * @return array
     */
    public function getClone()
    {
        if (!$this->clone) {
            $this->setClone()->clusters();
        }

        return $this->clone;
    }

    /**
     * Get slice array orders
     * @return array
     */
    public function getSlice()
    {
        if ($this->slice) {
            return $this->slice;
        }
    }

    /**
     * Set slice, array slice from clone array
     * @param int $limit slice limit
     * @return $this
     */
    public function setSlice($limit)
    {
        $this->slice = array_slice($this->clone, 0 , $limit, true);

        return $this;
    }

    /**
     * Generate orders
     * @return $this
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

        return $this;
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
     * @return $this
     */
    protected function sortOrders()
    {
        usort($this->order, function($a,$b)
        {
            return $a->finish - $b->finish;
        });

        return $this;
    }

    /**
     * Set clone, clone order objects
     * @return $this
     */
    protected function setClone()
    {
        foreach ($this->order as $order) {
            $clone[] = clone $order;
        }

        $this->clone = $clone;

        return $this;
    }

    /**
     * Grouping orders by location
     * @return $this
     */
    protected function clusters()
    {
        $temp = $this->clone;

        foreach ($this->clone as $key => &$order) {
            unset($temp[$key]);

            $nearOrder = Location::getNearPoint(
                $order->location,
                $temp
            );

            if (count($nearOrder) > 0) {
                foreach ($nearOrder as $key => $near) {
                    $order->children[$key] = $near;
                    unset($this->clone[$key]);
                }
            }
        }

        return $this;
    }
}
