<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class GroupSvc extends IM
{
    /**
     * 获取App中的所有群组
     * @param null $limit
     * @param null $next
     * @param null $type
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupList($limit = null, $next = null, $type = null)
    {
        $data = [];
        if ($limit) {
            $data['Limit'] = $limit; // 本次获取的群组 ID 数量的上限，不得超过 10000。如果不填，默认为最大值 10000
        }
        if ($next) {
            $data['Next'] = $limit; // 群太多时分页拉取标志，第一次填 0，以后填上一次返回的值，返回的 Next 为 0 代表拉完了
        }
        if ($type) {
            $data['GroupType'] = $limit; // 群组形态包括 Public（公开群），Private（私密群），ChatRoom（聊天室），AVChatRoom（音视频聊天室）和 BChatRoom（在线成员广播大群）
        }
        return $this->httpsClient('group_open_http_svc', 'get_appid_group_list', $data);
    }

    /**
     * 创建群组
     * @param $name - 群名称，最长 30 字节
     * @param string $type 群组形态，包括 Public（公开群），Private（私密群），ChatRoom（聊天室），AVChatRoom（音视频聊天室），BChatRoom（在线成员广播大群）
     * @param null $owner 群主 ID，自动添加到群成员中。如果不填，群没有群主
     * @param null $groupId 为了使得群组 ID 更加简单，便于记忆传播，腾讯云支持 App 在通过 REST API 创建群组时自定义群组 ID；详情请参阅 群组系统
     * @param null $introduction 群简介，最长 240 字节
     * @param null $notification 群公告，最长 300 字节
     * @param null $faceurl 群头像 URL，最长 100 字节
     * @param int $maxmember 最大群成员数量，缺省时的默认值：私有群是 200，公开群是 2000，聊天室是 10000，音视频聊天室和在线成员广播大群无限制
     * @param string $joinoption 申请加群处理方式。包含 FreeAccess（自由加入），NeedPermission（需要验证），DisableApply（禁止加群），不填默认为 NeedPermission（需要验证）
     * @param array $defaultdata 群组维度的自定义字段，默认情况是没有的，需要开通，详情请参阅 群组系统
     * @param array $menberlist 初始群成员列表，最多 500 个；成员信息字段详情请参阅 群组系统
     * @return bool|\Psr\Http\Message\StreamInterface 群成员维度的自定义字段，默认情况是没有的，需要开通，详情请参阅 群组系统
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createGroup($name, $type = 'Public', $owner = null, $groupId = null, $introduction = null, $notification = null, $faceurl = null, $maxmember = 0, $joinoption = 'NeedPermission', $defaultdata = [], $menberlist = [])
    {
        $data = [
            'Name' => $name,
            'Type' => $type,
        ];
        if ($owner) {
            $data['Owner_Account'] = $owner;
        }
        if ($groupId) {
            $data['GroupId'] = $groupId;
        }
        if ($introduction) {
            $data['Introduction'] = $introduction;
        }
        if ($notification) {
            $data['Notification'] = $notification;
        }
        if ($faceurl) {
            $data['FaceUrl'] = $faceurl;
        }
        if ($maxmember) {
            $data['MaxMemberCount'] = $maxmember;
        }
        if ($joinoption) {
            $data['ApplyJoinOption'] = $joinoption;
        }
        if ($defaultdata) {
            $data['AppDefinedData'] = $defaultdata;
        }
        if ($menberlist) {
            $data['MemberList'] = $menberlist;
        }
        return $this->httpsClient('group_open_http_svc', 'create_group', $data);
    }

    /**
     * 获取群组详细资料
     * @param $groupidlist - 需要拉取的群组列表
     * @param array $filter 包含三个过滤器：GroupBaseInfoFilter，MemberInfoFilter，AppDefinedDataFilter_Group，分别是基础信息字段过滤器，成员信息字段过滤器，群组维度的自定义字段过滤器
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupInfo($groupidlist, $filter = [])
    {
        $data = [
            'GroupIdList' => $groupidlist,
            'Type' => $filter,
        ];
        return $this->httpsClient('group_open_http_svc', 'get_group_info', $data);
    }

    /**
     * 获取群组成员详细资料
     * @param $groupid  需要拉取成员信息的群组的 ID
     * @param int $limit 一次最多获取多少个成员的资料，不得超过 10000。如果不填，则获取群内全部成员的信息
     * @param int $offset 从第几个成员开始获取，如果不填则默认为 0，表示从第一个成员开始获取
     * @param array $infofilter 需要获取哪些信息， 如果没有该字段则为群成员全部资料，成员信息字段详情请参阅 群组系统
     * @param array $rolefilter 拉取指定身份的群成员资料。如没有填写该字段，默认为所有身份成员资料，成员身份可以为：“Owner”，“Admin”，“Member”
     * @param array $defineddatafilter 默认情况是没有的。该字段用来群成员维度的自定义字段过滤器，指定需要获取的群成员维度的自定义字段，群成员维度的自定义字段详情请参阅 群组系统
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupMemberInfo($groupid, $limit = 0, $offset = 0, $infofilter = [], $rolefilter = [], $defineddatafilter = [])
    {
        $data['GroupId'] = $groupid;
        if ($limit) {
            $data['Limit'] = $limit;
        }
        if ($offset) {
            $data['Offset'] = $offset;
        }
        if ($infofilter) {
            $data['MemberInfoFilter'] = $infofilter;
        }
        if ($rolefilter) {
            $data['MemberRoleFilter'] = $rolefilter;
        }
        if ($defineddatafilter) {
            $data['AppDefinedDataFilter_GroupMember'] = $defineddatafilter;
        }
        return $this->httpsClient('group_open_http_svc', 'get_group_member_info', $data);
    }

    /**
     * 增加群组成员
     * @param $groupid
     * @param $memberlist = [{ "Member_Account": "jared"}]
     * @param int $silence 是否静默加人。0：非静默加人；1：静默加人。不填该字段默认为 0
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addGroupMember($groupid, $memberlist, $silence = 0)
    {
        return $this->httpsClient('group_open_http_svc', 'get_group_member_info', [
            'GroupId' => $groupid,
            'MemberList' => $memberlist,
            'Silence' => $silence
        ]);
    }

    /**
     * 删除群组成员
     * @param $groupid
     * @param $memberlist
     * @param int $silence
     * @param string $reason
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteGroupMember($groupid, $memberlist, $silence = 0, $reason = '')
    {
        $data = [
            'GroupId' => $groupid,
            'MemberToDel_Account' => $memberlist,
            'Silence' => $silence
        ];
        if ($reason) {
            $data['Reason'] = $reason;
        }
        return $this->httpsClient('group_open_http_svc', 'delete_group_member', $data);
    }

    /**
     * 修改群成员资料
     * @param $groupid
     * @param $memberlist
     * @param array $others
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function modifyGroupMemberInfo($groupid, $memberlist, $others = [])
    {
        $others['GroupId'] = $groupid;
        $others['MemberToDel_Account'] = $memberlist;
        return $this->httpsClient('group_open_http_svc', 'modify_group_member_info', $others);
    }

    /**
     * 解散群组
     * @param $groupid
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroyGroup($groupid)
    {
        return $this->httpsClient('group_open_http_svc', 'destroy_group', [
            'GroupId' => $groupid
        ]);
    }

    /**
     * 获取用户所加入的群组
     * @param $memberlist
     * @param null $type
     * @param int $limit
     * @param int $offset
     * @param array $baseinfofilter 表示需要拉取哪些基础信息字段，详情请参阅 群组系统；SelfInfoFilter 表示需要拉取用户在每个群组中的哪些个人资料，详情请参阅 群组系统
     * @param array $selfinfofilter
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJoinedGroupList($memberlist, $type = null, $limit = 0, $offset = 0, $baseinfofilter = [], $selfinfofilter = [])
    {
        $data['Member_Account'] = $memberlist;
        if ($type) {
            $data['GroupType'] = $type;
        }
        if ($limit) {
            $data['Limit'] = $limit;
        }
        if ($offset) {
            $data['Offset'] = $offset;
        }
        if ($baseinfofilter) {
            $data['ResponseFilter']['GroupBaseInfoFilter'] = $baseinfofilter;
        }
        if ($selfinfofilter) {
            $data['ResponseFilter']['SelfInfoFilter'] = $selfinfofilter;
        }
        return $this->httpsClient('group_open_http_svc', 'get_joined_group_list', $data);
    }

    /**
     * 查询用户在群组中的身份
     * @param $groupid
     * @param $accounts  表示需要查询的用户帐号，最多支持 500 个帐号
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRoleInGroup($groupid, $accounts)
    {
        return $this->httpsClient('group_open_http_svc', 'get_role_in_group', [
            'GroupId' => $groupid,
            'User_Account' => $accounts
        ]);
    }

    /**
     * 批量禁言和取消禁言
     * @param $groupid
     * @param $accounts  需要禁言的用户帐号，最多支持 500 个帐号
     * @param $time  需禁言时间，单位为秒，为 0 时表示取消禁言
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function forbidSendMsg($groupid, $accounts, $time)
    {
        return $this->httpsClient('group_open_http_svc', 'forbid_send_msg', [
            'GroupId' => $groupid,
            'Members_Account' => $accounts,
            'ShutUpTime' => $time
        ]);
    }

    /**
     * 获取群组被禁言用户列表
     * @param $groupid
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupShuttedUin($groupid)
    {
        return $this->httpsClient('group_open_http_svc', 'get_group_shutted_uin', [
            'GroupId' => $groupid
        ]);
    }

    /**
     * 在群组中发送普通消息
     * @param $groupid  向哪个群组发送消息
     * @param $id    消息来源帐号，选填。如果不填写该字段，则默认消息的发送者为调用该接口时使用的 App 管理员帐号。除此之外，App 亦可通过该字段“伪造”消息的发送者，从而实现一些特殊的功能需求。需要注意的是，如果指定该字段，必须要确保字段中的帐号是存在的
     * @param $body 消息体，详细可参阅 消息格式描述
     * @param null $priority 可以指定消息的优先级，默认优先级 Normal； 可以指定4种优先级，从高到低依次为 High，Normal，Low，Lowest，区分大小写。
     * @param null $offlineinfo 离线推送信息配置，详细可参阅 消息格式描述
     * @param bool $callbackcontrol 消息回调禁止开关，只对单条消息有效，ForbidBeforeSendMsgCallback 表示禁止发消息前回调，ForbidAfterSendMsgCallback 表示禁止发消息后回调
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendGroupMsg($groupid, $id, $body, $priority = null, $offlineinfo = null, $callbackcontrol = false)
    {
        $data['GroupId'] = $groupid;
        $data['Random'] = rand(10000000000000000000000000000000, 99999999999999999999999999999999);
        $data['MsgBody'] = $body;
        if ($id) {
            $data['From_Account'] = $id;
        }
        if ($priority) {
            $data['MsgPriority'] = $priority;
        }
        if ($offlineinfo) {
            $data['OfflinePushInfo'] = $offlineinfo;
        }
        if ($callbackcontrol) {
            $data['ForbidCallbackControl'] = $callbackcontrol;
        }
        return $this->httpsClient('group_open_http_svc', 'send_group_msg', $data);
    }

    /**
     * 在群组中发送系统通知
     * @param $groupid
     * @param $content
     * @param array $accounts 接收者群成员列表，不填或为空表示全员下发
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendGroupSystemNotification($groupid, $content, $accounts = [])
    {
        $data['GroupId'] = $groupid;
        $data['Content'] = $content;
        if ($accounts) {
            $data['From_Account'] = $accounts;
        }
        return $this->httpsClient('group_open_http_svc', 'send_group_system_notification', $data);
    }

    /**
     * 群组消息撤回
     * @param $groupid
     * @param $seqList 被撤回的消息 seq 列表，一次请求最多可以撤回 10 条消息 seq
     * @param $seq 请求撤回的消息 seq
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groupMsgRecall($groupid, $seqList, $seq)
    {
        return $this->httpsClient('group_open_http_svc', 'group_msg_recall', [
            'GroupId' => $groupid,
            'MsgSeqList' => $seqList,
            'MsgSeq' => $seq
        ]);
    }

    /**
     * 转让群组
     * @param $groupid
     * @param $newowner  新群主 ID
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeGroupOwner($groupid, $newowner)
    {
        return $this->httpsClient('group_open_http_svc', 'change_group_owner', [
            'GroupId' => $groupid,
            'NewOwner_Account' => $newowner
        ]);
    }

    /**
     * 导入群基础资料
     * @param $name
     * @param string $type
     * @param null $owner
     * @param null $groupId
     * @param null $introduction
     * @param null $notification
     * @param null $faceurl
     * @param int $maxmember
     * @param string $joinoption
     * @param array $defaultdata
     * @param array $menberlist
     * @param null $createtime
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function importGroup($name, $type = 'Public', $owner = null, $groupId = null, $introduction = null, $notification = null, $faceurl = null, $maxmember = 0, $joinoption = 'NeedPermission', $defaultdata = [], $menberlist = [], $createtime = null)
    {
        $data = [
            'Type' => $type,
            'Name' => $name
        ];
        if ($owner) {
            $data['Owner_Account'] = $owner;
        }
        if ($groupId) {
            $data['GroupId'] = $groupId;
        }
        if ($introduction) {
            $data['Introduction'] = $introduction;
        }
        if ($notification) {
            $data['Notification'] = $notification;
        }
        if ($faceurl) {
            $data['FaceUrl'] = $faceurl;
        }
        if ($maxmember) {
            $data['MaxMemberCount'] = $maxmember;
        }
        if ($joinoption) {
            $data['ApplyJoinOption'] = $joinoption;
        }
        if ($defaultdata) {
            $data['AppDefinedData'] = $defaultdata;
        }
        if ($menberlist) {
            $data['MemberList'] = $menberlist;
        }
        if ($createtime) {
            $data['CreateTime'] = $createtime;
        }
        return $this->httpsClient('group_open_http_svc', 'import_group', $data);
    }

    /**
     * 导入群消息
     * @param $groupid
     * @param $account
     * @param $msglist
     * @param $sendtime
     * @param $msgbody
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function importGroupMsg($groupid, $account, $msglist, $sendtime, $msgbody)
    {
        return $this->httpsClient('group_open_http_svc', 'import_group_msg', [
            'GroupId' => $groupid,
            'From_Account' => $account,
            'MsgList' => $msglist,
            'Random' => rand(10000000000000000000000000000000, 99999999999999999999999999999999),
            'SendTime' => $sendtime,
            'MsgBody' => $msgbody,
        ]);
    }

    /**
     * 导入群成员
     * @param $groupid
     * @param $memberlist
     * @param $memberaccount
     * @param string $role 待导入群成员角色；目前只支持填 Admin，不填则为普通成员 Member
     * @param int $jointime
     * @param int $unreadmsgnum
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function importGroupMember($groupid, $memberlist, $memberaccount, $role = 'Member', $jointime = 0, $unreadmsgnum = 0)
    {
        $data = [
            'GroupId' => $groupid,
            'MemberList' => $memberlist,
            'Member_Account' => $memberaccount,
            'Role' => $role
        ];
        if ($jointime) {
            $data['JoinTime'] = $jointime;
        }
        if ($unreadmsgnum) {
            $data['UnreadMsgNum'] = $unreadmsgnum;
        }
        return $this->httpsClient('group_open_http_svc', 'import_group_member', $data);
    }

    /**
     * 设置成员未读消息计数
     * @param $groupid
     * @param $memberlist
     * @param $memberaccount
     * @param string $role 待导入群成员角色；目前只支持填 Admin，不填则为普通成员 Member
     * @param int $jointime
     * @param int $unreadmsgnum
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setUnreadMsgNum($groupid, $memberlist, $memberaccount, $role = 'Member', $jointime = 0, $unreadmsgnum = 0)
    {
        $data = [
            'GroupId' => $groupid,
            'MemberList' => $memberlist,
            'Member_Account' => $memberaccount,
            'Role' => $role
        ];
        if ($jointime) {
            $data['JoinTime'] = $jointime;
        }
        if ($unreadmsgnum) {
            $data['UnreadMsgNum'] = $unreadmsgnum;
        }
        return $this->httpsClient('group_open_http_svc', 'set_unread_msg_num', $data);
    }

    /**
     * 删除指定用户发送的消息
     * @param $groupid
     * @param $account
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteGroupMsgBySender($groupid, $account)
    {
        return $this->httpsClient('group_open_http_svc', 'delete_group_msg_by_sender', [
            'GroupId' => $groupid,
            'Sender_Account' => $account
        ]);
    }

    /**
     * 拉取群漫游消息
     * @param $groupid
     * @param $number
     * @param int $seq 按指定 seq 拉取群组的漫游消息； 返回消息 seq 小于等于 ReqMsgSeq 的 ReqMsgNumber 条消息。 {
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function groupMsgGetSimple($groupid, $number, $seq = 0)
    {
        $data = [
            'GroupId' => $groupid,
            'ReqMsgNumber' => $number
        ];
        if ($seq) {
            $data['ReqMsgSeq'] = $seq;
        }
        return $this->httpsClient('group_open_http_svc', 'group_msg_get_simple', $data);
    }
}