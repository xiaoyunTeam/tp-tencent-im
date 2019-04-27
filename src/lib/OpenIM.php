<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class OpenIM extends IM
{
    /**
     * 获取用户在线状态
     * @param $id
     * @param $list
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryState($id)
    {
        return $this->httpsClient('openim', 'querystate', [
            'To_Account' => $id
        ]);
    }

    /**
     * 单发单聊消息
     * @param $id
     * @param $type
     * @param $content
     * @param null $offlineinfo
     * @param int $lefttime
     * @param int $machine
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendMsg($id, $to, $type, $content, $offlineinfo = null, $lefttime = 604800, $machine = 1)
    {
        $data = [
            'From_Account' => $id,
            'To_Account' => $to,
            'MsgRandom' => $this->getRandom(10),
            'MsgLifeTime' => $lefttime,
            'MsgTimeStamp' => time(),
            'MsgBody' => [
                'MsgType' => $type,
                'MsgContent' => $content
            ]
        ];
        if ($machine) {
            $data['SyncOtherMachine'] = $machine;
        }
        if ($offlineinfo) {
            $data['OfflinePushInfo'] = $offlineinfo;
        }
        return $this->httpsClient('openim', 'sendmsg', $data);
    }

    /**
     * 批量发单聊消息
     * @param $id
     * @param $type
     * @param $content
     * @param null $offlineinfo
     * @param int $lefttime
     * @param int $machine
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchSendMsg($id, $to, $type, $content, $offlineinfo = null, $lefttime = 604800, $machine = 1)
    {
        $data = [
            'From_Account' => $id,
            'To_Account' => $to,
            'MsgRandom' => $this->getRandom(10),
            'MsgLifeTime' => $lefttime,
            'MsgTimeStamp' => time(),
            'MsgBody' => [
                'MsgType' => $type,
                'MsgContent' => $content
            ]
        ];
        if ($machine) {
            $data['SyncOtherMachine'] = $machine;
        }
        if ($offlineinfo) {
            $data['OfflinePushInfo'] = $offlineinfo;
        }
        return $this->httpsClient('openim', 'batchsendmsg', $data);
    }

    /**
     * 导入单聊消息
     * @param $id
     * @param $type
     * @param $content
     * @param null $offlineinfo
     * @param int $lefttime
     * @param int $machine // 1:平滑过渡期间，实时消息导入，消息计入未读 2: 历史消息导入，消息不计入未读
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function importMsg($id, $to, $type, $content, $offlineinfo = null, $lefttime = 604800, $machine = 1)
    {
        $data = [
            'From_Account' => $id,
            'To_Account' => $to,
            'MsgRandom' => $this->getRandom(10),
            'MsgLifeTime' => $lefttime,
            'MsgTimeStamp' => time(),
            'MsgBody' => [
                'MsgType' => $type,
                'MsgContent' => $content
            ]
        ];
        if ($machine) {
            $data['SyncOtherMachine'] = $machine;
        }
        if ($offlineinfo) {
            $data['OfflinePushInfo'] = $offlineinfo;
        }
        return $this->httpsClient('openim', 'importmsg', $data);
    }

}