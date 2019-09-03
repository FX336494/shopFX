<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<scroll ref="scroll" @scrollEnd="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="content">
				<div class="search_form">
					<div class="item input">
						<input type="text" v-model="keyword" placeholder="请输入想要搜索的商品">
					</div>
					<div class="item sub" @click="to_search">
						搜索
					</div>
				</div>

				<div class="search_his">
					<div class="title">历史搜索</div>
					<span class="item" v-for="item in hisData" @click="to_search_his(item)">{{item}}</span>

					<div class="his_sub" v-show="hisData.length>0" @click="clear_search">清空历史搜索记录</div>
				</div>

			</div>
		</scroll>
	</div>
</template>

<script type="text/javascript">
import topTile from '@/components/common/top_title'
import scroll from '@/components/common/scroll'
export default{
	components: {
      	topTile,
      	scroll
	},			
	data() {
		return {
			headerName:'产品搜索',
			BSstyle:"position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
			keyword:'',
			hisData:[],
		}
	},
	created() {
		// sessionStorage.removeItem('search_key');
		this.$nextTick(()=>{
			this.get_search_his();
		})
	},	
	methods:{
		get_search_his() {
			let hisData = sessionStorage.search_key;
			console.log(hisData);
			if(hisData) {
				this.hisData = hisData.split(",");
				console.log(this.hisData);
			}
		},
		to_search() {
			if(!this.keyword) return false;
			if(sessionStorage.search_key){
				sessionStorage.search_key += ','+this.keyword;
			}else{
				sessionStorage.search_key = this.keyword;
			}  
			this.$router.push({path:'/page/goods/search_list',query:{keyword:this.keyword}});
		},
		to_search_his(keyword) {
			this.$router.push({path:'/page/goods/search_list',query:{keyword:keyword}});
		},
		clear_search() {
			sessionStorage.removeItem('search_key');
			this.hisData = [];
		},
		scroll() {

		}
	}
}
</script>

<style type="text/css" scoped>
	.content {
		width: 100%;
	}
	.search_form{
		width: 100%;
		display: flex;
		padding: 20px 10px;
		background: #fff;
	}
	.search_form .input{
		flex: 6;
	}
	.search_form .input input{
		width: 100%;
		height: 40px;
		line-height: 40px;
		background: #fff;
		border:1px solid #f0f0f0;
		text-indent: 20px;
	}
	.search_form .sub{
		flex: 2;
		height: 40px;
		line-height: 40px;
		background: green;
		color:#fff;
		font-size: 15px;
		font-weight: bold;
	}

	.search_his{
		width: 100%;
		background: #fff;
		padding: 20px;
		text-align: left;
	}
	.search_his .title{
		line-height: 40px;
		text-align: left;
		text-indent: 20px;
	}
	.search_his span{
		display: inline-block;
		padding: 5px 9px;
		background: #f0f0f0;
		color:#555;
		border-radius: 4px;
		margin-left: 8px;
		margin-top: 5px;
	}
	.his_sub{
		width: 40%;
		background: #f0f0f0;
		margin:30px auto;
		text-align: center;
		height: 40px;
		line-height: 40px;
		border-radius: 6px;
	}
</style>