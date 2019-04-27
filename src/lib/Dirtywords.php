<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class Dirtywords extends IM
{
    /**
     * 查询 App 自定义脏字
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get()
    {
        return $this->httpsClient('openim_dirty_words', 'get', []);
    }

    /**
     * 添加 App 自定义脏字
     * @param $list { "DirtyWordsList": [ // 自定义脏字列表（必填），列表中的脏字不能超过50个 "韩国代购", // 每个自定义脏字不能超过 200 字节 "发票" ] }
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add($list)
    {
        return $this->httpsClient('openim_dirty_words', 'add', [
            'DirtyWordsList' => $list
        ]);
    }

    /**
     * 删除 App 自定义脏字
     * @param $list { "DirtyWordsList": [ // 自定义脏字列表（必填），列表中的脏字不能超过50个 "韩国代购", // 每个自定义脏字不能超过 200 字节 "发票" ] }
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($list)
    {
        return $this->httpsClient('openim_dirty_words', 'delete', [
            'DirtyWordsList' => $list
        ]);
    }
}