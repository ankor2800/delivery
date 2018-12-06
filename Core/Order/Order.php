<?php
namespace Core\Order;

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
        if (!$this->order)
        {
            $this->generate();
        }

        return $this->sortOrders();
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

            $this->order[] = (object)[
                'coming' => $coming,
                'cooking' => $cooking,
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
}
