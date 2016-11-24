<?php
/**
 * Created by PhpStorm.
 * User: d8q8
 * Date: 2016/11/21
 * Time: 16:37
 */

namespace Home\Controller;

/**
 * 登录控制器
 * Class LoginController
 * @package Home\Controller
 */
class LoginController extends UcController
{
    /**
     * 用户首页
     */
    public function index(){
        dump(cookie());
        dump(parent::uc_user_cookie());
        $this->assign('uc_username',parent::uc_get_username());
        $this->display();
    }

    /**
     * 用户登录
     */
    public function sign_on(){
        if(IS_POST){
            $post = I('post.');
            $result = parent::uc_login($post['username'],$post['password']);
            if(!empty($result)){
                echo $result;//输出同步登录,不能去掉
                $this->success('登录成功',U('login/index'));
            }
            else{
                $this->error('登录失败,请联系管理员');
            }
        }
        else{
            $this->display();
        }
    }

    /**
     * 用户退出
     */
    public function sign_out(){
        $result = parent::uc_logout();
        if (!empty($result)){
            echo $result;//输出同步退出,不能去掉
            $this->success('安全退出',U('login/sign_on'));
        }
    }
}