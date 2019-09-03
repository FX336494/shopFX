<template>
<div class="tab"  v-show="showtab">
	<router-link 
		tag="div" 
		class="tab-item" v-for="(menu,index) in menus" 
		:to="menu.url" 
		:key="index"
		:class="rot==menu.url?'active':''"
	>
		<i class="iconfont" :class="menu.icon"></i>
	    <div class="tab-link">{{menu.name}}</div>
	</router-link>
</div>

</template>
<script>
import {post_} from '@/components/js/common'
	export default{
		props: {
      		
    	},
		data (){
			return {
                cart_num:'',
                showtab:true,
                rot:'',
                menus:[
                	{url:'/page/index',icon:'icon-Home',name:'首页'},
                	{url:'/page/goods/class',icon:'icon-fenlei',name:'全部分类'},
                	{url:'/page/order/cart_list',icon:'icon-gouwuche',name:'购物车'}, 
                	{url:'/page/member/index',icon:'icon-wode',name:'我的'},	               	
                ],
			}
		},
		created(){
			this.rot = this.$route.path
		},
		watch:{
			"$route":"choose_tab"
		},
		methods:{
			choose_tab(){
				this.rot = this.$route.path
			}
		}
	}
</script>

<style scoped>
.cartnum{
	position: absolute;
	width:16px;
	height: 16px;
	line-height: 16px;
	border-radius: 50%;
	border: 1px solid red;
	color:red;
	font-size: 8px;
	top:0;
	right: 8px;
	background: #fff;
}
.tab{
	position: fixed;
	bottom: 0;
	height: 50px;
	width: 100%;
	border-top:1px solid #eee;
	background: #fff;
	display: flex;
}
.tab .active{
	color:#d81e06;
}
.tab-item{
	text-align: center;
	flex:1;
}
.tab .iconfont{
	font-size: 22px;
}
.tab-link{
	font-size: 12px;
	line-height: 15px;
}

</style>