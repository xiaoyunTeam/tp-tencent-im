<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class ConfigSvr extends IM
{
    /**
     * 设置全局禁言
     * @param $account
     * @param int $c2cmsgtime 单聊消息禁言时间，单位为秒，非负整数，最大值为 4294967295（十六进制 0xFFFFFFFF）。等于 0 代表取消帐号禁言；
     * @param int $groupmsgtime 群组消息禁言时间，单位为秒，非负整数，最大值为 4294967295（十六进制 0xFFFFFFFF）。等于 0 代表取消帐号禁言；
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setNoSpeaking($account, $c2cmsgtime = 0, $groupmsgtime = 0)
    {
        $data['Set_Account'] = $account;
        if ($c2cmsgtime) {
            $data['C2CmsgNospeakingTime'] = $c2cmsgtime;
        }
        if ($groupmsgtime) {
            $data['GroupmsgNospeakingTime'] = $groupmsgtime;
        }
        return $this->httpsClient('openconfigsvr', 'setnospeaking', $data);
    }

    /**
     * 查询全局禁言
     * @param $account
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNoSpeaking($account)
    {
        return $this->httpsClient('openconfigsvr', 'getnospeaking', [
            'Get_Account' => $account
        ]);
    }

    /**
     * 拉取运营数据
     * @param array $field 该字段用来指定需要拉取的运营数据，不填默认拉取所有字段。详细可参阅下文可拉取的运营字段
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAppInfo($field = [])
    {
        $data = $field ? ['RequestField' => $field] : [];
        return $this->httpsClient('openconfigsvr', 'getappinfo', $data);
    }

    /**
     * 下载消息记录
     * @param $type 消息类型，C2C表示单发消息 Group表示群组消息
     * @param $time 需要下载的时间段，2015120121 表示获取 2015 年 12 月 1 日 21:00~21:59 的消息的下载地址
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHistory($type, $time)
    {
        return $this->httpsClient('openconfigsvr', 'get_history', [
            'ChatType' => $type,
            'MsgTime' => $time
        ]);
    }
}