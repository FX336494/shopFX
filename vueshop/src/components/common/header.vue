<template>
	<div class="header">
		<div>
			<div class="title_">{{name}}</div>
			<img src="@/assets/images/goback.png" @click="goback" class="goback">
			<!-- <div class="showmenu" @click="showmenu">
				<span :class="ifshowmenu?'isactived':''">...</span>
			<span style="position:absolute;height:6px;width:6px;background:red;border-radius:50%;right:5px;top:5px;" v-show="$store.state.cartnum>0"></span>
			</div> -->
		</div>
		<div class="showTriangle" v-show="ifshowmenu"></div>
		<ul class="menu" v-show="ifshowmenu">
			<li @click="topath(0)"><img src="@/assets/images/index0.png">首页</li>
			<li @click="topath(1)"><img src="@/assets/images/search0.png">搜索</li>
			<li @click="topath(2)"><img src="@/assets/images/class0.png">分类</li>
			<li @click="topath(3)"><img src="@/assets/images/qianbao0.png">钱包</li>
			<li style="position:relative;" @click="topath(4)"><img src="@/assets/images/cart0.png">
				购物车
				<span class="cartnum" v-show="$store.state.cartnum>0">{{$store.state.cartnum}}</span>
			</li>
			<li @click="topath(5)" style="border-bottom:none;"><img src="@/assets/images/my0.png">个人中心</li>
		</ul>
	</div>
</template>

<script>
	export default{
		props: {
      		name:''
    	},
	   	data() {
	      	return {
	        	ifshowmenu:false
	      	}
	    },
	    methods:{
	    	goback(){
	    		this.$router.go(-1)
	    	},
	    	showmenu(){
	    		this.ifshowmenu = !this.ifshowmenu
	    	},
	    	topath(id){
	    		this.ifshowmenu = false
	    		switch(id){
					case 0:
						this.$router.push({path:'/index'})
						break;
					case 1:
						this.$router.push({path:'/product/search'})
						break;
					case 2:
						this.$router.push({path:'/product/product_class'})
						break;
					case 3:
						this.$router.push({path:'/news'})
						break;
					case 4:
						this.$router.push({path:'/cart'})
						break;
					default:
						this.$router.push({path:'/my'})
				}
	    	}
	    }
	}
</script>

<style scoped>
	.isactived{
		color:#165d94;
	}
	.cartnum{
		position: absolute;
		right: 10px;
		top:5px;
		height: 16px;
		width: 16px;
		border-radius: 50%;
		font-size: 10px;
		background: red;
		color:#fff;
		line-height: 16px;
		text-align: center;
	}
	.showTriangle{
		position: absolute;
		right:6%;
		top:40px;
		border-right: 7px solid transparent;
  		border-bottom: 10px solid #165d94;
  		border-left: 7px solid transparent;
  		transform: translateX(50%);
	}
	.menu li{
		border-bottom: 1px solid #ccc;
		position: relative;
		line-height: 300%;
	}
	.menu li img{
		width: 16%;
		position: absolute;
		left:5%;
		top:26%;
	}
	.menu{
		position: absolute;
		background: #165d94;
		width: 35%;
		right:5px;
		top:50px;
		border-radius: 5px;
		padding:0 2%; 
	}
	.header{
		position: fixed;
		top:0;
		height: 40px;
		width: 100%;
		background: #333;
		color:#fff;
		text-align: center;
		line-height: 40px;
		left: 0;
		z-index: 600;
	}
	.title_{
		position: absolute;
		width: 80%;
		left:10%;
		font-size: 17px;
		overflow:hidden;
		text-overflow:ellipsis;
		white-space: nowrap;
	}
	.showmenu{
		float: right;
		color:#fff;
		font-size: 30px;
		line-height: 20px;
		text-align: center;
		width: 10%;
	}
	.goback{
		float: left;
		margin-left: 10px;
		width: 26px;
		margin-top: 7px;
	}
</style>