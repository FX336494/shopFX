<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<div class="content">
      <div class="list">

        <div class="item" v-for="(item,i) in list" :key="i">
          <div class="img">
            <img :src="item.image" alt="">
          </div>
          <div class="more" @click="morePintuan(item)">更多惊喜>></div>
          <div class="goods_list">
            <div class="goods" @click="toproduct(goods.goods_commonid)" v-for="(goods,index) in item.goods" :key="index">
              <div class="img">
                <img v-lazy="goods.goods_image" />
              </div>
              <div class="goods_info">
                <div class="goods_name">{{goods.goods_name}}</div>
                <div class="goods_price">拼团价：￥{{goods.pintuan_price}}</div>
              </div>
            </div>
          </div>
        </div>

        <div v-show="!list.length">
          <i class="iconfont icon-meiyoudingdan-01" style="font-size: 32px;"></i>
          <div>敬请期待</div>
        </div>

      </div>
    </div>

		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import topTile from '@/components/common/top_title'
	import loading from '@/components/common/loading'
	export default {
		components: {
			topTile,
			loading,
		},
		data() {
			return {
				headerName: '拼团专区',
				ifload: false,
        list:[],
			}
		},
    created() {
      this.$nextTick(()=>{
        this.getData();
      })
    },
    methods:{
      getData(){
        this.$post_('goods/promotion/get_pintaun_instance',{},(res)=>{
          console.log(res);
          this.list = res.data;
        })
      },
      toproduct(id){
        this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:id}})
      },
      morePintuan(item) {
        let insId = item.instance_id;
        let image = item.image;
        this.$router.push({path:'/page/goods/promotion/more_pintuan',query:{instance_id:insId,image:image}});
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
