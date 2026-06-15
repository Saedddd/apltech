#!/bin/sh

SOURCE_FILE=$(find / -name "product.json" -path "*/runtime/product.json" 2>/dev/null | head -1)

if [ -n "$SOURCE_FILE" ]; then
    echo "Found product.json at: $SOURCE_FILE"
    mkdir -p /var/www/html/runtime
    cp "$SOURCE_FILE" /var/www/html/runtime/product.json
    echo "Copied product.json to /var/www/html/runtime/"
else
    echo "Warning: product.json not found in repository"
fi


# php yii migrate/up --interactive=0


php-fpm -D && nginx -g 'daemon off;'