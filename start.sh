#!/bin/sh
#echo "init DB"
#php /opt/dsw/workerman/initDb.php

#echo "create tables"
#vendor/bin/phinx migrate -e production

echo "php workman!"
php /opt/dsw/workerman/start.php start

tail -f /dev/null
