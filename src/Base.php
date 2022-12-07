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

        if ($post == '[]') $post = '{}';
        $md5 = md5($post);

        // 带"x-bili-"前缀的自定义header
        $biliHeaders = [
            'x-bili-accesskeyid'       => $this->accessKeyId,
            'x-bili-content-md5'       => $md5,
            'x-bili-signature-method'  => 'HMAC-SHA256',
            'x-bili-signature-nonce'   => uniqid(),
            'x-bili-signature-version' => '1.0',
            'x-bili-timestamp'         => (string)time()
        ];

        $headers = array_merge(
            $headers,
            $biliHeaders,
            ['Authorization' => $this->sign($biliHeaders)] // 请求签名
        );

        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = $this->apiDomain . $url;
        }

        $headerStringArray = [];
        foreach ($headers as $key => $value) {
            $headerStringArray[] = "{$key}: $value";
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => $headerStringArray,
        ));
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $data = json_decode($response, true);
        if (!is_array($data)) throw new Exception('请求哔哩哔哩接口接口错误' . $httpCode . $response);
        return $data;
    }

    /**
     * 签名
     * @param array $params 待签名数据
     * @return string
     */
    function sign(array $params): string
    {
        ksort($params);
        $signData = "";
        foreach ($params as $key => $value) {
            $signData .= $key . ":" . $value . "\n";
        }
        $signData = rtrim($signData, "\n");
        return hash_hmac("sha256", $signData, $this->accessKeySecred);
    }

}