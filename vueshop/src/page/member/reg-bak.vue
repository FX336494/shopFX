<template>
  <div class="recommend" ref="recommend">
    <topTitle :name="headerName"></topTitle>
    <div class="set_password">
      推荐人：
      <input type="text" placeholder="请输入推荐人手机号/ID" @blur="checkmobile" v-model="member_id">
      <div style="position:absolute;right:10px;top:0" v-show="rec_member">{{rec_member}}</div>
    </div>
    <div class="set_password">
      昵称：
      <input type="text" placeholder="请输入昵称" v-model="nickname">
    </div>
    <div class="set_password">
      手机s：
      <input type="text" placeholder="请输入手机号" v-model="mobile">
    </div>
    <div class="put_YZM">
      <input type="text" placeholder="验证码" v-model="vcode_num">
      <div class="vcode">
        <vcode :mobile="mobile"></vcode>
      </div>

    </div>
    <div class="set_password">
      登陆密码：
      <input type="password" placeholder="请输入6-20位登陆密码" v-model="passWord" style="width:70%">
    </div>
    <div class="set_password">
      支付密码：
      <input type="password" placeholder="再次输入6位支付密码" maxlength="6" v-model="passWord_pay" style="width:70%">
    </div>
    <div class="agree_">
      <div class="agree_img">
        <img :src="ifagree" @click="agree()">
      </div>
      <div class="agree_text">
        同意<span @click="readLicense" style="color:#387ef5;text-decoration:underline">《用户注册许可服务》</span>
      </div>
    </div>

    <div class="submit_btn btn" @click="submit_">
      注册
    </div>
    <agreelicense @backToreg="back_toreg" :ifshow="showlicense"></agreelicense>
    <Onlyalert @Onlycallback_="Onlycallback_" :content="Onlycontent" :ifshow="Onlyif_show"></Onlyalert>
  </div>
</template>
<script>
  import {
    post_
  } from '@/components/js/common'
  import topTitle from '@/components/common/top_title'
  import Onlyalert from '@/components/common/Only_alert'
  import Agreelicense from '@/components/common/agree_license'
  import vcode from '@/components/common/vcode'
  export default {
    components: {
      topTitle,
      Onlyalert,
      Agreelicense,
      vcode
    },
    data() {
      return {
        headerName: "帐号注册",
        content: '',
        mobile: '',
        vcode_num: '',
        Onlycontent: '',
        Onlyif_show: false,
        username: '',
        passWord: '',
        passWord_pay: '',
        ifagree: require("../../assets/images/disagree.png"),
        is_agree: false,
        showlicense: false,
        rec_member: '',
        member_id: '',
        nickname: ''
      }
    },
    created() {
      this.member_id = this.$route.query.member_id
      if (this.member_id) {
        this.checkmobile()
      }
      this.init_data()
    },
    activated() {
      this.init_data()
    },
    watch: {
      member_id() {
        if (!this.member_id) {
          this.rec_member = ''
        }
      }
    },
    methods: {
      init_data() {

      },

      agree() {
        this.is_agree = !this.is_agree
        if (this.is_agree) {
          this.ifagree = require("../../assets/images/agree.png")
        } else {
          this.ifagree = require("../../assets/images/disagree.png")
        }
      },
      checkmobile() {
        if (!this.member_id) {
          return
        }
        var data = {
          member: this.member_id
        }
        this.$post_('common/getmember', data, (res) => {
          console.log(res)
          if (res.code == 0) {
            if (this.member_id.length == 11) {
              this.rec_member = '用户ID:' + res.data.id
              this.rec_id = res.data.id
            } else {
              this.rec_member = '用户手机:' + res.data.member_mobile
              this.rec_id = res.data.id
            }

          } else {
            this.rec_member = res.msg
            this.rec_id = '';
          }
        })
      },
      submit_() {
        if (this.passWord.length < 6 || this.passWord.length > 20) {
          this.Onlyif_show = true
          this.Onlyalert_id = 0
          this.Onlycontent = '请输入6-20位登陆密码'
          return
        }
        if (!this.passWord) {
          this.Onlyif_show = true
          this.Onlyalert_id = 0
          this.Onlycontent = '请设置登陆密码'
          return
        }
        if (this.passWord_pay.length != 6) {
          this.Onlyif_show = true
          this.Onlyalert_id = 0
          this.Onlycontent = '请输入6位支付密码'
          return
        }
        if (!this.passWord_pay) {
          this.Onlyif_show = true
          this.Onlyalert_id = 0
          this.Onlycontent = '请设置支付密码'
          return
        }
        if (this.is_agree == false) {
          this.Onlyif_show = true
          this.Onlyalert_id = 0
          this.Onlycontent = '请完善信息'
          return
        }
        var data = {
          member_mobile: this.mobile,
          member_name: this.nickname,
          loginpwd: this.passWord,
          paypwd: this.passWord_pay,
          captcha: this.vcode_num,
          invite_id: this.rec_id
        };
        this.$post_('member/connect/reg', data, (res) => {
          console.log(res); //return;
          if (res.code == 0) {
            this.Onlyif_show = true
            this.Onlyalert_id = 1
            this.Onlycontent = '注册成功，请登录'
          } else {
            this.Onlyif_show = true
            this.Onlyalert_id = 0
            this.Onlycontent = res.msg
          }
        });
      },
      Onlycallback_() {
        this.Onlycontent = ''
        if (this.Onlyalert_id == 1) {
          this.Onlyif_show = false
          this.$router.replace({
            path: '/page/member/login'
          });
        } else {
          this.Onlyif_show = false
          return
        }

      },
      back_toreg() {
        this.showlicense = false
      },
      readLicense() {
        this.showlicense = true
      },
    }
  }
</script>

<style scoped>
  .agree_ {
    background: #fff;
    line-height: 50px;
    padding-left: 15px;
  }

  .agree_img {
    display: inline-block;
    width: 25px;
  }

  .agree_img img {
    width: 100%;
  }

  .agree_text {
    display: inline-block;
    padding-left: 15px;
  }

  .put_YZM {
    display: flex;
    text-align: center;
    line-height: 50px;
    height: 50px;
    background: #fff;
    padding-left: 15px;
    border-bottom: 1px solid #ccc;
  }

  .put_YZM input {
    flex: 5;
    background: #eee;
    height: 40px;
    margin-top: 5px;
    padding-left: 15px;
  }

  .put_YZM .vcode {
    flex: 3;
  }

  .forgot_ {
    margin-top: 20px;
    text-align: right;
    width: 50%;
    margin-left: 40%;
    color: blue;
  }

  .submit_btn {
    margin-top: 20px;
    width: 80%;
    margin-left: 10%;
    line-height: 40px;
    border-radius: 8px;
    text-align: center;
    color: #fff;
    font-size: 18px;
  }

  .submit_btn:active {
    background: red;
  }

  .set_password {
    position: relative;
    line-height: 50px;
    border-bottom: 1px solid #ccc;
    padding-left: 15px;
    background: #fff;
  }

  .set_password input {
    padding-left: 15px;
    font-size: 14px;
    width: 80%;
  }

  .recommend {
    position: fixed;
    width: 100%;
    top: 0;
    bottom: 0px;
    background: #eee;
    z-index: 999;
    text-align: left;
  }

  .clear {
    clear: both;
  }
</style>
