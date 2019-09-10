<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="icon">
        <i class="iconfont icon-qianbao"></i>
      </div>
      <div class="integral">
        <h4>我的{{data.type_name}}</h4>
        <div class="balance">￥<b>{{data.balance}}</b></div>
      </div>
      <div class="op">
        <router-link tag='span' to="/page/wallet/integral_list">{{data.type_name}}明细</router-link>
      </div>
    </div>

    <loading :ifload="ifload"></loading>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import loading from '@/components/common/loading'
  export default{
  	components: {topTile,scroll,loading},
    data() {
      return {
        headerName:'',
        ifload:false,
        data:[],
      }
    },
    created() {
      this.$nextTick(()=>{
        this.getData();
      })
    },
    methods: {
      getData() {
        this.ifload = true;
        this.$post_('member/balance/get_balance',{},(res)=>{
          console.log(res);
          this.data = res.data;
          this.ifload = false;
        });
      }
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
  .wrapper .content{
    width: 100%;
  }
  .content .icon{
    line-height: 70px;
  }
  .content .icon .iconfont{
    font-size: 50px;
    color:orange;
  }
  .content .integral h4{
    font-size: 16px;
    letter-spacing: 2px;
  }
  .content .integral .balance{
    font-size: 18px;
    font-weight: bold;
  }
  .content .integral .balance b{
    font-size: 40px;
  }
  .content .op{
    margin-top:60px;
  }
  .content .op span{
    background: orangered;
    display: inline-block;
    width: 200px;
    height: 40px;
    line-height: 40px;
    color:#fff;
    letter-spacing: 2px;
    font-size: 15px;
    border-radius: 4px;
  }

</style>
