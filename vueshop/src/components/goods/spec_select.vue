<template>
	<div class="wrapper">
		<div class="content">
			<div class="product">
				<div class="img">
					<img :src="goodsImage" />
				</div>
				<div class="info">
					<p class="price">￥{{goodsPrice}}</p>
					<p class="name">{{goodsName}}
						<span style="font-size:0px;"></span>
					</p>
					<p class="store">库存：{{goodsStorage}}</p>
				</div>
				<i class="iconfont icon-guanbi" @click="hidden_spec()"></i>
			</div>
			<scroll  ref="scroll" :ifloaded="true" :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="spec_list">
				<div class="spec"  v-show="specNum">
					<div v-for="(spec,specId) in specArr" class="row">
						<div class="spec-name">{{spec.name}}</div>
						<div class="spce_val">
							<div v-for="(val,key) in spec.value">
								<span class="val" :class="JSON.stringify(selectedVal).indexOf(key)>-1?'active_val':''" @click="select_spec(specId,val,key)">
									{{val}}
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="goods_num">
					<div class="item">购买数量</div>
					<div class="item">
						<span><i class="iconfont icon-jianshao_l" @click="edit_nums(-1)"></i></span>
						<input type="text" v-model="nums">
						<span><i class="iconfont icon-add" @click="edit_nums(1)"></i></span>
					</div>
				</div>
			</div>
			</scroll>
			<div class="btn" @click="confirm_buy">确认</div>
		</div>
		<fadeAlert
			:msg="msg"
			v-if="showAlert"
			@hideFade="showAlert=false">
		</fadeAlert>
	</div>
</template>

<script type="text/javascript">
	import {post_} from '@/components/js/common.js'
	import scroll from '@/components/common/scroll'
	import fadeAlert from '@/components/common/fadealert'
	export default{
	    props: ['goods','buy_type'],
	    components:{scroll,fadeAlert},
		data() {
			return {
				showAlert:false,
				msg:'信息提示',
				specArr:[],
				selectedSpec:[],
				selectedVal:[],   //选中的规格值id
				goodsId:0,
				nums:1,
				specNum:0,    //总共的规格数量
				selectNum:0,  //选中的规格数量
				BSstyle:"position:fixed;bottom:52px;left:0px;height:315px;width:100%;overflow:hidden;",
				goodsPrice:0,
				goodsName:'',
				goodsStorage:0,
				goodsImage:'',

			}
		},
		created() {
			this.$nextTick(()=>{
				console.log(this.goods);
				this._init_data(this.goods);

				this.specArr = this.goods.spec_list;
				this.specNum = 0
				for(var i in this.goods.spec_list){
					this.specNum ++
				}

				if(this.specNum<=0){
					this.get_default_goods();
				}

			})
		},
		methods: {
			_init_data(goods) {
				this.goodsPrice = goods.goods_price;
				this.goodsName  = goods.goods_name;
				this.goodsStorage = goods.goods_storage;
				this.goodsImage = goods.goods_image;
			},
			select_spec(specId,val,specValId) {
				// console.log(specId,val,specValId);
				if(this.selectedVal.indexOf(specValId)>0) return false;

				this.$set(this.selectedVal,specId,specValId);
				this.selectNum = 0;
				this.selectedVal.forEach((val)=>{
					if(val>0) this.selectNum ++;
				})
				if(this.selectNum==this.specNum){
					this.get_new_goods();
				}
			},
			//获取规格下的产品
			get_new_goods() {
				let specName = [];
				let specVal  = [];
				console.log(this.specArr);
				for(var index in this.specArr)
				{
					console.log(this.selectedVal);
					for(var i in this.specArr[index].value)
					{
						if(this.selectedVal[index]==i){
							// specName[index] = this.specArr[index].name;
							// specVal[i] = this.specArr[index].value[i]
							specName.push({[index]:this.specArr[index].name});
							specVal.push({[i]:this.specArr[index].value[i]});
							console.log(specVal);
						}
					}
				}
				this.get_spec_goods(specName,specVal);
			},

			//没有规格的获取一个默认的产品
			get_default_goods() {
				post_('goods/goods/get_default_goods',{goods_commonid:this.goods['goods_commonid']},(res)=>{
					console.log(res);
					if(res.code>0 || !res.data.id){
						this.fadeAlert=true;
						this.msg = res.msg;
						return
					}
					this.goodsId = res.data.id;
				});
			},
			//获取规格产品
			get_spec_goods(specName,specVal) {
				console.log(specName);
				console.log(specVal);
				let param = {
					goods_commonid:this.goods['goods_commonid'],
					spec_name:JSON.stringify(specName),
					spec_val:JSON.stringify(specVal)
				};
				post_('goods/goods/get_spec_goods',param,(res)=>{
					if(res.code>0 || !res.data.goods_id){
						this.fadeAlert=true;
						this.msg = res.msg;
						return
					}
					this.goodsId = res.data.goods_id;
					this._init_data(res.data);
				});
			},
			//购物车加减
			edit_nums(num) {
				this.nums = parseInt(this.nums);
				this.nums += num;
				if(this.nums<1) this.nums = 1;

			},
			//关闭规格选择
			hidden_spec(){
				this.$emit('hiddenSpec');
			},
			//确认操作
			confirm_buy() {
				if(!this.buy_validate()) return false;

				let param = {
					goods_id : this.goodsId,
					nums     : this.nums,
					buy_type : this.buy_type,
				}
				console.log(param);

				post_('order/cart/add_cart',param,(res)=>{
					console.log(res);
					// if(res.code==0 && this.buy_type=='1'){
					// 	this.$store.state.cartnum += param.nums;
					// }

					if(this.buy_type==2){
						let param = {cart_ids:res.data.cart_id,ifcart:0,nums:this.nums};
						this.$router.push({path: '/page/order/create_order',query:param});
					}
					this.$emit('cartEmit',{msg:res.msg,code:res.code});
				});
			},
			buy_validate() {
				this.nums = parseInt(this.nums);
				if(this.selectNum<this.specNum){
					this.showAlert = true;
					this.msg = '请选择完整产品信息';
					return false;
				}
				if(this.goodsStorage<this.nums){
					this.showAlert = true;
					this.msg = '库存不足';
					return false;
				}
				if(!this.goodsId){
					this.showAlert=true;
					this.msg = '产品信息错误';
					return false;
				}
				if(this.nums<1){
					this.showAlert=true;
					this.msg = '数量不能小于1';
					return false;
				}
				return true;
			}
		}
	}
</script>

<style type="text/css" scoped>
	.wrapper{
		position: fixed;
		top: 0px;
		left: 0px;
		bottom: 0px;
		width: 100%;
		height: 100%;
		background: rgba(0,0,0,0.8);
		z-index: 100000;
	}
	.wrapper .content{
		position: fixed;
		left: 0px;
		bottom: 0px;
		width: 100%;
		height: 500px;
		font-size: 0px;
		background: #fff;
	}
	.wrapper .product{
		border-bottom: 1px solid #f0f0f0;
		position: relative;
	}
	.product .icon-guanbi{
		position: absolute;
		right: 0px;
		top: -5px;
		font-size: 30px;
		color:#FF0036;
	}
	.wrapper .product .img{
		padding:5px 1%;
		width: 33%;
		display: inline-block;
		vertical-align: top;
	}
	.wrapper .product .img img{
		/*width: 100%;*/
		width: 135px;
		height: 135px;
		margin-top: -20px;
		border:3px solid #fff;
	}
	.wrapper .product .info{
		/*padding:9px 3%;*/
		padding: 9px 15px;
		width: 61%;
		display: inline-block;
		text-align: left;
	}
	.wrapper .product .info .price{
		font-size: 15px;
		color:red;
	}
	.wrapper .product .info .name{
		font-size:14px;
		margin-top:10px;
	}
	.wrapper .product .info .store{
		font-size:14px;
		display: inline-block;
		margin-top:5px;
		vertical-align: bottom;
	}

	.spec_list{
		padding-bottom: 20px;
	}

	.wrapper .spec{
		width: 100%;
		border-bottom:1px solid #f0f0f0;
		font-size: 0px;
		text-align: left;
		padding-bottom: 10px;
	}

	.wrapper .spec .row{
		font-size:14px;
		width: 95%;
		margin: 10px auto;
	}
	.wrapper .spec .row .spec-name{
		line-height: 40px;
		font-size: 15px;
		color:#111;
		font-weight: 600;
	}
	.wrapper .spec .row  .spce_val div{
		display: inline-block;
		color:14px;
		margin-left: 10px;
	}

	.wrapper .spec .row  .spce_val div:first-child{
		margin-left: 0px;
	}
	.wrapper .spec .row  .spce_val .val{
		display: inline-block;
		color:block;
		padding:5px 20px;
		background: #f0f0f0;
		border-radius: 8px;
	}
	.wrapper .spec .row .spce_val .active_val{
		background: #FF0036;
		color:#fff;
	}

	.wrapper .spec .row .spce_val .selected{
		border:1px solid red;
		color:red;
	}
	.goods_num{
		display: flex;
		width: 100%;
		color:#000;
		font-size: 14px;
		margin-top: 10px;
	}
	.goods_num .item{
		flex: 1;
		/*border:1px solid red;*/
		text-align: right;
		padding:0px 10px;
		line-height: 35px;
	}
	.goods_num div:first-child{
		text-align: left;
	}
	.goods_num .item input{
		border:1px solid #ddd;
		width: 60px;
		text-align: center;
		font-size: 15px;
	}
	.goods_num .item .iconfont{
		font-size: 32px;
		vertical-align:middle;
	}

	.wrapper .content .btn{
		display: hidden;
		position: fixed;
		bottom: 0px;
		left: 0px;
		width: 100%;
		line-height: 45px;
		height: 50px;
		background: #FF0036;
		border: none;
		font-size:20px;
		text-align: center;
		color:#fff;
		letter-spacing: 4px;
		font-weight: 200;

	}
</style>