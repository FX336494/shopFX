<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<div class="content">
      <scroll ref="scroll" @scrollEnd="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
        <div class="list">
          <div class="item">
            <div class="img">
              <img :src="image" alt="">
            </div>
            <div class="goods_list">
              <div class="goods" @click="toproduct(goods.goods_commonid)" v-for="(goods,index) in list" :key="index">
                <div class="img">
                  <img v-lazy="goods.goods_image" />
                </div>
                <div class="goods_info">
                  <div class="goods_name">{{goods.goods_name}}</div>
                  <div class="goods_price">秒杀价：￥{{goods.seckill_price}}</div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="!list.length">
            <i class="iconfont icon-meiyoudingdan-01" style="font-size: 32px;"></i>
            <div>暂无数据</div>
          </div>

          <div class="more" v-if="isMore">下拉加载显示更多...</div>
          <div class="more" v-if="!isMore">我也是有底线的...</div>
        </div>
      </scroll>
    </div>

		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import topTile from '@/components/common/top_title'
	import loading from '@/components/common/loading'
  import countdown from '@/components/common/countdown'
  import scroll from '@/components/common/scroll'
	export default {
		components: {
			topTile,
			loading,
      countdown,
      scroll
		},
		data() {
			return {
				headerName: '更多秒杀',
        BSstyle:"position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
				ifload: false,
        page:1,
        list:[],
        insId:0,
        image:'',
        isMore:true,
			}
		},
    created() {
      this.insId = this.$route.query.instance_id;
      this.image = this.$route.query.image;
      this.$nextTick(()=>{
        this.getData();
      })
    },
    methods:{
      getData(){
        this.ifload = true;
        let param = {instance_id:this.insId,page:this.page};
        this.$post_('goods/promotion/seckill_instance_goods',param,(res)=>{
          this.ifload = false;
          let data = res.data;
          if(data.length>0){
            data.forEach((val)=>{
              this.list.push(val);
            })
            this.page++;
          }else{
            this.isMore = false;
          }
        })
      },
      toproduct(id){
        this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:id}})
      },
      scroll(pos,maxScrollY) {
        if(maxScrollY==pos){
          this.getData()
        }
      },
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
  .tab{
    display: flex;
    width: 100%;
    margin-bottom: 10px;
  }

  .list{
    width: 100%;
  }

  .list .item{
    display: block;
    width: 100%;
    margin-bottom: 30px;
  }
  .list .item img{
    width: 100%;
  }
  .list .item .more{
    margin-top: 10px;
    background: #fff;
    color: #555;
    line-height: 35px;
    text-align: right;
    padding-right: 5px;
    margin-bottom: 1px;
  }
  .list .item .more span{
    display: inline-block;
    width: 48%;
  }
  .list .item .more span:first-child{
    text-align: left;
    text-indent: 20px;
  }
  .list .item .goods_list{
    width: 100%;
    font-size: 0px;
    text-align: left;

  }
  .list .item .goods_list .goods{
    display: inline-block;
    width:49%;
    background: #fff;
    padding-top: 5px;
    margin-bottom: 10px;
  }
  .list .item .goods_list div:nth-child(2n-1){
  	margin-right: 2%;
  }
  .list .item .goods_list .goods .img{
    width: 100%;
    text-align: center;
  }
  .list .item .goods_list .goods .img img{
    width: 90%;
    border-radius: 9px;
  }
	.list .item .goods_list .goods_info{
		height: 60px;
		padding:0px 5px;
    text-indent: 10px;
    background: #fff;
	}
	.list .item .goods_list .goods_info .goods_name{
		font-size: 12px;
    line-height: 20px;
    overflow:hidden;
    text-overflow:ellipsis;
    display:-webkit-box;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:2;
	}
	.list .item .goods_list .goods_info .goods_price{
		color:#FF0036;
    font-size: 15px;
	}


</style>
