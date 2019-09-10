<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<scroll ref="scroll" @scroll="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="content">
				<div class="goods_list">
					<div class="item" @click="toproduct(goods.goods_commonid)" v-for="(goods,index) in goodsList">
						<div class="img">
							<img  v-lazy="goods.goods_image"/>
						</div>
						<div class="goods_info">
							<div class="goods_name">{{goods.goods_name}}</div>
							<div class="goods_price">￥{{goods.goods_price}}</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class="more" v-if="showMore && isMore && !noGoods">下拉加载显示更多...</div>
				<div class="more" v-if="showMore && !isMore && !noGoods">我也是有底线的...</div>

				<div class="nogoods" v-if="noGoods">
					<div>					
						<i class="iconfont icon-meiyoudingdan-01"></i>
					</div>
					没有找到相关商品
				</div>

				<loading :ifload="ifload"></loading>
			</div>
		</scroll>
	</div>
</template>

<script type="text/javascript">
import topTile from '@/components/common/top_title'
import scroll from '@/components/common/scroll'
import loading from '@/components/common/loading'
export default{
	components: {
      	topTile,
      	scroll,
      	loading
	},			
	data() {
		return {
			headerName:'title',
			BSstyle:"position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
			cateId:0,
			goodsList:[],
			page:1,
			perpage:10,  //每页显示条数
			isMore:true,     //是否还有更多
			showMore:false,  //是否显示下拉加载的文字
			isPost:false,    //是否正在请求
			noGoods:false,  //是否有产品,:false,
			ifload:false,			
		}
	},
	created() {
		this.cateId = this.$route.query.cate_id;
		this.headerName = this.$route.query.name;
	},
	mounted() {
		this.$nextTick(()=>{
			this.get_cate_goods();
		})
	},
	actived(){

	},		
	methods:{
		get_cate_goods() {
			if(this.isPost) return true;
			this.isPost = true;
			this.ifload = true;
			this.$post_('goods/goods/list',{cate_id:this.cateId,page:this.page},(res)=>{
				console.log(res);
				this.ifload = false;
				let data = res.data;
				if(data.length>0){
					data.forEach((val)=>{
						this.goodsList.push(val);
					})
					this.page++;
					if(data.length<this.perpage){
						this.isMore = false;
					}
					this.showMore = true;
				}else{
					this.isMore = false;
				}

				if(this.goodsList.length<1){
					this.noGoods = true;
				}	
				this.isLoad = true;	
				this.isPost = false;
			});
		},
		toproduct(id){
			this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:id}})
		},		
		scroll(pos,maxScrollY) {
			if(maxScrollY==pos){
				this.get_cate_goods()
			}
		}
	}
}
</script>

<style type="text/css" scoped>
	.content{
		width: 100%;
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
	}
	.goods_list .item .goods_info .goods_price{
		color:#FF0036;
	}
	.more{
		height:40px;
		line-height: 40px;
		/*background: #fff;*/
	}
	.nogoods{
		height: 200px;
		margin-top:50px;
		width: 100%;
		text-align: center;
	}
	.nogoods .iconfont{
		font-size: 64px;
		color:#FF0036;
	}

</style>