<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>登录{{strpos(url()->previous(),'admin') === false ? '前台' : '后台'}}</title>
    <link rel="stylesheet" type="text/css" href="/css/login.css"/>
</head>
<body>

<div class="mainfather">
    <div class="logo">
        <span>管理中心</span>
    </div>
    <div class="main">
        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <div class="login">

                <div class="center">
                    <div class="numberone">
                        <div class="number">
                            <div class="num">
                                <img src="/images/backren.png"/>
                            </div>
                            <div class="num_input">
                                <div class="num_input">
                                    <input id="account" type="text"
                                           placeholder="请输入账号"
                                           class="login_num"
                                           name="account"
                                           value="{{ old('account') }}">
                                </div>
                            </div>
                        </div>
                        <div class="number">
                            <div class="num">
                                <img src="/images/backsuo.png"/>
                            </div>
                            <div class="num_input">
                                <div class="num_input">
                                    <input class="login_num"
                                           id="password"
                                           type="password"
                                           placeholder="密码"
                                           name="password">
                                </div>
                            </div>
                        </div>

                        @if(request()->session()->get('has_been_disabled','no') === 'yes')
                            <p class="reminder">此账号已经被禁用</p>
                        @else
                            @if($errors->has('account'))
                                <p class="reminder">{{ $errors->first('account') }}</p>
                            @elseif($errors->has('password'))
                                <p class="reminder">{{ $errors->first('password') }}</p>
                            @endif
                        @endif

                        <button class="denglu">
                            登录
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="fuceng"></div>
</body>
</html>