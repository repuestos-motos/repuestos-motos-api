<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\ResponseModels\ResponseModel;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function GetProductImage(Request $request) {

        try {
            $productId = $request->input('productId');
            $product = Product::find($productId);
            $image = '';
            if (!$product) {
                return ResponseModel::GetErrorResponse(null, 'Producto no encontrado', 404);
            }
    
            if (!$product->FOTO || $product->FOTO == "NO") {
                $image = Storage::disk('public')->get('sample-product.PNG');
            } else {
                $image = base64_decode($product->FOTO);                
            }

            return response(
                $image,
                200,
                [
                    'Content-Type' => 'image/*',
                    'Cache-Control' => 'max-age=3600'
                ]
            );
    
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
                Product::GetProductsListWithPrices($user->IDLISTA),
                [ 'Cache-Control' => 'max-age=900' ]
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

    public function GetClientList(Request $request) {
        try {
            // Get client List
            return ResponseModel::GetSuccessfullResponse(
                Client::all()
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error al obtener el listado de clientes',
                500
            ); 
        }
    }

    /**
     * Return a list of categories
     */
    public function GetProductsCategories(Request $request) {
        try {
            // Get all categories
            return ResponseModel::GetSuccessfullResponse(
                Category::GetCategories(),
                [ 'Cache-Control' => 'max-age=900' ]
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return ResponseModel::GetErrorResponse(
                null,
                'Se produjo un error al obtener las categorías de productos',
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
