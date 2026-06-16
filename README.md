# 📦 Apltech — Product Management System

> Тестовое задание для компании APLTECH  
> REST API на Yii2 + мини-веб-приложение на Vue 3

---

## 🚀 Демо

| Ссылка | Описание |
|--------|----------|
| [apltech-front-eight.vercel.app](https://apltech-front-eight.vercel.app) | Vue 3 фронтенд |
| [apltech-backend.onrender.com/api/products](https://apltech-backend.onrender.com/api/products) | Yii2 API |
| [github.com/Saedddd/apltech](https://github.com/Saedddd/apltech) | Исходный код |

---

## ⚙️ Бэкенд (Yii2 REST API)

### Эндпоинты

| `GET` | `/api/products` |
| `GET` | `/api/product/{id}` | 
| `POST` | `/api/product/create` | 
| `PATCH` | `/api/product/update/{id}` | 
| `GET` | `/api/product/brand/{name}` |
| `POST` | `/api/auth/login` |

### Стек

- PHP 8.2
- Yii2
- PostgreSQL
- Firebase JWT

---

## 🖥️ Фронтенд (Vue 3)

### Страницы

- `/products` — список товаров
- `/product/:id` — детали товара
- `/product/create` — создание товара
- `/product/edit/:id` — редактирование
- `/brand/search` — поиск по бренду

### Стек

- Vue 3
- TypeScript
- Pinia
- PrimeVue
- Tailwind CSS
- Vite


## 🧪 Локальный запуск

### Бэкенд

```bash
cd backend
composer install
cp config/db.php.example config/db.php
php yii migrate/up
php -S localhost:8080 -t web

### Фронтенд

cd frontend
npm install
npm run dev
