<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class Profile extends IM
{
    /**
     * 拉取资料
     * @param $id
     * @param $list
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function portraitGet($id, $list)
    {
        return $this->httpsClient('profile', 'portrait_get', [
            'To_Account' => $id,
            'TagList' => $list
        ]);
    }

    /**
     * 设置资料
     * @param $id
     * @param $item
     * @param $tag
     * @param $value
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function portraitSet($id, $item, $tag, $value)
    {
        return $this->httpsClient('profile', 'portrait_get', [
            'From_Account' => $id,
            'ProfileItem' => $item,
            'Tag' => $tag,
            'Value' => $value
        ]);
    }
}