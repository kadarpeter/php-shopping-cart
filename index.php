<?php
require __DIR__ . '/vendor/autoload.php';

use kp\cart\BaseCart;
use kp\cart\CartItem;

$cart = new BaseCart();


$item = new CartItem(1, 'Első termék', 100, 1, ['taxRate' => 27]);
$item->setOption('isNew', true);
$item->setQuantity(2);
//dump($item->getQuantity());

$cart->addItem($item);
dump($cart);

$item = new CartItem(1, 'Első termék', 100, 1, ['taxRate' => 27]);
$item->setQuantity(-1);
$cart->addItem($item);

$item = new CartItem(2, 'Második termék', 150, 2, ['taxRate' => 27]);
$cart->addItem($item);

dump($cart->countItems());
dump($cart->sumItems());
dump($cart->total());
dump($cart->total(false));
dump($cart->getItems());