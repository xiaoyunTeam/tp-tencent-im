<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class Snsim extends IM
{
    /**
     * 添加好友
     * @param $id
     * @param $item
     * @param null $type
     * @param null $flags
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendAdd($id, $item, $type = null, $flags = null)
    {
        $data = [
            'From_Account' => $id,
            'AddFriendItem' => $item
        ];
        if ($type) {
            $data['AddType'] = $type;
        }
        if ($flags) {
            $data['ForceAddFlags'] = $flags;
        }
        return $this->httpsClient('sns', 'friend_add', $data);
    }

    /**
     * 导入好友
     * @param $id
     * @param $item
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendImport($id, $item)
    {
        return $this->httpsClient('sns', 'friend_import', [
            'From_Account' => $id,
            'AddFriendItem' => $item
        ]);
    }

    /**
     * 更新好友
     * @param $id
     * @param $item
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendUpdate($id, $item)
    {
        return $this->httpsClient('sns', 'friend_update', [
            'From_Account' => $id,
            'AddFriendItem' => $item
        ]);
    }

    /**
     * 删除好友
     * @param $id
     * @param $to
     * @param string $type
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendDelete($id, $to, $type = 'Delete_Type_Single')
    {
        return $this->httpsClient('sns', 'friend_delete', [
            'From_Account' => $id,
            'To_Account' => $to,
            'DeleteType' => $type
        ]);
    }

    /**
     * 删除全部好友
     * @param $id
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendDeleteAll($id)
    {
        return $this->httpsClient('sns', 'friend_delete_all', [
            'From_Account' => $id
        ]);
    }

    /**
     * 校验好友
     * @param $id
     * @param $to
     * @param string $type
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendCheck($id, $to, $type = 'CheckResult_Type_Both')
    {
        return $this->httpsClient('sns', 'friend_check', [
            'From_Account' => $id,
            'To_Account' => $to,
            'CheckType' => $type
        ]);
    }

    /**
     * 拉取好友
     * @param $id
     * @param array $tag
     * @param int $start
     * @param int $time
     * @param int $sequence
     * @param int $count
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendGetAll($id, $tag = [
        'Tag_Profile_IM_Nick', 'Tag_Profile_IM_Gender', 'Tag_Profile_IM_BirthDay', 'Tag_Profile_IM_Location',
        'Tag_Profile_IM_SelfSignature', 'Tag_Profile_IM_AllowType', 'Tag_Profile_IM_Language', 'Tag_Profile_IM_Image',
        'Tag_Profile_IM_MsgSettings', 'Tag_Profile_IM_AdminForbidType', 'Tag_Profile_IM_Level', 'Tag_Profile_IM_Role'
    ], $start = 0, $time = 0, $sequence = 0, $count = 100)
    {
        return $this->httpsClient('sns', 'friend_get_all', [
            'From_Account' => $id,
            'StartIndex' => $start,
            'TimeStamp' => $time,
            'TagList' => $tag,
            'LastStandardSequence' => $sequence,
            'GetCount' => $count
        ]);
    }

    /**
     * 拉取指定好友
     * @param $id
     * @param array $to
     * @param array $tag
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function friendGetList($id, $to, $tag = [
        'Tag_Profile_IM_Nick', 'Tag_Profile_IM_Gender', 'Tag_Profile_IM_BirthDay', 'Tag_Profile_IM_Location',
        'Tag_Profile_IM_SelfSignature', 'Tag_Profile_IM_AllowType', 'Tag_Profile_IM_Language', 'Tag_Profile_IM_Image',
        'Tag_Profile_IM_MsgSettings', 'Tag_Profile_IM_AdminForbidType', 'Tag_Profile_IM_Level', 'Tag_Profile_IM_Role'
    ])
    {
        return $this->httpsClient('sns', 'friend_get_list', [
            'From_Account' => $id,
            'To_Account' => $to,
            'TagList' => $tag
        ]);
    }

    /**
     * 添加黑名单
     * @param $id
     * @param $to
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function blackListAdd($id, $to)
    {
        return $this->httpsClient('sns', 'friend_get_list', [
            'From_Account' => $id,
            'To_Account' => $to
        ]);
    }

    /**
     * 删除黑名单
     * @param $id
     * @param $to
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function blackListDelete($id, $to)
    {
        return $this->httpsClient('sns', 'black_list_delete', [
            'From_Account' => $id,
            'To_Account' => $to
        ]);
    }

    /**
     * 拉取黑名单
     * @param $id
     * @param int $start
     * @param int $maxLimited
     * @param int $lastsequence
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function blackListGet($id, $start = 0, $maxLimited = 50, $lastsequence = 0)
    {
        return $this->httpsClient('sns', 'black_list_get', [
            'From_Account' => $id,
            'StartIndex' => $start,
            'MaxLimited' => $maxLimited,
            'LastSequence' => $lastsequence
        ]);
    }

    /**
     * 校验黑名单
     * @param $id
     * @param $to
     * @param string $type
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function blackListCheck($id, $to, $type = 'BlackCheckResult_Type_Both')
    {
        return $this->httpsClient('sns', 'black_list_check', [
            'From_Account' => $id,
            'To_Account' => $to,
            'CheckType' => $type
        ]);
    }

    /**
     * 添加分组
     * @param $id
     * @param $to
     * @param string $type
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groupAdd($id, $groupName, $to = [])
    {
        $data = [
            'From_Account' => $id,
            'GroupName' => $groupName
        ];
        if ($to) {
            $data['To_Account'] = $to;
        }
        return $this->httpsClient('sns', 'group_add', $data);
    }

    /**
     * 删除分组
     * @param $id
     * @param $groupname
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groupDelete($id, $groupname)
    {
        return $this->httpsClient('sns', 'group_delete', [
            'From_Account' => $id,
            'GroupName' => $groupname
        ]);
    }
}