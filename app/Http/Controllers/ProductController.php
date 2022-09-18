<?php

namespace App\Http\Controllers;

use App\Http\ResponseModels\ResponseModel;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ProductController extends Controller
{

    public function GetProductImage(Request $request) {

        try {
            $productId = $request->input('productId');
            $product = Product::find($productId);
    
            if (!$product) {
                return ResponseModel::GetErrorResponse(null, 'Producto no encontrado', 404);
            }
    
            if (!$product->FOTO || $product->FOTO == "NO") {
                return ResponseModel::GetErrorResponse(null, 'Imagen no encontrada', 404);
            }
    
            return response('data:image/*;base64,' . $product->FOTO, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(null, 'Error al generar imagen', 500);
        }
    }

    /**
     * Return the product List with prices
     */
    public function GetProducts(Request $request) {
        try {
            $userId = $request->input('userId');
    
            // Get user
            $user = User::find($userId);
    
            if (!$user) {
                return ResponseModel::GetErrorResponse('Usuario no encontrado', null, 404);
            }
    
            // Get products
            return ResponseModel::GetSuccessfullResponse(
                Product::GetProductsListWithPrices($user->IDLISTA)
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                'Se produjo un error al obtener el listado',
                null,
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
            if (Client::clientExist($order->clientId)) {
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
                $e->getMessage(),
                null,
                $e->getStatusCode()
            );
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                'Se produjo un error al crear su orden',
                null,
                500
            ); 
        }
    }
}
