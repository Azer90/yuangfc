<?php

namespace App\Admin\Controllers;

use App\Events\Event;
use App\WechatCode;
use App\WeChatUser;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\Hash;
use EasyWeChat;
class LoginAuthController extends Controller
{
    /**
     * @var string
     */
    protected $loginView = 'admin::login';

    /**
     * Show the login page.
     *
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function getLogin()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        return view($this->loginView);
    }

    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', 0);

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $where[$key]=$value;
            }
        }
         $admin_user=Administrator::where($where)->first();

         $check_pwd=Hash::check($credentials['password'], $admin_user->getAuthPassword());
        if($admin_user&&$check_pwd){
            $wecode_id= WechatCode::insertGetId([
                'uid' => $admin_user->id,
                'type' => 'qrcode',
                'openid' => '',
                'sceneid' => 0,
                'expire' => time() + 3600,
                'remember' => $remember,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s',time()),
                ]);
            $app=EasyWeChat::officialAccount();
            $response = $app->oauth->scopes(['snsapi_userinfo'])->redirect(route('wechat_check',['wecode_id'=>$wecode_id]));
            $url=$response->getTargetUrl();//获取重定向资源
            $shortUrl = $app->url->shorten($url);
            $shortUrl=$shortUrl['short_url'];

            return view($this->loginView)->with(compact('shortUrl','wecode_id'));
        }


        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            $this->username()   => 'required',
            'password'          => 'required',
        ]);
    }

    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(config('admin.route.prefix'));
    }

    /**
     * User setting page.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function getSetting(Content $content)
    {
        $form = $this->settingForm();
        $form->tools(
            function (Form\Tools $tools) {
                $tools->disableList();
            }
        );

        return $content
            ->header(trans('admin.user_setting'))
            ->body($form->edit(Admin::user()->id));
    }

    /**
     * Update user setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putSetting()
    {
        return $this->settingForm()->update(Admin::user()->id);
    }

    /**
     * Model-form for user setting.
     *
     * @return Form
     */
    protected function settingForm()
    {
        $class = config('admin.database.users_model');

        $form = new Form(new $class());

        $form->display('username', trans('admin.username'));
        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('confirmed|required');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->setAction(admin_base_path('auth/setting'));

        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        $form->saved(function () {
            admin_toastr(trans('admin.update_succeeded'));

            return redirect(admin_base_path('auth/setting'));
        });

        return $form;
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? trans('auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        admin_toastr(trans('admin.login_successful'));

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * 微信验证登录权限
     */
    public function wechat_check($wecode_id){
        $app = EasyWeChat::officialAccount();
        $user = $app->oauth->user();
        $original = $user->getOriginal();
        $info='账号授权成功';
        if(!empty($original)){
            $wechat_id=WeChatUser::where('openid',$original['openid'])->value('id');
            if(empty($wechat_id)){
                $wechat_id= WeChatUser::insertGetId([
                    'openid' => $original['openid'],
                    'nickname' => $original['nickname'],
                    'sex' => $original['sex'],
                    'language' => $original['language'],
                    'city' => $original['city'],
                    'province' => $original['province'],
                    'country' => $original['country'],
                    'headimgurl' => $original['headimgurl'],
                    'created_at' => date('Y-m-d H:i:s',time()),
                ]);
            }
            WechatCode::where('id',$wecode_id)->update([
                'status' => 1,
                'openid' => $original['openid'],
                'sceneid' => $wechat_id,
                'updated_at'=> date('Y-m-d H:i:s',time()),
            ]);
        }else{
            $info='账号授权错误';
        }

        return view('admin::wechatlogin')->with(compact('info'));
    }

    public function sweep_code_check(Request $request){
        $data=$request->all();

        $code_data=WechatCode::where('id',$data['wecode_id'])->first();
        if (time() > (int) $code_data['expire']) {
            return Api_error('二维码已过期',['mode'=>'is_expire']);
        }
        if ((int) $code_data['status'] === 0) {
            return Api_error('等待扫码',['mode'=>'wait']);
        }
        if ((int) $code_data['status'] === 1) {
            if (empty($code_data['sceneid'])) {
                return Api_error('等待扫码',['mode'=>'wait']);
            }
            $admin_uid=WeChatUser::where('id',$code_data['sceneid'])->value('admin_uid');
            if(empty($admin_uid)){

                return Api_error('你还没有绑定成为管理员,请联系管理员',['mode'=>'alert','url'=>route('admin.login')]);

            }
            if((int)$admin_uid!=(int)$code_data['uid']){

                return Api_error('授权错误,你不能授权该账户',['mode'=>'alert','url'=>route('admin.login')]);
            }else{
                $admin_user=Administrator::where('id',$code_data['uid'])->first();
                $this->guard()->login($admin_user, $code_data['remember']);
                admin_toastr(trans('admin.login_successful'));
                $request->session()->regenerate();
                return Api_success('成功',['mode'=>'success','url'=>route('admin.home')]);
            }
        }


    }
}
