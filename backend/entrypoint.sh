#!/bin/sh

mkdir -p /var/www/html/runtime


cat > /var/www/html/runtime/product.json << 'EOF'
[
    {
        "id": 101,
        "name": "Apple Watch",
        "category_name": "Wearables",
        "brand_name": "Apple",
        "price": 399,
        "rrp_price": 449,
        "status": 1
    },
    {
        "id": 102,
        "name": "Apple iPad",
        "category_name": "Tablets",
        "brand_name": "Apple",
        "price": "цена по запросу",
        "rrp_price": 599,
        "status": 2
    }
]
EOF


ls -la /var/www/html/runtime/
cat /var/www/html/runtime/product.json

# php yii migrate/up --interactive=0

php-fpm -D && nginx -g 'daemon off;'