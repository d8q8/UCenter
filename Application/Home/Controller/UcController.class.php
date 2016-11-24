<?php
/**
 * Created by PhpStorm.
 * User: d8q8
 * Date: 2016/11/21
 * Time: 16:24
 */

namespace Home\Controller;

use UCenter\Client\UcApi;

/**
 * 统一UC控制器
 * Class UcController
 * @package Home\Controller
 */
class UcController extends BaseController
{
    protected $uc; //统一UC实例化
    protected $authPre;//统一登录cookie前缀

    /**
     * 初始化
     */
    protected function init()
    {
        $this->uc = new UcApi();
        $this->authPre = !empty(C('AuthPre')) ? C('AuthPre') : 'Example_';
    }

    /**
     * 检查用户名
     * @param $username
     * @return mixed
     * 1:成功
     * -1:用户名不合法
     * -2:包含要允许注册的词语
     * -3:用户名已经存在
     */
    public function uc_check_name($username)
    {
        return $this->uc->uc_user_checkname($username);
    }

    /**
     * 检查邮箱地址
     * @param $email
     * @return mixed
     * 1:成功
     * -4:Email 格式有误
     * -5:Email 不允许注册
     * -6:该 Email 已经被注册
     */
    public function uc_check_email($email)
    {
        return $this->uc->uc_user_checkemail($email);
    }

    /**
     * 用户登录
     * @param string|int $username 用户名/用户ID/用户EMAIL
     * @param string $password 密码
     * @param int $is_uid 是否使用用户 ID登录
     * 1:使用用户 ID登录
     * 2:使用用户 E-mail登录
     * 0:(默认值) 使用用户名登录
     * @return array|bool
     *
     */
    public function uc_login($username, $password, $is_uid = 0)
    {
        list($uid, $username, $password, $email) = $this->uc->uc_user_login($username, $password, $is_uid);//根据用户名检查用户是否存在
        if ($uid > 0) {
            return $this->uc->uc_user_synlogin($uid);//同步登陆；一定要输出这段代码
        }
        return false;
    }

    /**
     * 用户注册
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 电子邮件
     * @param string $question_id 安全提问索引
     * @param string $answer 安全提问答案
     * @param string $reg_ip 注册ip
     * @return bool|mixed
     * 大于 0:返回用户 ID，表示用户注册成功
     * -1:用户名不合法
     * -2:包含不允许注册的词语
     * -3:用户名已经存在
     * -4:Email 格式有误
     * -5:Email 不允许注册
     * -6:该 Email 已经被注册
     */
    public function uc_register($username, $password, $email, $question_id = '', $answer = '', $reg_ip = '')
    {
        $ip = !empty($reg_ip) ? $reg_ip : get_client_ip();
        return $this->uc->uc_user_register($username, $password, $email, $question_id, $answer, $ip);
    }

    /**
     * 修改资料
     * @param string $username 用户名
     * @param string $old_password 旧密码
     * @param string $new_password 新密码
     * @param string $email 邮箱,可以为空
     * @param int $ignore_old_pw 忽略密码检验
     * 1:忽略，更改资料不需要验证密码
     * 0:(默认值) 不忽略，更改资料需要验证密码
     * @return mixed
     * 1:更新成功
     * 0:没有做任何修改
     * -1:旧密码不正确
     * -4:Email 格式有误
     * -5:Email 不允许注册
     * -6:该 Email 已经被注册
     * -7:没有做任何修改
     * -8:该用户受保护无权限更改
     */
    public function uc_edit($username, $old_password, $new_password, $email = '', $ignore_old_pw = 1)
    {
        return $this->uc->uc_user_edit($username, $old_password, $new_password, $email, $ignore_old_pw);
    }

    /**
     * 获取用户信息
     * @param string $username 用户名
     * @param bool|int $is_uid 是否使用用户 ID获取
     * 1:使用用户 ID获取
     * 0:(默认值) 使用用户名获取
     * @return mixed
     */
    public function uc_get_user($username, $is_uid = 0)
    {
        return $this->uc->uc_get_user($username, $is_uid);
    }

    /**
     * 删除用户
     * @param int|array $uid 用户名
     * @return mixed 1:成功 0:失败
     */
    public function uc_delete($uid)
    {
        return $this->uc->uc_user_delete($uid);
    }

    /**
     * 用户退出
     * @return string
     */
    public function uc_logout()
    {
        return $this->uc->uc_user_synlogout();//同步退出，一定要输出这段代码
    }

    /**
     * 获取用户信息
     * @return array|bool
     */
    public function uc_user_cookie()
    {
        if (!empty(cookie($this->authPre . 'auth'))) {
            list($uid, $username) = explode("\t", $this->uc->uc_authcode(cookie($this->authPre . 'auth'), 'DECODE'));
            return array(
                'uid' => $uid,
                'username' => $username,
            );
        } else {
            return false;
        }
    }

    /**
     * 判断是否已经登录,成功返回用户信息
     * @return array|bool
     */
    public function uc_is_login()
    {
        return $this->uc_user_cookie();
    }

    /**
     * 获取用户id
     * @return string
     */
    public function uc_get_uid()
    {
        $uc_user = $this->uc_user_cookie();
        if (empty($uc_user['uid'])) {
            return $uc_user;
        }
        return $uc_user['uid'];
    }

    /**
     * 获取用户名
     * @return string
     */
    public function uc_get_username()
    {
        $uc_user = $this->uc_user_cookie();
        if (empty($uc_user['username'])) {
            return $uc_user;
        }
        return $uc_user['username'];
    }

}