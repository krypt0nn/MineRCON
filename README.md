# MineRCON

**MineRCON** - PHP библиотека для работы с RCON серверов Minecraft

## Установка

```
php qero.phar i KRypt0nn/MineRCON
```

## Использование

```php
<?php

use MineRCON\RCON;

$ip       = readline ('Server IP: ');
$port     = readline ('RCON port: ');
$password = readline ('RCON password: ');

echo PHP_EOL;

try
{
    $rcon = new RCON ($ip, $port, $password);
}

catch (Exception $e)
{
    die ('Connection error'. PHP_EOL);
}

while (true)
    echo ($rcon->send (readline ('> ')) ?: '# command sending error') . PHP_EOL;
```

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](https://vk.com/hphp_convertation)