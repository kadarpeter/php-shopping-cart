<?php
/**
 * CartInterface.php
 *
 * PHP alapú webshop kosár
 *
 * @author Kádár Péter <kadar.peter@gmail.com>
 * @copyright 2019 Kádár Péter <kadar.peter@gmail.com>
 * @version 1.0
 */

namespace kp\cart;


interface CartInterface
{
    public function getItems();
    
    public function getItem($id);
    
    public function addItem(CartItem $item);
    
    public function updateItemQty(CartItem $item, $qty);
    
    public function removeItem(CartItem $item);
    
    public function total();
    
    public function countItems();
    
    public function sumItems();
    
    public function isEmpty();
    
    public function clear();
}
