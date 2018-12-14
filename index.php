<?php
/**
 * Autoload classes
 */
spl_autoload_register(function ($class) {
    require_once str_replace('\\','/', $class).'.php';
});

$object = new \Core\Order\Order();

use \Core\Delivery\Delivery as Delivery;

$object->orderOutput();

$object->sortOrders();

while (count($object->getOrders()) > 0) {
    $calculate = $object->getClone();

    $slice = $object->setSlice(Delivery::LIMIT_COUNT)->getSlice();

    $result = $object->delivery(
        Delivery::checkWait(
            Delivery::getWaitTime(current($slice)),
            $slice
        )?:current($slice)
    );

    $result->deliveryOutput();

    $result->resetDelivery();
}
