<?php
/**
 * CartItem.php
 *
 * Kosárban lévő tétel.
 *
 * @author Kádár Péter <kadar.peter@gmail.com>
 * @copyright 2019 Kádár Péter <kadar.peter@gmail.com>
 * @version 1.0
 */

namespace kp\cart;


use Exception;

class CartItem implements CartItemInterface
{
    public $productId;
    public $combinationId;
    public $sku;
    public $name;
    
    /** @var double a termék nettó eladási (adott esetben kedvezményes) egységára */
    protected $price;
    protected $quantity;
    protected $options  = [];
    protected $taxRate  = 0;
    
    protected $rowId;
    private   $uniqueId;
    
    
    /**
     * CartItem constructor.
     *
     * @param $productId
     * @param $name
     * @param $price
     * @param int $quantity
     * @param array $options
     *
     * @throws Exception
     */
    public function __construct($productId, $name, $price, $quantity = 1, $options = [])
    {
        $this->options       = $options;
        $this->productId     = $productId;
        $this->name          = $name;
        $this->quantity      = $quantity;
        $this->sku           = $this->getOption('sku', null);
        $this->combinationId = $this->getOption('combinationId', 0);
        $this->taxRate       = $this->getOption('taxRate', 0);
        $this->price         = $price;

        $this->setUniqueId();
    }
    
    
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity <= 0 ?: 1;
        return $this;
    }

    /**
     * Az adott tétel darabszámát adja vissza
     * @return int
     */
    public function getQuantity()
    {
        return (int) $this->quantity;
    }
    
    
    public function getPrice()
    {
        return $this->price;
    }
    
    
    /**
     * Adott termék bruttó egységára.
     * @return float|int
     */
    public function getPriceWithTax()
    {
        $price = $this->price;
        if ((bool) $this->taxRate) {
            $price = (($this->price * (100 + $this->taxRate)) / 100);
        }
        
        return $price;
    }
    
    
    /**
     * Az adott tétel darabszámai és alapára alapján a teljes ár (ár*darabszám)
     *
     * @param bool $taxIncluded true esetén áfás árat ad vissza, egyébként nettót
     *
     * @return int
     */
    public function total($taxIncluded = true)
    {
        $itemPrice = ($taxIncluded === false) ? $this->price : $this->getPriceWithTax();
        return ($itemPrice * $this->quantity);
    }
    
    /**
     * Az áfa értékét adja vissza a darabszám függvényében
     * @return int
     */
    public function getTaxTotal()
    {
        return ($this->total() - $this->total(false));
    }
    
    /**
     * Tételhez opció beállítása.
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }
    
    /**
     * A tételhez rögzített tulajdonság lekérdezése
     * @param string $name a lekérdezendő tulajdonság megnevezése
     * @param null|mixed $default
     *
     * @return null
     */
    public function getOption($name, $default = null)
    {
        $path = strpos($name, '.*') ? trim($name, '.*') : $name;
        $keys = explode('.', $path);
        $cursor = $this->options;
        foreach ($keys as $key)
        {
            if (!isset($cursor[$key]))
                return $default;
            else
                $cursor = $cursor[$key];
        }
        return $cursor;
    }
    
    /**
     * @throws Exception
     */
    private function setUniqueId()
    {
        if (!$this->productId && (false === $this->combinationId || is_null($this->productId) || !is_int($this->productId))) {
            throw new Exception('Kombináció azonosító (combination_id) és termék azonosító (product_id) megadása kötelező!');
        }
        
        $uid = self::hashUniqueId($this->productId, $this->combinationId);
        $this->uniqueId = $uid;
    }
    
    
    public function getUniqueId()
    {
        return $this->uniqueId;
    }
    
    public static function hashUniqueId($productId, $combinationId)
    {
        return md5($productId . '-' . $combinationId);
    }
}//end class
