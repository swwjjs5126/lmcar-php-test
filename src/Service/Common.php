<?php
namespace App\Service;

/**
 * 公用方法
 *
 *
 *
 */
class Common
{ 
    protected static $debug;

    /**
     * geo helper 地址转换为坐标
     * @param $address
     * @return bool|string
     * 问题点
     * 1）这一块如果多个人同时请求，并且缓存失效，则会多个同时请求api获取地址信息，需要加锁
     * 2）缓存key不要用中文，hash一下
     * 3）缓存方式有问题，一个地址一个key， 这样key后期会无限多。 最好放在一个hashMap里面的key值比较好，不会造成redis的key太多
     */
    public function geoHelperAddress($address, $merchant_id = '')
    {

        try {
            $cackeKey = 'cache-address-'.$address;

            // 從獲取座標
            $userLocation = redisx()->get($cackeKey);
            if ($userLocation) {
                return $userLocation;
            }

            $key = 'time=' . time();

            // requestLog：寫日志
            requestLog('Backend', 'Thrift', 'Http', 'phpgeohelper\\Geocoding->convert_addresses', 'https://geo-helper-hostr.ks-it.co',  [[$address, $key]]);

            // getThriftService： 獲取 Thrift 服務
            $geoHelper = ServiceContainer::getThriftService('phpgeohelper\\Geocoding');
            $param = json_encode([[$address, $key]]);

            // 調用接口，以地址獲取座標
            $response = $geoHelper->convert_addresses($param);
            $response = json_decode($response, true);

            if ($response['error'] == 0) {
                responseLog('Backend', 'phpgeohelper\\Geocoding->hksf_addresses', 'https://geo-helper-hostr.ks-it.co', '200', '0',  $response);
                $data = $response['data'][0];
                $coordinate = $data['coordinate'];

                // 如果返回 '-999,-999'，表示調用接口失敗，那麼直接使用商家位置的座標
                if ($coordinate == '-999,-999') {
                    infoLog('geoHelper->hksf_addresses change failed === ' . $address);
                    if ($merchant_id) {
                        $sMerchant = new Merchant();
                        $res = $sMerchant->get_merchant_address($merchant_id);
                        $user_location = $res['latitude'] . ',' . $res['longitude'];
                        return $user_location;
                    }
                    infoLog('geoHelper->hksf_addresses change failed === merchant_id is null' . $merchant_id);
                    return false;
                }
                if (!isset($data['error']) && (strpos($coordinate,',') !== false)) {
                    $arr = explode(',', $coordinate);
                    $user_location = $arr[1] . ',' . $arr[0];

                    // set cache
                    redisx()->set($cackeKey, $user_location);
                    return $user_location;
                }
            }
            responseLog('Backend', 'phpgeohelper\\Geocoding->hksf_addresses', 'https://geo-helper-hostr.ks-it.co', '401', '401',  $response);
            return false;
        } catch (\Throwable $t) {
            criticalLog('geoHelperAddress critical ==' . $t->getMessage());
            return 0;
        }
    }

    // 回调状态过滤
    /**
     * @param $order_id
     * @param $status
     * @return int|string
     * 问题
     * 1）常量改成配置，不然如果改动还需要修改代码
     * 2）只是考虑了在指定几个状态值的情况，日光不是这几个状态值如何处理没有解决 假如是904会返回什么？
     *
     */
    public static function checkStatusCallback($order_id, $status)
    {
        // 是900 可以回调
        if ($status == 900) {
            return 1;
        }
        // backend状态为 909 915 916 时 解锁工作单 但不回调
        $code_arr = ['909', '915', '916'];
        if (in_array($status, $code_arr)) {
            infoLog('checkStatusCallback backend code is 909 915 916');
            return 0;
        }

        $open_status_arr = ['901' => 1, '902' => 2, '903' => 3];
        return $order_id.'-'.$open_status_arr[$status];
    }
}
