<?php

namespace Airmole\BiliveOpenApi;

use Airmole\BiliveOpenApi\Exception\Exception;

/**
 * 哔哩哔哩直播开放平台 Open Api
 * doc：https://open-live.bilibili.com/document/doc&tool/api/interactPlay.html
 */
class BiliveService extends Base
{
    /**
     * 项目开启
     * @param string $code 主播身份码
     * @param string $appId 项目ID
     * @return array
     * @throws Exception
     */
    public function start(string $code, string $appId): array
    {
        return $this->bilivePost('/v2/app/start', [
            'code'   => $code,
            'app_id' => $appId
        ]);
    }

    /**
     * 项目关闭
     * @param string $appId 13位项目ID
     * @param string $ganmeId 场次ID
     * @return array
     * @throws Exception
     */
    public function end(string $appId, string $ganmeId): array
    {
        return $this->bilivePost('/v2/app/end', [
            'app_id'  => $appId,
            'game_id' => $ganmeId
        ]);
    }

    /**
     * 项目心跳
     * @param string $gameId 场次ID
     * @return array
     * @throws Exception
     */
    public function heartBeat(string $gameId): array
    {
        return $this->bilivePost('/v2/app/heartbeat', [
            'game_id' => $gameId
        ]);
    }

    /**
     * 项目批量心跳
     * @param array $gameIds
     * @return array
     * @throws Exception
     */
    public function batchHeartBeat(array $gameIds): array
    {
        return $this->bilivePost('/v2/app/batchHeartbeat', [
            'game_ids' => $gameIds
        ]);
    }

    /**
     * 验证应用商店签名
     * @param string $caller
     * @param string $code
     * @param string $mid
     * @param string $timestamp
     * @param string $sign
     * @return bool
     */
    public function checkSign(string $caller, string $code, string $mid, string $timestamp, string $sign): bool
    {
        $para = [
            'Caller'    => $caller,
            'Code'      => $code,
            'Mid'       => $mid,
            'Timestamp' => $timestamp
        ];

        $signed = $this->sign($para);
        if ($signed != $sign) return false;
        return true;
    }
}