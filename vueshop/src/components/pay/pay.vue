<template>
	<div class="wrapper">
			<div class="content">
				<div class="title">
					选择支付方式
					<i @click="close_type" class="iconfont icon-cuowu"></i>
				</div>
				<div class="list">
					<div class="item" @click="pay('balance')">
						<span class="icon"><i class="iconfont icon-zhifu"></i></span>
						<span class="name">积分支付</span>
						<span class="select">
							<i class="iconfont icon-ziyuan" :class="payType=='balance'?'active':''"></i>
						</span>
					</div>
					<div class="item" @click="pay('alipay')">
						<span class="icon"><i class="iconfont icon-zhifubao"></i></span>
						<span class="name">支付宝</span>
						<span class="select">
							<i  class="iconfont icon-ziyuan" :class="payType=='alipay'?'active':''"></i>
						</span>
					</div>
					<div class="item" @click="pay('wxpay')">
						<span class="icon"><i class="iconfont icon-weixinzhifu1"></i></span>
						<span class="name">微信支付</span>
						<span class="select">
							<i class="iconfont icon-ziyuan" :class="payType=='wxpay'?'active':''"></i>
						</span>
					</div>
				</div>

			</div>

		<div class="paylayer" v-show="platPay">
			<div class="pay">
				<div class="title">
					请输入密码支付
					<i @click="platPayClose" class="iconfont icon-cuowu"></i>
				</div>
				<div class="info">
					<span>积分支付</span>
					<div class="pay_money">￥<b>{{this.totalFee}}</b></div>
				</div>
				<div class="pass">
					<input type="password" v-model="payPass" placeholder="输入支付密码" />
				</div>
				<div class="sub">
					<span @click="balance_pay" class="btn">确认支付</span>
				</div>
			</div>
		</div>
    <loading :ifload="ifload"></loading>
		<fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :clsType="clsCode"></fadeAlert>
	</div>
</template>

<script type="text/javascript">
import {post_} from '@/components/js/common.js'
import fadeAlert from '@/components/common/fadealert'
import loading from '@/components/common/loading'

export default{
	props:{
		order_id:'',  //订单ID
		totalFee:0, //支付金额
	},
	components: {
      	fadeAlert,loading
	},
	data() {
		return {
      ifload:false,
			clsCode:3,
			showAlert:false,
			msg:'信息提示',

			payPass:'',
			platPay:false, //平台钱包支付方式
			payType:'',
		}
	},
	created() {

	},
	mounted() {

	},
	methods:{
		pay(payType) {

			this.payType = payType;
			if(payType=='balance'){
				this.platPay = true;
				return true;
			}
      this.msg = '此支付暂未开通';
      this.showAlert = true;
		},
		close_type(){
			this.$emit('closePay')
		},
		platPayClose() {
      this.$emit('closePay')
			this.platPay = !this.platPay;
			this.$router.push({path: '/page/order/order_list',query:{type:'1'}});
		},
		//余额支付
		balance_pay() {
			if(!this.pay_validate()) return false;
      this.ifload = true;
			let params = {
				paypass:this.payPass,
				total_fee:this.totalFee,
				order_id:this.order_id,
				btype:1,
			}
			console.log(params);
			post_('order/payment/balance_pay',params,(res)=>{
				console.log(res);
				this.msg = res.msg;
				this.showAlert = true;
        this.ifload = false;
				if(res.code=='0'){
					this.clsCode = 2;
					this.platPay = false;
					this.$emit('payAfter');
				}
			});


		},

		pay_validate() {
			if(!this.totalFee || this.totalFee<=0){
				this.showAlert = true;
				this.msg = '金额不能小于0';
				return false;
			}
			if(!this.order_id){
				this.showAlert = true;
				this.msg = '支付参数错误';
				return false;
			}
			if(!this.payPass && this.payType=='balance'){
				this.showAlert = true;
				this.msg = '请输入密码';
				return false;
			}
			return true;
		}
	}
}
</script>

<style type="text/css" scoped>
  .wrapper{
    position:fixed;
    bottom:0px;
    left:0px;
    top: 40px;
    width:100%;
    overflow:hidden;
    background:rgba(0,0,0,0.5);
    padding-bottom:20px;
    border-top:1px solid #f0f0f0;
  }
	.content{
		width: 100%;
    position: absolute;
    bottom: 0px;
    left: 0px;
    background: #fff;
	}
	.content .title{
		line-height: 40px;
		font-weight: 600;
		height: 40px;
		border-bottom: 1px solid #f0f0f0;
		position: relative;
	}
	.content .title i{
		position: absolute;
		right: 10px;
		top: 0px;
		/*background: red;*/
		padding-right: 10px;
		padding-left: 10px;
	}
	.content .list{
		width: 100%;
	}
	.content .list .item{
		width: 100%;
		display: flex;
		text-align: left;
		border-bottom: 1px solid #f0f0f0;
		line-height: 40px;
		height: 40px;
	}
	.content .list .item span{
		display: inline-block;
		flex: 1;
	}
	.content .list .item .icon{
		text-align: center;
	}
	.content .list .item .icon .iconfont{
		font-size: 32px;
	}
	.content .list .item .icon-zhifubao{
		color:#1296db;
	}
	.content .list .item .icon-weixinzhifu1{
		color:#0e932e;
	}

	.content .list .item .name{
		flex: 8;
		text-indent: 10px;
		/*background: red;*/
	}
	.content .list .item .select{
		text-align: center;
		font-size: 32px;
	}
	.content .list .item .select .iconfont{
		font-size: 24px;
	}
	.content .list .item .select .active{
		color:green;
	}



	.paylayer{
		width: 100%;
		position: fixed;
		top:0px;
		bottom: 0px;
		left: 0px;
		background: rgba(0,0,0,0.5);

	}
	.paylayer .pay{
    position: absolute;
		width: 95%;
		top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
		background: #fff;
		padding-bottom: 20px;
	}
	.paylayer .pay .title{
		line-height: 40px;
		height: 40px;
		font-weight: 600;
		position: relative;
	}
	.paylayer .pay .title i{
		position: absolute;
		right: 10px;
		top: 0px;
		/*background: red;*/
		padding-right: 10px;
		padding-left: 10px;
	}

	.paylayer .pay .info .pay_money b{
		font-size: 24px;
	}
	.paylayer .pay .pass {
		width: 60%;
		border:1px solid #f0f0f0;
		margin: 0px auto;
	}
	.paylayer .pay .pass input{
		line-height: 35px;
		height: 35px;
		width: 100%;
		text-indent: 10px;
	}
	.paylayer .pay .sub{
		margin-top: 20px;
	}
	.paylayer .pay .btn{
		width: 120px;
		background: green;
		color:#fff;
	}
</style>
