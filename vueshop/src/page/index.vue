<template>
	<div class="wrapper">
		<router-link tag='div' to='/page/goods/search' class="search" v-show="showSearch">
			<i class="iconfont icon-sousuo"></i>
			<span>输入要搜索的产品</span>
		</router-link>
		<scroll ref="scroll" @scrollEnd="scroll_end" @scroll="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="content">

				<div class="slide_img">
					<carousel >
            <a tag='div' :href="item.link" class="swiper-slide" v-for="(item,index) in banner" :key="index">
              <img :src="item.img_url" />
            </a>
          </carousel>
				</div>

				<div class="class_nav">
					<router-link tag='div' :to='{path:"/page/goods/class_goods_list",query:{cate_id:menu.id,name:menu.category_name}}' class="item" v-for="(menu,index) in classMenu" :key="index">
						<img :src="menu.img" />
						<div class="text">{{menu.category_name}}</div>
					</router-link>
				</div>

        <!-- 促销活动 -->
        <div class="promotion" v-if="pintuanInfo">
            <router-link tag="div" class="pintuan" to="/page/goods/promotion/pintuan">
              <div class="img">
                <img :src="pintuanInfo.pintuan_image" />
                <!-- <div class="title">{{pintuanInfo.title}}</div> -->
              </div>
            </router-link>
        </div>
        <div class="promotion" v-if="seckillInfo">
            <router-link tag="div" class="pintuan" to="/page/goods/promotion/seckill">
              <div class="img">
                <img :src="seckillInfo.seckill_image" />
                <!-- <div class="title">{{pintuanInfo.title}}</div> -->
              </div>
            </router-link>
        </div>


				<div class="goods_list">
					<div
            class="item"
            @click="toproduct(goods.goods_commonid)"
            v-for="(goods,index) in goodsList"
            :key="index">
              <div class="img">
                <img  v-lazy="goods.goods_image"/>
              </div>
              <div class="goods_info">
                <div class="goods_name">{{goods.goods_name}}</div>
                <div class="goods_price">￥{{goods.goods_price}}</div>
              </div>
					</div>
					<div class="clear"></div>
					<div class="more" v-if="showMore && isMore">下拉加载显示更多...</div>
					<div class="more" v-if="showMore && !isMore ">我也是有底线的...</div>
				</div>

			</div>
		</scroll>
		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
import scroll from '@/components/common/scroll'
import loading from '@/components/common/loading'
import carousel from '@/components/common/carousel'
export default{
	components: {
      	scroll,loading,carousel
	},
	data() {
		return {
			BSstyle:"position:absolute;top:0px;bottom:50px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
			showSearch:false,
			goodsList:[],
			curpage:1,
			perpage:10,  //每页显示条数
			isMore:true,     //是否还有更多
			showMore:false,  //是否显示下拉加载的文字
			isPost:false,    //是否正在请求
			ifload:true,
			banner:[],
			classMenu:[],
      pintuanInfo:[],
      seckillInfo:[],
		}
	},
	created() {
		this.$nextTick(()=>{
			this.getProductList();
			this.getPlatinfo();
      this.getPromotionInfo();
		})
	},

	methods:{
    //获取商品
		getProductList() {
			if(this.isPost || !this.isMore) return true;
			this.isPost = true;
			this.$post_('goods/goods/list',{page:this.curpage},(res)=>{
				console.log(res);
				let data = res.data;
				if(data.length>0){
					data.forEach((val)=>{
						this.goodsList.push(val);
					})
					this.curpage++;
					if(data.length<this.perpage){
						this.isMore = false;
					}
					this.showMore = true;
				}else{
					this.isMore = false;
				}
				this.isPost = false;
				this.ifload = false;

			});
		},
    // 获取 轮播图，公告、分类等信息
		getPlatinfo() {
			this.$post_('platform/notice/platinfo',{},(res)=>{
				console.log(res);
				this.banner = res.data.banner;
				this.classMenu = res.data.class_nav;
			});
		},

    //获取促销信息
    getPromotionInfo(){
      this.$post_('goods/promotion/get_promotion_info',{},(res) =>{
        this.pintuanInfo = res.data.pintuan_info;
        this.seckillInfo = res.data.seckill_info;
      })
    },

    //下拉到底部加载
		scroll_end(pos,maxScrollY) {
			if(maxScrollY==pos){
				this.getProductList()
			}
		},
		scroll(pos,maxScrollY) {
			this.showSearch = Math.abs(pos.y)>=200?true:false;
			// console.log(this.showSearch);
		},
		toproduct(id){
			this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:id}})
		},
	}
}
</script>

<style type="text/css" scoped>
	.content{
		width: 100%;
	}
	.search{
		position: fixed;
		top: 0px;
		left: 0px;
		height: 50px;
		line-height: 50px;
		width: 100%;
		z-index: 9999;
		background: rgba(255,255,255,1);
		font-size: 20px;
		text-align: center;
		text-indent: 20px;
		border-bottom: 1px solid #ddd;
	}
	.search .iconfont{
		font-size: 28px;
		color:#555;
		vertical-align: middle;
	}
	.search span{
		font-size: 14px;
		color:#555;
	}

	.class_nav{
		width: 100%;
		/*display: flex;*/
		background: #fff;
		text-align: left;
	}
	.class_nav .item{
		display: inline-block;
		width: 25%;
		text-align: center;
		padding:20px 0px;
	}
	.class_nav .item img{
		width: 48px;
	}
	.class_nav .item .text{
		font-size: 13px;
		/*line-height: 10px;*/
	}
  .promotion{
    width: 100%;
    margin-bottom: 10px;
  }
  .promotion .img{
    position: relative;
  }
  .promotion .img .title{
    position: absolute;
    bottom: 0px;
    left: 0px;
    width: 100%;
    height: 40px;
    line-height: 40px;
    background: rgba(0,0,0,0.4);
    color:#fff;
    font-size: 16px;
    text-align: left;
    text-indent: 10px;
  }
  .promotion .img img{
    width: 100%;
    max-height: 300px;
  }

	.goods_list{
		width: 100%;
		padding:0px;
		margin: 0px;
	}
	.goods_list .item{
		float: left;
		background: #fff;
		width: 49%;
		margin-top: 10px;
		text-align: left;


	}
	.goods_list div:nth-child(2n-1){
		margin-right: 2%;
	}
	.goods_list .item .img{
		height:200px;
		overflow: hidden;
	}
	.goods_list .item .img img{
		width: 100%;
	}
	.goods_list .item .goods_info{
		height: 60px;
		padding:0px 5px;
	}
	.goods_list .item .goods_info .goods_name{
		text-indent: 5px;
		font-size: 12px;
    overflow:hidden;
    text-overflow:ellipsis;
    display:-webkit-box;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:2;
	}
	.goods_list .item .goods_info .goods_price{
		color:#FF0036;
	}
	.more{
		height:40px;
		line-height: 40px;
		/*background: #fff;*/
	}
</style>
