<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<div class="content">
      <div class="address">
        <div class="item location">
          <i class="iconfont icon-location"></i>
        </div>
        <div class="item info">
          <div class="user">
            <span>收货人：{{reciverInfo.true_name}}</span>
            <span>电话:{{reciverInfo.mob_phone}}</span>
          </div>

          <div class="detail">
            <span>收货地址：{{reciverInfo.area_info}}{{reciverInfo.address}}</span>
          </div>
        </div>
      </div>

      <div class="goods">
        <div class="item">
          <div class="cart" v-for="(goods,index) in orderGoods" :key='index'>
            <div class="info">
              <div class="img">
                <img :src="goods.goods_image" />
              </div>
              <div class="text">
                <p class="name">{{goods.goods_name}}</p>
                <p class="price">
                  <span class="p1"> ￥{{goods.goods_price}}</span>
                  <span class="num">x{{goods.goods_num}}</span>
                </p>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="extra">
        <p class="subtotal">
          <span v-show="couponInfo.coupon_id">优惠券抵扣：{{couponInfo.coupon_money}}</span>
          <span>合计（含运费）:<b>￥ {{order.order_amount}}</b></span>
        </p>
      </div>

      <div class="order-info">
        <div class="title">订单信息</div>
        <div class="item">订单状态：{{order.state_text}}</div>
        <div class="item">订单类型：{{order.order_type_text}}</div>
        <div class="item">订单编号：{{order.create_time}}{{order.order_id}}</div>
        <div class="item">支付编号：{{order.pay_sn}}</div>
        <div class="item">创建时间：{{order.create_time}}</div>
        <div class="item">支付时间：{{order.payment_time}}</div>
      </div>

		</div>

    <div>
      <div class="footer">
          <span class="pay" @click="selectPay()" v-show="order.order_state=='1'">
            立即支付
          </span>
          <span class="pay" style="border: none;background: none;color:#000"  v-show="order.order_state=='2'">
            待发货
          </span>
          <span class="pay" @click="order_receive(order,index)" v-show="order.order_state=='3'">
            确认收货
          </span>
      </div>
    </div>


		<loading :ifload="ifload"></loading>
    <fadeAlert
      :msg="msg"
      v-if="showAlert"
      @hideFade="showAlert=false"
      :url="url">
    </fadeAlert>

    <pay v-if="showPay"
      @closePay="selectPay"
      @payAfter="payAfter"
      :order_id="order.order_id"
    >
    </pay>

	</div>
</template>

<script type="text/javascript">
	import topTile from '@/components/common/top_title'
	import loading from '@/components/common/loading'
	import alert from '@/components/common/alert'
	import fadeAlert from '@/components/common/fadealert'
  import pay from '@/components/pay/pay'
	export default {
		components: {
			topTile,
			loading,
      alert,
      fadeAlert,
      pay
		},
		data() {
			return {
				headerName: '订单详情',
				ifload:false,
        ifshow:false,
        content:'提示信息',
        msg:'',
        showAlert:false,
        url:'',
        showPay: false, //显示支付
        showOfflinePay:false, //显示线下支付信息

        type:'', //1 自己的订单  2下级订单
        orderId:0,
        order:[],
        reciverInfo:[], //收货信息
        orderGoods:[],
        couponInfo:[],
			}
		},
    created() {
      this.orderId = this.$route.query.order_id;
      this.type = this.$route.query.type;
      this.$nextTick(() =>{
        this.getDetail();
      })
    },
    methods:{
      getDetail() {
        this.ifload = true;
        let params = {order_id:this.orderId};
        this.$post_('order/order/get_order_info',params,(res) =>{
          console.log(res);
          this.ifload = false;
          this.order = res.data;
          this.reciverInfo = this.order.reciver_info;
          this.couponInfo = this.order.coupon_info;
          this.orderGoods = this.order.order_goods;
        })
      },

      selectPay() {
          this.showPay = !this.showPay;
      },
      payAfter() {
        setTimeout(() => {
          this.showPay = !this.showPay;
          this.getDetail();
        }, 1000)
      },

      //确认收货
      order_receive() {
        this.$post_('order/order/order_receive', {
          order_id: this.order.order_id
        }, (res) => {
          this.showAlert = true;
          this.msg = res.msg;
          if (res.code == '0') {
            this.clsCode = 2;
          } else {
            this.clsCode = 3;
          }
        });
      },

    }
	}
</script>

<style type="text/css" scoped>
	.wrapper {
		position: absolute;
		top: 0px;
		bottom: 50px;
		left: 0px;
		width: 100%;
		overflow: auto;
		background: #f0f0f0;
	}

	.wrapper .content {
		width: 100%;
	}
  .top{
      padding: 20px;
      background: linear-gradient(to right, #f3cd0c , #f32d0b);
      color:#fff;
   }
   .top p{
     font-size: 14px;
     color:rgba(0,0,0,0.5);
   }

   .delivery{
     width: 100%;
     padding: 10px;
     background: #fff;
     text-align: left;
     text-indent: 10px;
     border-bottom: 1px solid #f0f0f0f0;
     line-height: 25px;
   }

  .address {
    display: flex;
    width: 100%;
    padding: 10px;
    background: #fff;
    margin-top: 1px;
  }

  .address .item {
    flex: 0;
  }

  .address .location {
    padding: 20px 15px 15px 10px;
    text-align: center;
  }

  .address .location .iconfont {
    display: inline-block;
    vertical-align: top;
    font-size: 24px;
    color: blue;
  }

  .address .info {
    flex: 6;
    text-align: left;
    padding-top: 10px;
  }

  .address .info .user span {
    display: inline-block;
    width: 49%;
    margin-bottom: 6px;
  }

  .address .info .user span:last-child {
    display: inline-block;
    text-align: right;
    padding-right: 8px;
  }

  .address .right {
    /*width: 10px;*/
    padding: 20px 0px 15px 0px;
    font-size: 20px;
    color: #555;
  }

  .goods {
    width: 100%;
  }

  .goods .item {
    width: 100%;
    margin-top: 0px;
    padding: 15px 5px 20px 5px;
    background: #fff;
    border-top: 1px solid #f0f0f0;
  }

  .goods .item .store {
    line-height: 30px;
    font-size: 14px;
    text-align: left;
  }

  .goods .item .cart {
    width: 100%;
    /*height: 80px;*/
    display: flex;
  }

  .goods .item .cart .info {
    flex: 5;
    /*background: orange;*/
    text-align: left;
    padding: 10px;
  }

  .goods .item .cart .info .img {
    display: block;
    float: left;
    width: 30%;
    max-height: 80px;
  }

  .goods .item .cart .info .text {
    display: block;
    float: left;
    width: 70%;
    text-align: left;
  }

  .goods .item .cart .info img {
    width: 90%;
    vertical-align: middle;
    display: inline-block;
  }

  .goods .item .cart .info .text {
    font-size: 14px;
    display: inline-block;
  }

  .goods .item .cart .info .price .p1 {
    display: inline-block;
    width: 49%;
    color: #FF0036;
    font-size: 16px;
  }

  .goods .item .cart .info .price .num {
    text-align: right;
    display: inline-block;
    width: 49%;
  }

  .extra {
    background: #fff;
    margin-top: 0px;
    padding-top: 10px;
    line-height: 40px;
  }

  .extra .item {
    text-align: left;
    text-indent: 5px;

  }

  .extra .item span {
    display: inline-block;
    width: 48%;
    font-size: 14px;
  }

  .extra .item span:last-child {
    text-align: right;
  }

  .extra .msg {
    display: flex;

  }

  .extra .msg span {
    flex: 2;
    display: inline-block;
  }

  .extra .msg span:last-child {
    flex: 7;
    text-align: left;
  }

  .extra .msg span:fisrt-child {}

  .extra .msg span input {
    width: 100%;
  }

  .extra .subtotal {
    text-align: right;
    padding-right: 15px;
  }

  .extra .subtotal span:first-child {
    margin-right: 15px;
  }

  .extra .subtotal b {
    color: #FF0036;
  }

  .order-info{
    width: 100%;
    padding: 10px;
    text-align: left;
    text-indent: 10px;
    background: #fff;
  }
  .order-info .title{
    font-size: 15px;
    border-left: 2px solid green;
  }
  .order-info .item{
    line-height: 30px;
  }

  .footer{
    position: fixed;
    bottom: 0px;
    left: 0px;
    width: 100%;
    height: 50px;

    border-top: 1px solid #ddd;
    text-align: right;
    padding-right: 20px;
  }
  .footer span{
    display: inline-block;
    min-width: 80px;
    text-align: center;
    background: #f32d0b;
    border-radius: 10px;
    color:#fff;
    line-height: 30px;
    margin-top: 10px;
  }

  .footer .order_state {
    width: 100%;
    background: none;
    /* margin-top: 10px; */
  }

  .footer .order_state span {
    display: inline-block;
    /* padding: 5px 13px; */
    /* width: 100%; */
    background: none;
    border: 1px solid #555;
    border-radius: 15px;
  }

  .footer .order_state .pay {
    color: green;
    border-color: green;
    padding: 0px 5px;
  }

</style>
