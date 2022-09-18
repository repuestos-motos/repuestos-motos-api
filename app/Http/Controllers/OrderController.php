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
use App\Models\Product;
use App\Models\Seller;

class OrderController extends Controller
{
    /**
     * Creates an order and stores it in the database
     */
    public function CreateOrder(Request $request) {
        try {
            $order = (object)$request->input('order');
            $order = json_decode(json_encode($order));
            $response = (object)[];
            
            DB::beginTransaction();
            if (!Client::clientExist($order->clientId)) {
                throw new HttpException(404, 'Cliente no válido');
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
                        $product->price()
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
}
