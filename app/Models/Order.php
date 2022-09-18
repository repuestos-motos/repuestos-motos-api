<?php

namespace App\Models;


use Exception;
use Illuminate\Database\Eloquent\Collection;
use JsonSerializable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Order extends Model implements JsonSerializable
{
    protected $table = 'TPEDIDOS';
    protected $primaryKey = 'IDPEDIDO';
    public $timestamps = false;
    protected $appends = ['orderId'];

    private static $ORIGIN = 'WEB';
    private static $STATE = 1;

    private array $orderItems = [];

    /**
     * Returns JSON object representation
     */
    public function jsonSerialize()
    {
        $stdObject = (object)[
            "orderId" => $this->orderId(),
            "clientId" => $this->clientId(),
            "date" => $this->date(),
            "totalAmount" => $this->totalAmount(),
            "stateId" => $this->stateId(),
            "origin" => $this->origin(),
            "sellerId" => $this->sellerId(),
            "orderItems" => $this->orderItems
        ];
        return $stdObject;
    }

    // Getters and setters

    public function orderId()
    {
        if (isset($value)) {
            $this->{$this->primaryKey} = $value;
        }
        return $this->{$this->primaryKey};
    }

    public function clientId($value = null) {
        if (isset($value)) {
            $this->IDCLIENTE = $value;
        }
        return $this->IDCLIENTE;
    }

    public function date($value = null) {
        if (isset($value)) {
            $this->FECHA = $value;
        }
        return $this->FECHA;
    }

    public function totalAmount($value = null) {
        if (isset($value)) {
            $this->TOTAL = $value;
        }
        return $this->TOTAL;
    }

    public function stateId($value = null) {
        if (isset($value)) {
            $this->IDESTADO = $value;
        }
        return $this->IDESTADO;
    }

    public function origin($value = null) {
        if (isset($value)) {
            $this->ORIGEN = $value;
        }
        return $this->ORIGEN;
    }

    public function sellerId($value = null) {
        if (isset($value)) {
            $this->IDVENDEDOR = $value;
        }
        return $this->IDVENDEDOR;
    }

    /**
     * Creates a New Order, saves it in database and return the database object
     * @return Order Order object in case o success
     */
    public static function CreateOrder($order): Order {
        try {
            $newOrder = new Order();
            $newOrder->date(date('Y-m-d'));
            $newOrder->clientId($order->clientId);
            $newOrder->totalAmount(0);
            $newOrder->stateId(self::$STATE);
            $newOrder->origin(self::$ORIGIN);
            if (isset($order->sellerId)) {
                $newOrder->sellerId($order->sellerId);
            }
            $newOrder->save();
            $newOrder->refresh();
            return $newOrder;
        } catch (Exception $e) {
            Log::error('Order::CreateOrder ' . $e->getMessage());
            throw new HttpException(500, 'Se produjo un error al crear la Orden');
        }
    }

    /**
     * Add items to the OrderItems array and stores them in the data base 
     */
    public function AddItem($productId, $description, $quantity, $price) {
        try {
            $newItem = new OrderItem();
            $newItem->orderId($this->orderId());
            $newItem->productId($productId);
            $newItem->description($description);
            $newItem->quantity($quantity);
            $newItem->unitPrice($price);
            $newItem->totalAmount(round($quantity * $price, 2));
            $newItem->state(1);
            $newItem->save();
            $this->orderItems[] = $newItem;
            return $newItem;
        } catch (Exception $e) {
            Log::error('Order::AddItem ' . $e->getMessage());
            throw new HttpException(500, 'Se produjo un error agregando un producto a su orden');
        }
    }

    /**
     * Load the order items from the database
     */
    public function LoadItems() {
        $this->orderItems = OrderItem::where('IDPEDIDO', '=', $this->orderId())
            ->get()
            ->toArray();
    }

    /**
     * Return all the orders for a client
     * @param clientId Client id
     * @return Collection A collection with all the orders found
     */
    public static function GetOrders($clientId) {
        return Order::where('IDCLIENTE', '=', $clientId)
            ->orderBy('FECHA', 'desc')
            ->get();
    }
}
