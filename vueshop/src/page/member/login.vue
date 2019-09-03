<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="form">

        <div class="item">
          <span>电话号码：</span>
          <div class="input_item">
            <input class="input" type="text" v-model="form.mobile" placeholder="输入电话号码" />
          </div>
        </div>

        <div class="item">
          <span>密码：</span>
          <div class="input_item">
            <input class="input" type="password" v-model="form.password" placeholder="输入密码" />
          </div>
        </div>
      </div>

      <div class="submit_btn btn" @click="login">
        登录
      </div>
      <div class="op">
        <router-link tag='span' class='reg' to="/page/member/reg">
          免费注册
        </router-link>
      </div>

    </div>

    <loading :ifload="ifload"></loading>
    <fadeAlert
      :msg="msg"
      v-if="showAlert"
      @hideFade="showAlert=false"
      :url="url">
    </fadeAlert>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import loading from '@/components/common/loading'
  import fadeAlert from '@/components/common/fadealert'
  export default {
    components: {
      topTile,
      loading,
      fadeAlert
    },
    data() {
      return {
        headerName: '用户登录',
        ifload: false,
        msg: '',
        showAlert: false,
        url: '',

        form: {
          mobile: '',
          password: '',
        },
      }
    },
    methods: {
      login() {
        this.ifload = true;
        this.$post_('member/connect/login', this.form, (res) => {
          this.showAlert = true;
          if(res.code=='0'){
            sessionStorage.auth_key = res.data.auth_key
            this.msg = '登录成功';
            this.url = '/page/index';
          }else{
            this.msg = res.msg;
          }
          this.ifload = false;
        });
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
