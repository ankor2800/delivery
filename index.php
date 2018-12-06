<?php
/**
 * Autoload classes
 */
spl_autoload_register(function ($class) {
    require_once str_replace('\\','/', $class).'.php';
});

$object = new \Core\Order\Order();
$order = $object->getOrders();
