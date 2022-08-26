<?php

namespace App\Http\Controllers;

use App\Http\ResponseModels\ResponseModel;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
