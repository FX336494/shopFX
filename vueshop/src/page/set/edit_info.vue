<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="form">
        <div class="item">
          <span>姓名：</span>
          <div class="input_item">
            <input class="input" type="text" v-model="member_name" placeholder="请输入姓名" />
          </div>
        </div>
        <div class="submit_btn btn" @click="submit_">
          确认修改
        </div>

      </div>
      <fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :clsType="clsCode" :url="url">
      </fadeAlert>
    </div>
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
        headerName: '修改资料',
        msg: '',
        showAlert: false,
        clsCode: 3,
        url: '',
        member_name: '',
      }
    },
    created() {
      this.$nextTick(() => {
        this.get_member_info();
      })
    },
    methods: {
      get_member_info() {
        this.$post_('member/member/info', {}, (res) => {
          console.log(res);
          this.member_name = res.data.member_name;
        });
      },
      submit_() {
        if (!this.member_name) {
          this.msg = '信息不完整';
          this.showAlert = true;
          return false;
        }
        let params = {
          member_name: this.member_name,
        };
        this.$post_('member/member/edit_info', params, (res) => {
          this.showAlert = true;
          this.msg = res.msg;
          if (res.code == '0') {
            this.clsCode = 2;
            this.url = '/page/member/index';
            return true;
          }
        })

      }
    }
  }
</script>

<style type="text/css" scoped>
  .content,
  .form {
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
    width: 20%;
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
    width: 80%;
    font-size: 14px;
  }

  .form .item .input_item .input {
    width: 100%;
    font-size: 16px;
    line-height: 35px;
    height: 35px;
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

  .submit_btn {
    background: #FF3306;
  }
</style>
