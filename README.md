# bilive-open-api

bilibili直播开放平台API接口。

- [官方接口文档](https://open-live.bilibili.com/document/doc&tool/auth.html)

![bilive-open-api](https://socialify.git.ci/Airmole/bilive-open-api/image?font=Raleway&forks=1&issues=1&language=1&name=1&owner=1&pattern=Floating%20Cogs&pulls=1&stargazers=1&theme=Auto)

## install

```shell
composer require "airmole/bilive-open-api"
```

## 用法

```php
<?php

use Airmole\BiliveOpenApi\BiliveService;

$biliveService = new BiliveService('access_key_id', 'access_key_secred');

$start = $biliveService->start('code', 'app_id');
echo "项目开启：\r\n";
print_r($start);

$gameId = $start['data']['game_info']['game_id'];
$heartBeat = $biliveService->heartBeat($gameId);
echo "项目心跳：\r\n";
print_r($heartBeat);

/*
$batchHeartBeat = $biliveService->batchHeartBeat([$gameId]);
echo "项目批量心跳：\r\n";
print_r($batchHeartBeat);
*/

echo "项目关闭：\r\n";
$close = $biliveService->end('app_id', $gameId);
print_r($close);
```

## 参数说明

- [返回参数格式](https://open-live.bilibili.com/document/doc&tool/api/interactPlay.html#%E9%A1%B9%E7%9B%AE%E5%BC%80%E5%90%AF)
- [配置参数FAQ](https://open-live.bilibili.com/document/Q&A.html#%E5%BC%80%E5%8F%91%E7%9B%B8%E5%85%B3%E9%97%AE%E9%A2%98)

... 更多详细完整说明请参阅[源码](src/)以及[官方文档](https://open-live.bilibili.com/document/quickStart.html)


