<?php


namespace XiaoYun\Tencent\lib;


use XiaoYun\Tencent\IM;

class LoginSvc extends IM
{
    /**
     * 单个帐号导入接口
     * @param $id
     * @param $nickname
     * @param $faceUrl
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accountImport($id, $nickname, $faceUrl)
    {
        return $this->httpsClient('im_open_login_svc', 'account_import', [
            'Identifier' => $id,
            'Nick' => $nickname,
            'FaceUrl' => $faceUrl
        ]);
    }

    /**
     * 批量帐号导入接口
     * @param $id
     * @param $nickname
     * @param $faceUrl
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function multiaccountImport($accounts)
    {
        return $this->httpsClient('im_open_login_svc', 'multiaccount_import', [
            'Accounts' => $accounts,
        ]);
    }

    /**
     * 帐号登录态失效接口
     * @param $id
     * @param $nickname
     * @param $faceUrl
     * @return bool|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function kick($id)
    {
        return $this->httpsClient('im_open_login_svc', 'kick', [
            'Identifier' => $id,
        ]);
    }
}
