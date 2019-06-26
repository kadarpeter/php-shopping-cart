<?php
/**
 * CartItemInterface.php
 *
 * Kosárban lévő tétel
 *
 * @author Kádár Péter <kadar.peter@gmail.com>
 * @copyright 2019 Kádár Péter <kadar.peter@gmail.com>
 * @version 1.0
 */

namespace kp\cart;


interface CartItemInterface
{
    public function setQuantity($quantity);
    
    public function getQuantity();
    
    public function getPrice();
    
    public function total();
    
    public function setOption($name, $value);
    
    public function getOption($name, $default = null);
    
    public function getUniqueId();
}
