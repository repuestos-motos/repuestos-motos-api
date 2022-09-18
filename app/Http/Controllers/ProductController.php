<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\ResponseModels\ResponseModel;
use App\Models\Client;
use App\Models\Product;

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
        } catch (Throwable $e) {
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
            $user = Client::find($userId);
    
            if (!$user) {
                return ResponseModel::GetErrorResponse(null, 'Usuario no encontrado', 404);
            }
    
            // Get products
            return ResponseModel::GetSuccessfullResponse(
                Product::GetProductsListWithPrices($user->IDLISTA)
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error al obtener el listado',
                500
            ); 
        }
    }

    /**
     * Return a product by id
     */
    public function GetProduct(Request $request, $id) {
        try {    
            // Get product
            $product = Product::getProduct($id);
    
            if (!$product) {
                return ResponseModel::GetErrorResponse(null, 'Producto no encontrado', 404);
            }
    
            // Get products
            return ResponseModel::GetSuccessfullResponse($product);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error al obtener el producto',
                500
            ); 
        }
    }
}
