#!/bin/bash
php /var/www/html/init-db.php
exec apache2-foreground
