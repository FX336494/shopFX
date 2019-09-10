<template>
      <div class="loading" v-if="ifload">
					<!-- <img :src="loadImg" /> -->
					<div class="layer">
						<img :src="loadImg" />
					</div>
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
    left: 0px;
    text-align: center;
    background: rgba(0,0,0,0);
    width: 100%;
    margin: 0 auto;
    padding:40% 0px;
    color: #000;
    z-index: 99999999999;
  }

	
  .loading .layer{
    position: absolute;
    top: 24%;
    left: 40%;
		background: rgba(0,0,0,0.1);
		padding: 10px 20px;
    z-index: 99999999999;
		border-radius: 4px;
  }
	.loading .layer img{
		width: 40px;
	}
  .desc{
      line-height: 20px;
      font-size: 12px;
      color:#000;
      letter-spacing: 4px;
      font-weight: bold;
  }
      
</style>
