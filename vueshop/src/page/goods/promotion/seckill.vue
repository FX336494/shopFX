<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<div class="content">
      <div class="tab">
        <div class="item" :class="tabType==1?'active':''" @click="switchTab(1)">
          即将开始
        </div>
        <div class="item" :class="tabType==2?'active':''" @click="switchTab(2)">
          抢购中
        </div>
        <div class="item" :class="tabType==3?'active':''" @click="switchTab(3)">
          已结束
        </div>
      </div>
      <div class="list">
        <div class="item" v-for="(item,i) in list" :key="i">
          <div class="img">
            <img :src="item.image" alt="">
          </div>
          <div class="more" v-if="tabType==1">
            <span>
              <countdown :endTime="item.start_time*1000"></countdown>
            </span>
            <span @click="moreSeckill(item)">更多秒杀>></span>
          </div>
          <div class="more" v-if="tabType==2">
            <span>
              <countdown :endTime="item.end_time*1000"></countdown>
            </span>
            <span @click="moreSeckill(item)">更多秒杀>></span>
          </div>

          <div class="goods_list">
            <div class="goods" @click="toproduct(goods.goods_commonid)" v-for="(goods,index) in item.goods" :key="index">
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

      </div>
    </div>

		<loading :ifload="ifload"></loading>
    <fadeAlert
      :msg="msg"
      v-if="showAlert"
      @hideFade="showAlert=false">
    </fadeAlert>
	</div>
</template>

<script type="text/javascript">
	import topTile from '@/components/common/top_title'
	import loading from '@/components/common/loading'
  import countdown from '@/components/common/countdown'
  import fadeAlert from '@/components/common/fadealert'
	export default {
		components: {
			topTile,
			loading,
      countdown,
      fadeAlert
		},
		data() {
			return {
				headerName: '秒杀专区',
				ifload: false,
        list:[],
        tabType:2,
        msg: '',
        showAlert: false,
			}
		},
    created() {
      this.$nextTick(()=>{
        this.getData();
      })
    },
    methods:{
      getData(){
        this.ifload = true;
        let param = {type:this.tabType};
        this.$post_('goods/promotion/get_seckill_instance',param,(res)=>{
          this.ifload = false;
          this.list = res.data;
        })
      },
      toproduct(id){
        if(this.tabType==3){
          this.msg = '活动已结束';
          this.showAlert = true;
          return;
        }
        this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:id}})
      },
      switchTab(type){
        this.tabType = type;
        this.getData();
      },
      moreSeckill(item) {
        let insId = item.instance_id;
        let image = item.image;
        this.$router.push({path:'/page/goods/promotion/more_seckill',query:{instance_id:insId,image:image}});
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
  .tab{
    display: flex;
    width: 100%;
    margin-bottom: 10px;
  }
  .tab .item{
    flex: 1;
    background: #fff;
    color:#000;
    font-size: 14px;
    line-height: 40px;
    height: 40px;
  }
  .tab .active{
    background: orange;
    color:#fff;
    font-weight: bold;
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
    width: 38%;
  }
  .list .item .more span:first-child{
    text-align: left;
    text-indent: 20px;
    width: 60%;
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
		height: 65px;
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
