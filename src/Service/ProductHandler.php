<?php

namespace App\Service;

class ProductHandler
{
    /**
     * @param array $productList
     * @return int
     * 获取商品列表的总价格
     */
    public static function GetAllPrice(array $productList):int
    {
        $priceList = array_column($productList, 'price');
        return array_sum($priceList);
    }


    /**
     * @param array $productList 商品列表，每个元素至少需要有type 和price字段
     * @param string $productType 类型 字符串
     * @return array
     * GetByTypeAndSort 根据类型获取对应的商品列表，并且根据价格大到小排序
     */
    public static function GetByTypeAndSort(array $productList, string $productType): array
    {
        $result = [];
        foreach ($productList as $product) {
            //非法数据，至少需要有type 和price字段
            if (!isset($product['type']) || !isset($product['price'])) {
                continue;
            }
            if ($product['type'] != $productType) {
                continue;
            }

            $result[] = $product;
            $priceList[] = $product['price'];
        }
        array_multisort($result, SORT_DESC, array_column($result, 'price'));
        return $result;
    }

    /**
     * @param $productList
     * 将商品的create_at 格式化成时间戳
     */
    public static function formatCreateTime($productList)
    {
        foreach ($productList as $k => $product) {
            if (!isset($product['created_at'])){
                continue;
            }
            $productList[$k]['created_at'] = strtotime($product['created_at']);
        }
        return $productList;
    }
}