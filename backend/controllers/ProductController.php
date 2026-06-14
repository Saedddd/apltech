<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;

use app\services\ProductService;



class ProductController extends Controller
{
    private $productService;
    
    /**
     * Конструктор с внедрением зависимости
     * Временное решение - создаём сервис внутри
     */
    public function init()
    {
        parent::init();
        // Создаём экземпляр сервиса напрямую
        $this->productService = new ProductService();
    }
    
    /**
     * Настройка формата ответа
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }
    
    /**
     * GET /api/products
     * Возвращает все товары из базы
     */
    public function actionIndex(): array
    {
        $products = \app\models\Product::find()->all();
        
        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category_name' => $product->category_name,
                'brand_name' => $product->brand_name,
                'price' => $this->getDisplayPrice($product),
                'rrp_price' => $product->rrp_price,
                'status' => $product->status,
            ];
        }
        
        return $result;
    }
    
    /**
     * GET /api/product/{id}
     * Возвращает товар по ID
     */
    public function actionView($id): array
    {
        $product = \app\models\Product::findOne($id);
        
        if (!$product) {
            throw new NotFoundHttpException("Товар с ID {$id} не найден");
        }
        
        return [
            'id' => $product->id,
            'name' => $product->name,
            'category_name' => $product->category_name,
            'brand_name' => $product->brand_name,
            'price' => $this->getDisplayPrice($product),
            'rrp_price' => $product->rrp_price,
            'status' => $product->status,
        ];
    }
    
    /**
     * GET /api/product/brand/{name}
     * Возвращает min/max цены для бренда
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
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * POST /api/product/create
     * Создает товар
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
     * Обновляет товар
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
     * Отображение цены по правилам ТЗ
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
}