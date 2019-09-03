<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<scroll ref="scroll" @scrollEnd="scroll_end"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="content">
				<div class="list">
					<div class="item" v-for="item in items" @click="togoods(item.goods_commonid)">
						<div class="img">
							<img :src="item.goods_image" />
						</div>
						<div class="info">
							<div class="name">{{item.goods_name}}</div>
							<div class="price">浏览次数：{{item.views}}</div>
							<div class="price">浏览时间：{{item.update_time*1000 | formatDate}}</div>
						</div>
					</div>	

					<div class="more" v-if="showMore && isMore">下拉加载显示更多...</div>
					<div class="more" v-if="showMore && !isMore ">我也是有底线的...</div>								
				</div>

			</div>
		</scroll>
		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
import {formatDate} from '@/components/js/common.js' 
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
			headerName:'我浏览的商品',
			BSstyle:"position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
			items:[],
			curpage:1,
			isMore:true,     //是否还有更多
			showMore:false,  //是否显示下拉加载的文字
			isPost:false,    //是否正在请求	
			ifload:true,		
		}
	},
	created() {
		this.$nextTick(()=>{
			this._get_favorites_goods();
		})
	},	
	methods:{
		_get_favorites_goods() {
			if(this.isPost || !this.isMore) return true;
			this.isPost = true;				
			this.$post_('goods/goods/browse_goods_list',{page:this.curpage},(res)=>{
				console.log(res);
				if(res.code=='0'){
					let data = res.data;
					if(data.length>0){
						data.forEach((val)=>{
							this.items.push(val);
						});
						this.curpage++;
						if(data.length<this.perpage){
							this.isMore = false;
						}
						this.showMore = true;	
					}else{
						this.isMore = false;
					}				
				}
				this.isPost = false;
				this.ifload = false;	
				// if(this.items.length<this.perpage/2) this.showMore = false;		
			})
		},
		scroll_end(pos,maxScrollY) {
			// console.log(maxScrollY+'==='+pos)
			if(maxScrollY==pos){
				//触发下拉加载
				this._get_favorites_goods();
			}
		},
		togoods(goodsId) {
			this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:goodsId}})
		}
	},
	filters:{
        formatDate(time){
            let date = new Date(time);
            return formatDate(date,'yyyy-MM-dd hh:mm');
        }
    }
}
</script>

<style type="text/css" scoped>
	.content{
		width: 100%;
	}
	.list{
		width: 100%;

	}
	.list .item{
		width: 100%;
		font-size: 0px;
		padding: 10px;
		background: #fff;
		text-align: left;
		border-bottom: 1px solid #f0f0f0;
	}
	.list .item .img{
		width: 20%;
		display: inline-block;
	}
	.list .item .img img{
		width:80%;
	}
	.list .item .info{
		width: 60%;
		display: inline-block;
		font-size: 12px;
		line-height: 20px;
		vertical-align: top;
	}
	.list .item .info .name{
		font-size: 13px;
		font-weight: bold;
		color:#555;
	}

	.list .more{
		height:40px;
		line-height: 40px;
		/*background: #fff;*/
	}		

</style>