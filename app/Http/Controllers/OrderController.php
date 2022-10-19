<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\ResponseModels\ResponseModel;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\Seller;

class OrderController extends Controller
{

    public function GetAllStatusCodes(Request $request) {
        try {
            return ResponseModel::GetSuccessfullResponse(
                OrderStatus::all()
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error obtener los estados',
                500
            );
        }
    }

    /**
     * Creates an order and stores it in the database
     */
    public function CreateOrder(Request $request) {
        try {
            $order = (object)$request->input('order');
            $order = json_decode(json_encode($order));
            $response = (object)[];
            
            DB::beginTransaction();
            // Get user
            $user = Client::find($order->clientId);
            if (!$user) {
                throw new HttpException(404, 'Cliente no válido');
            }
            // Get Price List
            $priceList = PriceList::find($user->IDLISTA);
            $discountPercentage = 0;
            if ($priceList) {
                $discountPercentage = $priceList->percentage();
            }
            if (isset($order->sellerId) && !Seller::sellerExist($order->sellerId)) {
                throw new HttpException(404, 'Vendedor no válido');
            }
            $newOrder = Order::CreateOrder($order);
            $response->order = $newOrder;
            if (isset($order->orderItems)) {
                foreach ($order->orderItems as $orderItem) {
                    // Get the product
                    $product = Product::getProduct($orderItem->productId);
                    $product->calculateSalesPrice($discountPercentage);
                    if ($product === null) {
                        throw new HttpException(404, 'Su pedido contiene productos inexistentes');
                    }
                    // Reduces the stock for the product
                    $product->reduceStock($orderItem->quantity);
                    // Adds the product to the order
                    $newOrder->addItem(
                        $product->productId(),
                        $product->title(),
                        $orderItem->quantity,
                        $product->price(),
                        $product->salesPrice()
                    );
                    // Modifies total amount of order
                    $newOrder->totalAmount($newOrder->totalAmount() + round($orderItem->quantity * $product->price(), 2));
                }
            }
            $newOrder->save();
            DB::commit();
            
            return ResponseModel::GetSuccessfullResponse($response);
        } catch (HttpException $e) {
            DB::rollBack();
            return ResponseModel::GetErrorResponse(
                null,
                $e->getMessage(),
                $e->getStatusCode()
            );
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error al crear su orden',
                500
            ); 
        }
    }

    public function OrdersList(Request $request, $clientId) {
        try {
            if (!Client::clientExist($clientId)) {
                throw new HttpException(400, 'Id de cliente no encontrado');
            }
            return ResponseModel::GetSuccessfullResponse(
                Order::GetOrders($clientId)
            );
        } catch (HttpException $e) {
            return ResponseModel::GetErrorResponse(
                null,
                $e->getMessage(),
                $e->getStatusCode()
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error obtener la lista de pedidos',
                500
            );
        }
    }

    public function OrderDetail(Request $request, $orderId) {
        try {
            if (!is_numeric($orderId) || $orderId < 0) {
                throw new HttpException(400, 'Id de pedido incorrecto');
            }
            $order = Order::find($orderId);
            if (!$order) {
                throw new HttpException(404, 'Pedido no encontrado');
            }
            $order->LoadItems();
            return ResponseModel::GetSuccessfullResponse($order);
        } catch (HttpException $e) {
            return ResponseModel::GetErrorResponse(
                null,
                $e->getMessage(),
                $e->getStatusCode()
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error obtener el pedido',
                500
            );
        }
    }
}
