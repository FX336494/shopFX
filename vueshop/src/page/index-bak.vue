<template>
  	<div class="recommend" ref="recommend">
<!--   		<router-link tag="div" class="body_item" to="/mywallet/mywallet" style="line-height:40px;background:#46adfc;text-align:left;padding-left:15px;font-size:16px;letter-spacing:2px;color:#fff;">
	  		<img src="@/assets/images/goback.png" style="width:20px;">
	  			首页
  		</router-link> -->

  		<div class="top" ref='top_sel' @click="to_search()">
  			<div class="top_search">
  				<img src="../assets/images/s.png">
  				产品搜索
  			</div>
  		</div>
  		<!--  -->

	    <scroll ref="scroll" class="recommend-content_" @scroll="scroll" :data="goods_list"  @scrollEnd="scroll_" :listenScroll="true" :probeType="3">
		    <div ref="scrollbox" class="scrollbox">
		    	<div v-if="banner.length>0" class="slider-wrapper" ref="sliderWrapper">
		        	<carousel :ifloop = "true" :ifauto="true" :direction="true">
						<div class="swiper-slide" v-for="str in banner">
							<img :src="str.img_url">
	        			</div>
		        	</carousel>
	        	</div>

			    <div class="qt_title" id="goods2">
		    		<div>商品ss<span style="color:#e70012;font-size:16px;">推荐</span></div>
    		    </div>

    		    <div>
			        <div class="qt_products" v-for="(item,index) in goods_list">
			        	<img @load="loadimg" class="qt_products_img" v-lazy="item.goods_image" @click="toproduct(item.id)">
			        	<div class="qt_price_name">
			        		<div class="qt_name">{{item.goods_name}}</div>
			        		<div style="height:16px;">
			        			<span style="color:red;">
			        				￥{{item.goods_price}}
			        			</span>
			        		</div>
			        	</div>
			        </div>
			    	<div class="clear"></div>
			    </div>
	    
				<div style="height:10px;" ></div>
	        	<div style="height:60px;line-height:20px;font-size:12px;color:gray;">{{ifmore?'加载中...':'我是有底线的'}}</div>

		       
		    </div>
	    </scroll>

  	</div>
</template>
<script>
import {post_} from '@/components/js/common'
import carousel from '@/components/common/carousel'
import Scroll from '@/components/common/scroll'
import Alert from '@/components/common/alert'

export default{
	components: {
      	Scroll,
      	carousel,
      	Alert,
    },
	data (){
		return {
			banner:[],
			imgtype:require('@/assets/images/to_down.png'),
			showtext:true,
			maxScrollY:'',
			ifmore:true,
			imgnum:0,
			ifloaded:false,
			end_time:'',
			goods_list:[],
			curpage:1,
		}
	},
	created(){
		// this.get_info()
		this.get_product_list();
		this.get_banner();
	},
	activated(){
		this.$nextTick(()=>{
			if(this.maxScrollY == 0){
				this.ifloaded = !this.ifloaded
			}
		})	
	},
	deactivated(){
		clearInterval(this.timer)
	},
	mounted(){
		this.$refs.top_sel.style.opacity = 0
		this.$refs.recommend.style.height = window.screen.height -50 +'px'
	},
	watch:{
		maxScrollY(){
			if(this.maxScrollY == 0){
				this.ifloaded = !this.ifloaded
			}
		}	
	},
	methods:{
		get_product_list() {
			post_('goods/goods/list',{page:this.curpage},(res)=>{
				console.log(res);
				this.ifloading = true
				this.showload = false	
				if(!res.data.length) this.ifmore = false;				
				if(res.code==0){
					res.data.forEach((key)=>{
						this.goods_list.push(key)
					})

				}else{
					this.ifmore =false
				}
				this.curpage ++	

			});
		},

		get_banner() {
		    //获取轮播图
		    // post_w('act=purse&op=banner',{},(res)=>{
		    //     console.log(res)
		    //     this.banner=res.datas;
		    // });
		},


		toproduct(id,time){
			this.$router.push({path:'/page/goods/goods_detail',query:{goods_commonid:id,end_time:time}})
		},
		loadimg(){
			this.imgnum++
			if(this.imgnum == this.goods_list.length){
				this.ifloaded = !this.ifloaded
			}
		},
		scroll_(pos,maxScrollY){  
			if(maxScrollY==pos)
			{
				if(!this.ifloading || !this.ifmore){
					return
				}
				this.get_product_list()
			}
		},		
		scroll(pos,maxScrollY){
			this.maxScrollY = maxScrollY
			if(!document.getElementById('goods2')){
				return
			}
			var height = document.getElementById('goods2').offsetTop
			if(-pos.y >height-150){
				this.imgtype = require('@/assets/images/to_up.png')
				this.showtext = false
			}else{
				this.imgtype = require('@/assets/images/to_down.png')
				this.showtext = true
			}
			this.$refs.top_sel.style.opacity = (-pos.y/240)<1?(-pos.y/240):1
		},
		to_search(){
			if(this.maxScrollY>1000){
				this.$refs.scrollbox.style.height=this.maxScrollY+50+'px'
			}
			this.$router.push({path:'/product/search'})
		},
	}
}
</script>

<style scoped>
	.newsimg{
		flex: 2;
	}
	.newsimg img{
		width: 100%;
	}
	.newscontent{
		flex: 5;
	}
	.newsmore{
		flex: 1;
	}
	.newsbox{
		display: flex;
		background: #fff;
		align-items: center;
		padding: 5px 10px;
		margin-top: 5px;
	}
	.qt_title{
		height: 40px;
		line-height: 40px;
		text-align: left;
		display: flex;
		padding: 5px;
		align-items: center;
		margin-top:3px;
		background: #fff;	
	}
	.qt_title div{
		margin-right:5px;
	}
	.bp_btn{
		background: #e50112;
		width: 50%;
		padding:3px 0;
		text-align: center;
		border-radius: 5px;
		float:right;
	}
	.timebox span{
		background: #000;
		padding:2px 5px;
		margin-left: 5px;
	}
	.cartimg{
		float: right;
		width: 30px;
	}
	.qt_name{
		overflow:hidden;
		text-overflow:ellipsis;
		white-space: nowrap;
		width: 100%;
		font-size: 13px;
	}
	.qt_price_name{
		padding: 3px 5px;
	}
	.qt_products_img{
		width: 100%;
	}
	.qt_products{
		background: #fff;
		margin-top: 3px;
		text-align: left;
		float:left;
		width: 48.5%;
		margin-left: 1%;

	}
	.qt_products img{
		/*height: 100%;*/
		width: 100%;
	}
	.time_img{
		display: flex;
		align-items: center;
		background: #e70012;
		align-items: center;
		color:#fff;
		padding:10px;
	}
	.product_box{
		background: #fff;
		width: 96%;
		margin-left: 2%;
		display: flex;
		border-radius: 10px;
		padding-bottom: 10px;
	}
	.bk_product{
		flex: 1;
		text-align: center;	
		padding-right: 3px;
	}
	.swiper-slide{
	    width: 100%;
	    position: relative;
	    padding-top:28%; 
	}
	.swiper-slide img{
		position: absolute;
		top:0;
		left:0;
	}
	.swiper-slide2{
		height: 30px;
		width: 100%;
	}
	.swiper-slide img{
		width: 100%;
	}
	.up_down div{
		width: 100%;
		color:#fff;
		border-radius:50%;
		width:50px;
		height: 50px;
	}
	.item1{
		background: #f10909;
	}
	.item2{
		background: #fff;
		border:3px solid #666;
	}
	.img1{
		margin-top: 5px;
		width:40%;
	}
	.img2{
		margin-top: 10px;
		width: 60%;
	}
	.up_down{
		text-align: center;
		position: absolute;
		z-index: 999;
		bottom:70px;
		right:20px;
		width:50px;
		height: 50px;
		border-radius:50%;
	}
	.top_search{
		width: 90%;
		margin-left: 5%;
		line-height: 30px;
		border:1px solid #eee;
		background: #fff;
		border-radius: 5px;
	}
	.top_search img{
		width: 30px;
	}
	.top{
		line-height: 40px;
		background: #eee;
		height: 45px;
		position:absolute;
		top:0px;
		width: 100%;
		padding-top:5px;
		z-index: 9900; 
		border-bottom: 1px solid #ccc;
	}
	.slider-wrapper,.recommend{
		width: 100%;

	}
	.recommend{
	    background: #eff3f6;
	}
	.com_box{
		padding: 0 5px;
		background: #eff3f6;
	}
	.recommend-content_{
		height: 100%;
	  	/*overflow: hidden;*/
	}   
	.slider-wrapper{
		position: relative;
		overflow: hidden;
		height: 100%;
	}
	.swiper-slide{
		
	}
</style>