<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<scroll ref="scroll" @scroll="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="root">
				<div class="item" :class="key==activeKey?'active':''" v-for="(item,key) in rootCate" @click="select_it(item,key)">
					<img :src="item.img" />
					<div class="name">{{item.category_name}}</div>
				</div>
			</div>
		</scroll>

		<scroll @scroll="scroll2"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle2">
			<div class="child_cate">
				<div class="item" :class="key==activeKey?'active':''" v-for="(item,key) in childCate" @click="search_goods(item)">
					<img :src="item.img" />
					<div class="name">{{item.category_name}}</div>
				</div>
			</div>
		</scroll>

		<loading :ifload="ifload"></loading>
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
			headerName:'产品分类',
			BSstyle:"position:absolute;top:45px;bottom:50px;left:0px;width:15%;overflow:hidden;background:#f0f0f0;",
			BSstyle2:"position:absolute;top:45px;bottom:50px;left:15%;width:85%;overflow:hidden;background:#fff;",
			rootCate:[],  //一级分类
			activeKey:0,   //当前活动key
			childCate:[],
			ifload:true,
		}
	},
	mounted() {
		this._init_data();
	},
	methods:{
		_init_data() {
			this.$post_('goods/category/categroy_root',{},(res)=>{
				console.log(res);
				if(res.code=='0'){
					this.rootCate = res.data;
					this.get_child(res.data[0].id);

				}
			});
		},
		//获取子分类
		get_child(pid) {
			this.ifload = true;
			this.$post_('goods/category/get_child',{pid:pid,is_sub:1},(res)=>{
				console.log(res);
				if(res.code=='0'){
					this.childCate = res.data;
					this.ifload = false;
				}
			});
		},

		//选择一级分类
		select_it(item,key) {
			this.activeKey = key;
			this.get_child(item.id);
		},
		//显示对应分类下的产品
		search_goods(item) {
			this.$router.push({path:'/page/goods/class_goods_list',query:{cate_id:item.id,name:item.category_name}});
		},
		scroll() {

		},
		scroll2() {

		}
	}
}
</script>

<style type="text/css" scoped>
	.root{
		width: 100%;
		padding: 0px;
		margin: 0px;
	}
	.root .item{
		width: 100%;
		text-align: center;
		margin:0px;
		padding:5px 0px;

	}
	.root .active{
		background: #fff;
		border-left: 2px solid #FF0036;
		color:#FF0036;
	}
	.root .item img{
		width: 40px;

		vertical-align: bottom;
	}

	.child_cate{
		width: 100%;
		padding:10px;
		text-align: left;
	}
	.child_cate .item{
		text-align: center;
		display: inline-block;
		width: 33.3%;
		margin-top:10px;
	}

	.child_cate .item img{
		width: 40px;
		vertical-align: bottom;
	}
</style>
