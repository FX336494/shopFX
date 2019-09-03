<template>
      <div class="loading" v-if="ifload">
          <img :src="loadImg" />
          <!-- <p class="desc">{{title}}</p> -->
      </div>
</template>
<script type="text/ecmascript-6">
  export default {
    props: {
      title: {
        type: String,
        default:"加载中..."
      },
      ifload:{
        default:false
      }
    },
    data() {
      return {
        loadImg:require('@/assets/load.gif'),
      }
    }
  }
</script>
<style scoped>

  .loading{
    position: fixed;
    top: 0px;
    bottom: 50px;
    left: 200px;
    text-align: center;
    background: rgba(0,0,0,0);
    width: 100%;
    margin: 0 auto;
    padding:40% 0px;
    color: #000;
    z-index: 99999999999;
  }
  .loading img{
    position: absolute;
    top: 24%;
    left: 25%;
    width: 48px;
    z-index: 99999999999;
  }

  .desc{
      line-height: 20px;
      font-size: 12px;
      color:#000;
      letter-spacing: 4px;
      font-weight: bold;
  }
      
</style>
