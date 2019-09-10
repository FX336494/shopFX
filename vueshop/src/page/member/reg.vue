<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="form">

        <div class="item">
          <span>电话号码：</span>
          <div class="input_item">
            <input class="input" type="text" v-model="form.member_mobile" placeholder="输入电话号码" />
          </div>
        </div>

        <div class="item">
          <span>昵称：</span>
          <div class="input_item">
            <input class="input" type="text" v-model="form.member_name" placeholder="输入昵称" />
          </div>
        </div>

        <div class="item">
          <span>验证码：</span>
          <div class="input_item i-vcode">
            <input class="input" type="text" v-model="form.captcha" placeholder="验证码,测试随填" />
            <div class="vcode">
              <vcode :mobile="form.member_mobile"></vcode>
            </div>
          </div>
        </div>

        <div class="item">
          <span>登录密码：</span>
          <div class="input_item">
            <input class="input" type="text" v-model="form.loginpwd" placeholder="6-20位数字加字母密码" />
          </div>
        </div>

        <div class="item">
          <span>支付密码：</span>
          <div class="input_item">
            <input class="input" type="text" v-model="form.paypwd" placeholder="6-20位数字加字母密码" />
          </div>
        </div>
      </div>

      <div class="submit_btn btn" @click="reg">
        注册
      </div>
      <div class="op">
        <router-link tag='span' class='reg' to="/page/member/login">
          已有账号，直接登录
        </router-link>
      </div>

    </div>

    <loading :ifload="ifload"></loading>
    <fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :url="url">
    </fadeAlert>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import loading from '@/components/common/loading'
  import vcode from '@/components/common/vcode'
  import fadeAlert from '@/components/common/fadealert'
  export default {
    components: {
      topTile,
      loading,
      vcode,
      fadeAlert
    },
    data() {
      return {
        headerName: '会员注册',
        ifload: false,
        msg: '',
        showAlert: false,
        url: '',

        form: {
          member_mobile: '',
          member_name: '',
          captcha: '',
          loginpwd: '',
          paypwd: '',
        }
      }
    },
    methods: {
      reg() {
        if(!this.validata()) return;
        this.$post_('member/connect/reg', this.form, (res) => {
          console.log(res);
          this.showAlert = true
          if (res.code == 0) {
            this.msg = '注册成功，请登录';
            this.url = '/page/member/login';
          } else {
            this.msg = res.msg
          }
        });
      },
      validata() {
        if(!this.form.member_mobile){
          this.showAlert = true;
          this.msg = '请填写手机号';
          return false;
        }
        let telReg = /^1[3456789]\d{9}$/;
        if(!telReg.test(this.form.member_mobile)){
          this.showAlert = true;
          this.msg = '请输入正确的手机格式';
          return false;
        }
        let passReg = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/;
        if(!passReg.test(this.form.loginpwd) || !passReg.test(this.form.paypwd)){
          this.showAlert = true
          this.msg = '请输入6-20位的密码数字加字母'
          return false;
        }

        if(!this.form.captcha){
          this.showAlert = true;
          this.msg = '请输入验证码';
          return false;
        }
        return true;
      }
    }
  }
</script>

<style type="text/css" scoped>
  .wrapper {
    position: absolute;
    top: 0px;
    bottom: 0px;
    left: 0px;
    width: 100%;
    overflow: auto;
    background: #f0f0f0;
  }

  .wrapper .content {
    width: 100%;
  }

  .form {
    background: #fff;
  }

  .form .item {
    width: 100%;
    font-size: 0px;
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
  }

  .form .intro_item {
    border-bottom: none;
  }

  .form .item span {
    display: inline-block;
    width: 30%;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
  }

  .form .intro_item span {
    width: 100%;
    text-align: left;
    text-indent: 4px;
  }

  .form .item .input_item {
    display: inline-block;
    width: 70%;
    font-size: 14px;
  }

  .form .item .input_item .input {
    width: 100%;
    font-size: 16px;
    line-height: 35px;
    height: 35px;
  }

  .form .item .i-vcode {
    font-size: 0px;
  }

  .form .item .i-vcode .input {
    width: 50%;
    font-size: 14px;
  }

  .form .item .i-vcode .vcode {
    width: 50%;
    display: inline-block;
    font-size: 14px;
    text-align: center;
  }

  .submit_btn {
    margin-top: 60px;
    width: 60%;
    line-height: 40px;
    border-radius: 8px;
    text-align: center;
    color: #fff;
    font-size: 18px;
  }

  .submit_btn {
    background: #FF3306;
  }
  .op{
    margin-top: 20px;
  }  
</style>
