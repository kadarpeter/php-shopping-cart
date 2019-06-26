<?php
/**
 * BaseCart.php
 *
 * @author Kádár Péter <kadar.peter@gmail.com>
 * @copyright 2019 Kádár Péter <kadar.peter@gmail.com>
 * @version 1.0
 */

namespace kp\cart;


use Exception;

class BaseCart implements CartInterface
{
    /** @var array|CartItem[] */
    protected $items   = [];
    protected $options = [];
    
    
    public function __construct(Array $options = [])
    {
        $this->options = $options;
    }
    
    
    /**
     * Összes tétel lekérdezése a kosárból.
     * @return array|CartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
    
    
    /**
     * ID alapján tétel visszaadása
     *
     * @param $uid string a termék egyedi azonosítója productId és combinationId alapján
     *
     * @return CartItem|null
     */
    public function getItem($uid)
    {
        if (isset($this->items[$uid])) {
            return $this->items[$uid];
        } else {
            return null;
        }
    }
    
    
    /**
     * @param CartItem $item
     *
     * @return bool|int
     * @throws Exception
     */
    public function addItem(CartItem $item)
    {
        // Ha nincs a tételnek azonosítója, akkor nem futhat tovább a kód.
        $productId      = $item->productId;
        $combination_id = $item->combinationId;
        $uid            = $item->getUniqueId();
        
        if (!$productId || (false === $combination_id || null === $combination_id)) {
            throw new Exception('A kosárba kerülő tételeknek egyedi azonosítóval kell rendelkezniük.');
        }
        
        // Tétel hozzáadása vagy darabszámának növelése.
        if ($this->getItem($uid)) {
            $qty = ($this->getItem($uid)->getQuantity() + $item->getQuantity());
            return $this->updateItemQty($item, $qty);
        } else {
            $this->items[$uid] = $item;
            return $item->getQuantity();
        }
    }
    
    
    public function updateItemQty(CartItem $item, $qty)
    {
        $uid = $item->getUniqueId();
        
        if (0 === (int) $qty) {
            return $this->removeItem($item);
        } elseif (($qty > 0) && ($qty != $this->getItem($item->getUniqueId())->getQuantity())) {
            $this->getItem($uid)->setQuantity($qty);
        } else {
            $this->items[$uid] = $item;
        }
        
        return (int) $qty;
    }
    
    
    public function removeItem(CartItem $item)
    {
        $uid = $item->getUniqueId();
        
        // Tétel eltávolítása.
        if ($this->getItem($uid)) {
            unset($this->items[$uid]);
            return true;
        }
        return false;
    }
    
    
    /**
     * A kosárban lévő tételek összértéke
     *
     * @param bool|true $taxIncluded
     *
     * @return int
     */
    public function total($taxIncluded = true)
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total($taxIncluded);
        }
        
        return $total;
    }
    
    
    /**
     * Egyedi tételek darabszáma, azaz hány különböző termék van a kosárban.
     * @return int
     */
    public function countItems()
    {
        return count($this->items);
    }
    
    
    /**
     * Az összes rendelt mennyiség.
     * @return int
     */
    public function sumItems()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getQuantity();
        }
        
        return $total;
    }
    
    
    /**
     * Üres a kosár?
     * @return bool
     */
    public function isEmpty()
    {
        return (empty($this->items));
    }
    
    
    /**
     * Kosár tartalmának törlése
     * @return bool
     */
    public function clear()
    {
        $this->items = [];
        return true;
    }
    
    
    /**
     * Konfigurációs tömbböl érték kiolvasása.
     *
     * @param $path
     * @param null $default
     *
     * @return array|null
     */
    protected function getOption($path, $default = null)
    {
        $path   = strpos($path, '.*') ? trim($path, '.*') : $path;
        $keys   = explode('.', $path);
        $cursor = $this->options;
        foreach ($keys as $key) {
            if (!isset($cursor[$key])) {
                return $default;
            } else {
                $cursor = $cursor[$key];
            }
        }
        return $cursor;
    }
}