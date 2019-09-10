<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="item">
        初始密码：
        <input type="text" placeholder="请输原密码" v-model="oldPass" style="width:70%">
      </div>
      <div class="item">
        输入密码：
        <input type="password" placeholder="请输入6-20位密码" v-model="password1" style="width:70%">
      </div>
      <div class="item">
        确认密码：
        <input type="password" placeholder="再次输入6-20位密码" v-model="password2" style="width:70%">
      </div>
      <div class="submit_btn" @click="submit_">
        确认修改
      </div>
    </div>
    <fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :clsType="clsCode" :url="tourl"></fadeAlert>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import fadeAlert from '@/components/common/fadealert'
  export default {
    components: {
      topTile,
      fadeAlert
    },
    data() {
      return {
        headerName: '修改密码',
        oldPass: '',
        password1: '',
        password2: '',
        msg: '提示信息',
        showAlert: false,
        clsCode: 1,
        tourl: ''
      }
    },
    created() {

    },
    methods: {
      submit_() {
        if (!this.oldPass || !this.password1 || !this.password2) {
          this.showAlert = true;
          this.clsCode = 3;
          this.msg = '请输入完整信息';
          return false;
        }

        if (this.password1 != this.password2) {
          this.showAlert = true;
          this.clsCode = 3;
          this.msg = '两次密码输入不一致';
          return false;
        }

        let params = {
          old_pass: this.oldPass,
          password1: this.password1,
          password2: this.password2
        };
        this.$post_('member/member/edit_pass', params, (res) => {
          console.log(res);
          this.showAlert = true;
          if (res.code == '0') {
            this.clsCode = 2;
            this.msg = '修改成功';
            this.tourl = '/page/member/index';
          } else {
            this.clsCode = 3;
            this.msg = res.msg;
          }
        })
      },
    }
  }
</script>

<style type="text/css" scoped>
  .wrapper{
    position:absolute;
    top:0px;
    bottom:0px;
    left:0px;
    width:100%;
    overflow:auto;
    background:#f0f0f0;
  }
  .item {
    position: relative;
    line-height: 50px;
    border-bottom: 1px solid #ccc;
    padding-left: 15px;
    background: #fff;
  }

  .item input {
    padding-left: 15px;
    font-size: 14px;
    width: 80%;
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
    background: #FF3300;
  }
</style>
