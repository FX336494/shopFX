<template>
  <div class="recommend" ref="recommend">
    <topTitle :name="headerName" :showBack="true"></topTitle>
    <div class="list">
      <router-link tag='div' to="/page/set/edit_pass">
        <div class="item">
          <i class="iconfont icon-xiugaimima"></i>
          <div class="item_text">修改登录密码</div>
        </div>
      </router-link>
      <router-link tag='div' to="/page/set/edit_info">
        <div class="item">
          <i class="iconfont icon-xiugai-copy"></i>
          <div class="item_text">修改资料</div>
        </div>
      </router-link>

      <router-link tag='div' to="/page/set/notice">
        <div class="item">
          <i class="iconfont icon-gonggao"></i>
          <div class="item_text">公告</div>
        </div>
      </router-link>

      <div class="item" @click="reg_out">
        <i class="iconfont icon-tuichu"></i>
        <div class="item_text">退出</div>
      </div>
    </div>

    <alert @callback_="callback_" :content="content" :ifshow="if_show"></alert>
  </div>
</template>
<script>
  import topTitle from '@/components/common/top_title'
  import Alert from '@/components/common/alert'
  export default {
    components: {
      topTitle,
      Alert,
    },
    data() {
      return {
        headerName: "设置",
        content: '',
        if_show: false,
        ocontent: '',
        oifshow: false,
        serviceInfo: []
      }
    },
    created() {

    },
    methods: {

      reg_out() {
        this.if_show = true
        this.content = "是否退出登录？"
      },

      callback_(id) {
        if (id == 0) {
          this.if_show = false
          return
        } else if (id == 1) {
          this.if_show = false
          sessionStorage.clear();
          sessionStorage.removeItem('auth_key');
          this.$post_('member/connect/logout', {}, (res) => {
            console.log(res);
            this.$router.replace({
              path: '/page/member/login'
            });
          });
        }
      }
    }
  }
</script>

<style scoped>
  .iconfont {
    font-size: 28px;
    color: #FF3306;
  }

  a {
    color: #000;
  }

  .item:active {
    background: #eee;
  }

  .item_text {
    padding-left: 30px;
    font-size: 15px;
    display: inline-block;
    line-height: 50px;
  }

  .item {
    border-bottom: 1px solid #ccc;
    text-align: left;
    padding-left: 20px;
  }

  .item img {
    width: 25px;
  }

  .recommend {
    position: fixed;
    width: 100%;
    top: 0;
    bottom: 0px;
    background: #f0f0f0;
    z-index: 999;
  }

  .recommend-content {
    height: 100%;
    overflow: hidden;
  }

  .clear {
    clear: both;
  }

  .list {
    background: #fff;
  }

  .service {
    width: 80%;

    background: #FFF;
    padding: 10px 0px 20px 0px;
    margin: 30px auto;
    border-radius: 15px;
  }

  .service .title {
    font-size: 16px;
    /*line-height: 40px;*/
    font-weight: 600;
  }

  .service .row {
    margin-top: 15px;
  }
</style>
