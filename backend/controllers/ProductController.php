<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBearerAuth;
use app\services\ProductService;


class ProductController extends ApiController
{
    public $enableCsrfValidation = false;
    
    private $productService;
    
    public function init()
    {
        parent::init();
        $this->productService = new ProductService();
    }
    
 public function behaviors()
{
    $behaviors = parent::behaviors();
    
    $behaviors['authenticator'] = [
        'class' => HttpBearerAuth::class,
        'except' => ['index', 'view', 'brand', 'options'],
    ];
    
    return $behaviors;
}

      public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', 'https://apltech-front-eight.vercel.app');
        Yii::$app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        Yii::$app->response->headers->set('Access-Control-Allow-Credentials', 'true');
        return [];
    }
    
    /**
     * GET /api/products
     * Возвращает все товары из БД и JSON файла
     */
    public function actionIndex(): array
    {
        // 1. Получаем товары из БД
        $dbProducts = \app\models\Product::find()->all();
        
        $result = [];
        foreach ($dbProducts as $product) {
            $result[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category_name' => $product->category_name,
                'brand_name' => $product->brand_name,
                'price' => $this->getDisplayPrice($product),
                'rrp_price' => $product->rrp_price,
                'status' => $product->status,
                'source' => 'database',
            ];
        }
        
        // 2. Получаем товары из JSON файла
        $jsonProducts = $this->getAllProductsFromJson();
        
        foreach ($jsonProducts as $product) {
            // Проверяем, нет ли такого ID в БД (чтобы избежать дублей)
            $exists = false;
            foreach ($result as $existing) {
                if ($existing['id'] == $product['id']) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $result[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'category_name' => $product['category_name'] ?? '',
                    'brand_name' => $product['brand_name'] ?? '',
                    'price' => $this->getDisplayPriceFromJson($product),
                    'rrp_price' => $product['rrp_price'] ?? 0,
                    'status' => $product['status'] ?? 1,
                    'source' => 'json',
                ];
            }
        }
        
        // Сортируем по ID
        usort($result, function($a, $b) {
            return $a['id'] - $b['id'];
        });
        
        return $result;
    }
    
    /**
     * GET /api/product/{id}
     */
    public function actionView($id): array
    {
        // 1. Сначала ищем в БД
        $product = \app\models\Product::findOne($id);
        
        if ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_name' => $product->category_name,
                'brand_name' => $product->brand_name,
                'price' => $this->getDisplayPrice($product),
                'rrp_price' => $product->rrp_price,
                'status' => $product->status,
                'source' => 'database',
            ];
        }
        
        // 2. Если не найден в БД, ищем в JSON файле
        $jsonProduct = $this->findProductInJson($id);
        
        if ($jsonProduct) {
            return [
                'id' => $jsonProduct['id'],
                'name' => $jsonProduct['name'],
                'category_name' => $jsonProduct['category_name'] ?? '',
                'brand_name' => $jsonProduct['brand_name'] ?? '',
                'price' => $this->getDisplayPriceFromJson($jsonProduct),
                'rrp_price' => $jsonProduct['rrp_price'] ?? 0,
                'status' => $jsonProduct['status'] ?? 1,
                'source' => 'json',
            ];
        }
        
        throw new NotFoundHttpException("Товар с ID {$id} не найден");
    }
    
    /**
     * GET /api/product/brand/{name}
     */
    public function actionBrand($name): array
    {
        try {
            return $this->productService->getBrandMinMaxPrice($name);
        } catch (NotFoundHttpException $e) {
            Yii::$app->response->statusCode = 404;
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Server error: ' . $e->getMessage()];
        }
    }
    
    /**
     * POST /api/product/create
     */
    public function actionCreate(): array
    {
        $request = Yii::$app->request->getBodyParams();
        $product = new \app\models\Product();
        
        $product->load($request, '');
        
        if (!$product->validate()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $product->getErrors()];
        }
        
        if ($product->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'id' => $product->id,
                'name' => $product->name,
                'brand_name' => $product->brand_name,
                'price' => $product->price,
                'message' => 'Product created successfully'
            ];
        }
        
        Yii::$app->response->statusCode = 500;
        return ['error' => 'Failed to create product'];
    }
    
    /**
     * PATCH /api/product/update/{id}
     */
    public function actionUpdate($id): array
    {
        $product = \app\models\Product::findOne($id);
        
        if (!$product) {
            throw new NotFoundHttpException("Товар с ID {$id} не найден");
        }
        
        $request = Yii::$app->request->getBodyParams();
        
        foreach ($request as $field => $value) {
            if ($product->hasAttribute($field)) {
                $product->$field = $value;
            }
        }
        
        if (!$product->validate()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $product->getErrors()];
        }
        
        if ($product->save()) {
            return [
                'id' => $product->id,
                'updated_fields' => array_keys($request),
                'message' => 'Product updated successfully'
            ];
        }
        
        Yii::$app->response->statusCode = 500;
        return ['error' => 'Failed to update product'];
    }
    
    /**
     * Отображение цены для товара из БД
     */
    private function getDisplayPrice($product)
    {
        if ((int)$product->status === 2) {
            return "цена по запросу";
        }
        
        $brand = strtolower($product->brand_name);
        if ($brand === 'dell' || $brand === 'lenovo') {
            return (int)$product->rrp_price;
        }
        
        return (int)$product->price;
    }
    
    /**
     * Отображение цены для товара из JSON
     */
    private function getDisplayPriceFromJson(array $product)
    {
        if ((int)($product['status'] ?? 0) === 2) {
            return "цена по запросу";
        }
        
        $brand = strtolower($product['brand_name'] ?? '');
        if ($brand === 'dell' || $brand === 'lenovo') {
            return (int)($product['rrp_price'] ?? 0);
        }
        
        return (int)($product['price'] ?? 0);
    }
    
    /**
     * Поиск товара в JSON файле по ID
     */
    private function findProductInJson($id): ?array
    {
        $products = $this->getAllProductsFromJson();
        
        foreach ($products as $product) {
            if (isset($product['id']) && (int)$product['id'] === (int)$id) {
                return $product;
            }
        }
        
        return null;
    }
    
    /**
     * Получить все товары из JSON файла
     */
    private function getAllProductsFromJson(): array
    {
        $filePath = Yii::getAlias('@app/runtime/product.json');
        
        if (!file_exists($filePath)) {
            return [];
        }
        
        $jsonData = file_get_contents($filePath);
        if ($jsonData === false) {
            return [];
        }
        
        $products = json_decode($jsonData, true);
        if (!is_array($products)) {
            return [];
        }
        
        return $products;
    }

        /**
     * DELETE /api/product/delete/{id}
     * Удаляет товар по ID
     */
    public function actionDelete($id): array
    {
        // 1. Сначала ищем в БД
        $product = \app\models\Product::findOne($id);
        
        if ($product) {
            if ($product->delete()) {
                Yii::$app->response->statusCode = 200;
                return [
                    'id' => $id,
                    'message' => 'Product deleted successfully from database'
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return ['error' => 'Failed to delete product from database'];
            }
        }
        
        // 2. Если не найден в БД, ищем в JSON файле
        $jsonProduct = $this->findProductInJson($id);
        
        if ($jsonProduct) {
            // Удаляем из JSON файла
            $deleted = $this->deleteProductFromJson($id);
            
            if ($deleted) {
                Yii::$app->response->statusCode = 200;
                return [
                    'id' => $id,
                    'message' => 'Product deleted successfully from JSON',
                    'source' => 'json'
                ];
            } else {
                Yii::$app->response->statusCode = 500;
                return ['error' => 'Failed to delete product from JSON'];
            }
        }
        
        throw new NotFoundHttpException("Товар с ID {$id} не найден");
    }

    /**
     * Удалить товар из JSON файла
     */
    private function deleteProductFromJson($id): bool
    {
        $filePath = Yii::getAlias('@app/runtime/product.json');
        
        if (!file_exists($filePath)) {
            Yii::warning("JSON file not found: {$filePath}");
            return false;
        }
        
        $jsonData = file_get_contents($filePath);
        if ($jsonData === false) {
            Yii::warning("Failed to read JSON file: {$filePath}");
            return false;
        }
        
        $products = json_decode($jsonData, true);
        if (!is_array($products)) {
            Yii::warning("Invalid JSON format in: {$filePath}");
            return false;
        }
        
        $originalCount = count($products);
        
        // Фильтруем - удаляем товар с нужным ID
        $newProducts = array_filter($products, function($product) use ($id) {
            return ($product['id'] ?? null) != $id;
        });
        
        // Если количество не изменилось - товар не найден
        if (count($newProducts) === $originalCount) {
            Yii::warning("Product with ID {$id} not found in JSON");
            return false;
        }
        
        // Переиндексируем массив
        $newProducts = array_values($newProducts);
        
        // Сохраняем обратно в файл
        $newJsonData = json_encode($newProducts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $result = file_put_contents($filePath, $newJsonData);
        
        if ($result === false) {
            Yii::error("Failed to write JSON file: {$filePath}");
            return false;
        }
        
        Yii::info("Product with ID {$id} deleted from JSON file");
        return true;
    }
}


