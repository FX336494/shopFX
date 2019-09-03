<template>
	<div class="wrapper">
		<div class="content">
			<div class="header">
				<div class="item avator">
					<i v-show="!member.member_avatar" class="iconfont icon-robot"></i>
					<img v-show="member.member_avatar" :src="member.member_avatar" class="img">
					<div class="uploada">
						<upload @showImg="showImg" :opacity="opacity"></upload>
					</div>
				</div>
				<div class="item info">
					<span class="name">{{member.member_name}}</span>
					<span class="memberId">会员ID：{{member.member_id}}</span>
				</div>
				<router-link to="/page/set/index" tag="div" class="item set">
					<i v-show="!member.member_avator" class="iconfont icon-shezhi"></i>
				</router-link>
			</div>

			<div class="like">
				<router-link tag='div' to='/page/member/favorites_goods' class="item">
					<i class="iconfont icon-shoucang"></i>
					<div>收藏夹</div>
				</router-link>
				<router-link tag='div'  to='/page/member/browse_goods' class="item">
					<i class="iconfont icon-zuji"></i>
					<div>足迹</div>
				</router-link>
			</div>


			<div class="tools">
				<div class="tools-item">
					<div class="title">
						<b>我的订单</b>
						<span @click="toOrder(0)">查看全部订单<i class="iconfont icon-right"></i></span>
					</div>
					<div class="rows">
						<div class="item" @click="toOrder(1)">
							<i class="iconfont icon-daifukuan"></i>
							<div>待付款</div>
							<span class="num">{{orderInfo[1]}}</span>
						</div>
						<div class="item" @click="toOrder(2)">
							<i class="iconfont icon-icon-daifahou"></i>
							<div>待发货</div>
							<span class="num">{{orderInfo[2]}}</span>
						</div>
						<div class="item" @click="toOrder(3)">
							<i class="iconfont icon-daishouhuo"></i>
							<div>待收货</div>
							<span class="num">{{orderInfo[3]}}</span>
						</div>
						<div class="item" @click="toOrder(4)">
							<i class="iconfont icon-pingjia"></i>
							<div>评价</div>
							<span class="num">{{orderInfo[4]}}</span>
						</div>
					</div>
				</div>

				<div class="tools-item">
					<div class="title">
						<b>我的工具</b>
					</div>
					<div class="rows">
						<router-link tag='div' class="item" :to="{path:'/page/order/address_list'}" >
							<i class="iconfont icon-location"></i>
							<div>收货地址</div>
						</router-link>
						<router-link tag='div' class="item" :to="{path:'/page/wallet/index'}" >
							<i class="iconfont icon-wallet"></i>
							<div>我的钱包</div>
						</router-link>
					</div>
				</div>

			</div>

		</div>
		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
import upload from '@/components/common/upload'
import loading from '@/components/common/loading'
export default{
	components: {
      	upload,loading
	},
	data() {
		return {
			ifload:true,
			member:[],
			orderInfo:[],
			opacity:0,
		}
	},
	created() {
		this.$nextTick(()=>{
			this._init_data();
		})
	},
	methods:{
		_init_data() {
			this.$post_('member/member/info',{},(res)=>{
				console.log(res);
				this.ifload = false;
				this.member = res.data;
				this.orderInfo = res.extend.order;
			});
		},
		showImg(imgUrl) {
			console.log(imgUrl);
			this.member.member_avatar = imgUrl;
			this.$post_('member/member/edit_info',{member_avatar:imgUrl},(res)=>{
				console.log(res);
			})
		},
		toOrder(type) {
			this.$router.push({path:'/page/order/order_list',query: {type: type}})
		},
	}
}
</script>

<style type="text/css" scoped>
	.content{
		position:absolute;
		top:0px;
		bottom:50px;
		left:0px;
		width:100%;
		overflow:scroll;
		background:#f0f0f0;
	}
	.header{
		background: #333;
		width: 100%;
		display: flex;
		margin: 0px;
		padding: 0px;
	}
	.header .item{
		flex: 1;
		height: 80px;
	}

	.header .avator .uploada{
		position: absolute;
		top: 0px;
		left: 10px;
		/*opacity: 0;*/
	}

	.header .avator .img{
		width: 70px;
		height: 70px;
		border-radius: 50%;
		margin-top: 10px;
	}
	.header .info{
		flex: 2;
		color:#fff;
		text-align: left;
	}
	.header .info .name{
		display: inline-block;
		width: 100%;
		padding-top: 20px;
		line-height: 20px;
		font-size: 22px;
	}
	.header .info .memberId{
		display: inline-block;
		width: 100%;
	}
	.header .avator .iconfont{
		vertical-align: middle;
		display: inline-block;
		width: 100%;
		line-height: 80px;
		font-size:64px;
		color:#fff;
	}
	.set .iconfont{
		font-size: 24px;
		color:#fff;
		line-height: 80px;
	}

	.like{
		margin:0px;
		width: 100%;
		display: flex;
		background: #333;
		color:#fff;
		height:60px;
		padding-top:8px; /*0px 10px 0px;*/
	}

	.like .item{
		flex: 1;
	}
	.like .item .iconfont{
		font-size: 18px;
	}

	.tools-item{
		width: 95%;
		background: #fff;
		margin: 10px auto;
		border-radius: 8px;
	}
	.tools-item .title{
		height: 40px;
		line-height: 40px;
		text-indent: 15px;
		border-bottom: 1px solid #f0f0f0;
		display: flex;
	    align-items: center;
	    justify-content: space-between;
	    padding-right: 15px;

	}

	.tools-item .rows{
		display: flex;
		width: 100%;
		padding-top: 10px;
		padding-bottom: 5px;
	}
	.tools-item .rows .item{
		flex: 1;
		position: relative;
	}
	.tools-item .rows .item .num{
		position: absolute;
		right: 18px;
		top: 1px;
		color:#FF0036;
	}
	.tools-item .rows .item .iconfont{
		color:#FF0036;
		font-size: 20px;
	}

</style>
