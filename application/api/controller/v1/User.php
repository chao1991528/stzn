<?php

namespace app\api\controller\v1;

use app\common\controller\Api;
use app\api\library\StatusCode;

/**
 * 用户接口
 * @ApiRoute (/api/v1/user)
 */
class User extends Api
{

    protected $noNeedLogin = ['login', 'resetpwd', 'third'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     * @ApiMethod   (POST)
     * @ApiParams   (name="username", type="string", required=true, description="用户名")
     * @ApiParams   (name="password", type="string", required=true, description="密码")
     * @ApiReturn   ({
        "code": 200,
        "msg": "登录成功",
        "data": {
            "id": 35,
            "username": "test_zyc",
            "token": "8684e994-2c85-4c86-9f32-b5c03fe624cf",
            "user_id": 35,
            "createtime": 1547276605,
            "expiretime": 1549868605,
            "expires_in": 2592000
        }
     })
     */
    public function login()
    {
        $data = $this->request->post();
        $result = $this->validate($data, 'User.login');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result, null, StatusCode::CODE_INVALID_PARAM);
        }
        $ret = $this->auth->login($data['username'], $data['password']);
        if ($ret) {
            $data = $this->auth->getUserinfo();
            $this->success(__('Logged in successful'), $data, StatusCode::CODE_SUCCESS);
        } else {
            $this->error($this->auth->getError(), null, StatusCode::CODE_LOGIN_ERROR);
        }
    }

    /**
     * 注销登录
     * @ApiMethod   (POST)
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 获取个人信息
     * @ApiMethod   (GET)
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiReturn   (
        {
            "code": 200,
            "msg": "登录成功",
            "data": {
                "id": 35,
                "username": "test_zyc",
                "token": "8684e994-2c85-4c86-9f32-b5c03fe624cf",
                "user_id": 35,
                "createtime": 1547276605,
                "expiretime": 1549868605,
                "expires_in": 2592000
            }
        }
    ) 
     */
    protected function getUserInfo()
    {
        $user = $this->auth->getUser();
        $imageDomain = config('image_domain');
        $data = [
            'username' => $user->username,
            'company_phone' => $user->userInfo->company_phone,
            'company_name' => $user->userInfo->company_name,
            'company_address' => $user->userInfo->company_address,
            'shipping_address' => $user->userInfo->shipping_address,//收货地址
            'id_card_front_image' => $imageDomain . DS . $user->userInfo->id_card_front_image,
            'id_card_back_image' => $imageDomain . DS . $user->userInfo->id_card_back_image,
            'business_license_image' => $imageDomain . DS . $user->userInfo->business_license_image,
            'door_face_image' => $imageDomain . DS . $user->userInfo->door_face_image,
            'auth_image' => $imageDomain . DS . $user->userInfo->auth_image,
            'contract_image' => $imageDomain . DS . $user->userInfo->contract_image
        ];
        $this->success(__('Success'), $data);
    }

    /**
     * 第三方登录
     * 
     * @param string $platform 平台名称
     * @param string $code Code码
     */
    protected function third()
    {
        $url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo' => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 修改密码
     * @ApiMethod   (POST)
     * @ApiParams   (name="old_password", type="string", required=true, description="旧密码")
     * @ApiParams   (name="new_password", type="string", required=true, description="新密码")
     * @ApiParams   (name="new_password_confirm", type="string", required=true, description="新密码确认密码")
     * @ApiReturn   (
        {
            "code": 200,
            "msg": "成功",
            "data": null
        }
    ) 
     */
    public function modifyPwd()
    {
        $data = $this->request->post();
        $result = $this->validate($data, 'User.modifyPwd');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result, null, StatusCode::CODE_INVALID_PARAM);
        }
        $user = $this->auth->getUser();
        $res = $user->modifyPwd($data);
        $res ? $this->success(__('Success')) : $this->error($user->getError());
    }

}
