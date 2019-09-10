<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="list">

        <router-link tag='div' class="item" :to="{path:'/page/wallet/integral'}">
          <i class="iconfont icon-money"></i>
          <span>我的积分</span>
          <i class="iconfont icon-right"></i>
        </router-link>
        
        <router-link tag='div' class="item" :to="{path:'/page/wallet/integral'}">
          <i class="iconfont icon-money"></i>
          <span>我的积分</span>
          <i class="iconfont icon-right"></i>
        </router-link>

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
        headerName:'我的钱包',
        ifload:false,
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
  .content .list{
    width: 100%;
  }
  .content .list .item{
    width: 100%;
    background: #fff;
    position: relative;
    line-height: 45px;
    height: 45px;
    text-align: left;
    text-indent: 10px;
    margin-bottom: 1px;
  }
  .content .list .item .iconfont{
    font-size: 24px;
    vertical-align: middle;
  }
  .content .list .item span{
    display: inline-block;
    padding: 0px 3px;
    font-size: 14px;
  }
  .content .list .item .icon-right{
    position: absolute;
    right: 8px;
    top: 0px;
  }


</style>
