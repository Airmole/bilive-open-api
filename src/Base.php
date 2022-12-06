<?php

namespace Airmole\BiliveOpenApi;

use Airmole\BiliveOpenApi\Exception\Exception;

class Base
{
    /**
     * @var string 环境域名
     */
    public string $apiDomain = 'https://live-open.biliapi.com';

    /**
     * @var string bilibili直播创作者服务中心 access_key_id
     */
    protected string $accessKeyId;

    /**
     * @var string bilibili直播创作者服务中心 access_key_secred
     */
    protected string $accessKeySecred;

    /**
     * 发送POST请求
     * @param string $url
     * @param $post
     * @return array
     * @throws Exception
     */
    public function bilivePost(string $url, $post): array
    {
        $post = is_array($post) ? json_encode($post) : $post;
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json'
        ];

        // 带"x-bili-"前缀的自定义header
        $biliHeaders = [
            'x-bili-content-md5'       => md5($post),
            'x-bili-timestamp'         => time(),
            'x-bili-signature-method'  => 'HMAC-SHA256',
            'x-bili-signature-nonce'   => rand(),
            'x-bili-accesskeyid'       => $this->accessKeyId,
            'x-bili-signature-version' => '1.0'
        ];

        $headers = array_merge(
            $headers,
            $biliHeaders,
            ['Authorization' => $this->sign($biliHeaders)] // 请求签名
        );

        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = $this->apiDomain . $url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);
        if (!is_array($data)) throw new Exception('请求哔哩哔哩接口接口错误' . $httpCode . $response);
        return $data;
    }

    /**
     * 签名
     * @param array $params 待签名数据
     * @return string
     */
    public function sign(array $params): string
    {
        ksort($params);
        $string = '';
        foreach ($params as $key => $value) {
            $string = $string . $key . ':' . $value;
        }
        $string = rtrim($string, "\n");

        $sign = hash_hmac('sha256', $string, $this->accessKeySecred, true);
        return base64_encode($sign);
    }

}