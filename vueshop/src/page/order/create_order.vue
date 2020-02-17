<template>
  <div class="create_order">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <router-link class="address" v-if="extend.address" tag="div" to="/page/order/address_list?type=1">
        <div class="item location">
          <i class="iconfont icon-location"></i>
        </div>
        <div class="item info">
          <div class="user">
            <span>收货人：{{extend.address.true_name}}</span>
            <span>{{extend.address.mob_phone}}</span>
          </div>

          <div class="detail">
            <span>收货地址：{{extend.address.area_info}}{{extend.address.address}}</span>
          </div>
        </div>
        <div class="item right">
          <i class="iconfont icon-right"></i>
        </div>
      </router-link>
      <router-link tag="div" to="/page/order/address_list?type=1" v-if="!extend.address" class="add">
        <i class="iconfont icon-add"></i>
        请添加收货地址
      </router-link>

      <div class="goods">
        <div class="item">
          <div class="cart" v-for="(cartItem,index) in cartList" :key='index'>
            <div class="info">
              <div class="img">
                <img :src="cartItem.goods_image" />
              </div>
              <div class="text">
                <p class="name">{{cartItem.goods_name}}</p>
                <p class="price">
                  <span class="p1"> ￥{{cartItem.goods_price}}</span>
                  <span class="num">x{{cartItem.goods_num}}</span>
                </p>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="coupons" v-show="coupons.length>0">
        <span class="name">优惠券</span>
        <span class="value" v-show="coupon_id<=0" @click="showCoupon=!showCoupon">选择优惠券 > </span>
        <span class="value active" v-show="coupon_id>0" @click="showCoupon=!showCoupon">
          - ￥{{this.couponMoney}} >
        </span>
      </div>

      <div class="extra">
        <p class="item">
          <span>运费</span>
          <span>{{extend.freight}}</span>
        </p>
        <p class="msg">
          <span>买家留言:</span>
          <span><input type="text" v-model="userMsg" placeholder="选填:填写内容已和卖家协商" /></span>
        </p>
        <p class="subtotal">
          <span>共{{totalNum}}件商品</span>
          <span>小计:<b>￥ {{totalMoney-extend.freight}}</b></span>
        </p>
      </div>
    </div>

    <div class="settlement">
      <div class="item select"></div>

      <div class="item total">
        <span>合计:</span>
        <span><b>￥{{totalMoney}}</b></span>
      </div>
      <div class="item submit" @click="create_order">提交订单</div>
    </div>
    <pay
      v-if="showPay"
      @closePay="closePay"
      @payAfter="payAfter"
      :order_id="order_id">
    </pay>

    <selectCoupons v-if="showCoupon" :coupons="coupons" @checkCoupon="checkCoupon"></selectCoupons>

    <loading :ifload="ifload"></loading>
    <fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :url="url" :clsType="clsCode">
    </fadeAlert>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import fadeAlert from '@/components/common/fadealert'
  import pay from '@/components/pay/pay'
  import loading from '@/components/common/loading'
  import selectCoupons from '@/components/goods/select_coupons'
  export default {
    components: {
      topTile,
      fadeAlert,
      pay,
      loading,
      selectCoupons
    },
    data() {
      return {
        headerName: '生成订单',
        showAlert: false,
        clsCode: 1,
        msg: '',
        url: '',
        ifload: true,
        cartList: [],
        cartIds: '', //购物车Ids
        ifcart: 1,
        extend: [],
        userMsg: '',
        totalNum: 0,
        nums: 1,
        showPay: false,
        order_id: '',
        totalFee: 0,
        totalMoney: 0,
        join_order_id:0, //加入的团订单id
        order_type:1,   //订单类型
        showCoupon:false,
        coupon_id:0, //优惠券id,
        coupons:[], //可用优惠券
        couponMoney:0,
      }
    },
    created() {
      this.cartIds = this.$route.query.cart_ids;
      this.ifcart = this.$route.query.ifcart;
      this.nums = this.$route.query.nums ? this.$route.query.nums : 1;
      this.order_type = this.$route.query.order_type?this.$route.query.order_type:this.order_type;
      this.join_order_id = this.$route.query.join_order_id?this.$route.query.join_order_id:0;
      this.$nextTick(() => {
        this._init_data();
      })
    },
    methods: {
      _init_data() {
        console.log(this.ifcart);
        let params = {
          cart_ids: this.cartIds,
          ifcart: this.ifcart,
          nums: this.nums,
        };
        this.$post_('order/cart/select_cart', params, (res) => {
          this.ifload = false;
          if (res.code == '0') {
            this.cartList = res.data;
            this.extend = res.extend;
            this.coupons = res.extend.coupons;
            this.calculatePrice();
          } else {
            this.$router.push('/');
          }

        });
      },
      calculatePrice() {
        this.totalNum = 0;
        this.totalMoney = 0;
        for (var index in this.cartList) {
          let price = this.cartList[index]['goods_price'];
          let nums = this.cartList[index]['goods_num'];
          this.totalMoney += price * nums;
          this.totalNum += parseInt(nums);

        }
        this.totalMoney += parseInt(this.extend.freight);
        this.totalMoney -= this.couponMoney;
      },

      //选择优惠券
      checkCoupon(curCoupon) {
        console.log(curCoupon);
        this.showCoupon = false;
        if(curCoupon.id){
          this.coupon_id = curCoupon.id;
          this.couponMoney = curCoupon.coupons_money;
          this.calculatePrice();
        }else{
          this.coupon_id = 0;
        }
      },

      //提交订单
      create_order() {
        let params = {
          cart_ids: this.cartIds,
          address_id: this.extend.address.address_id,
          message: this.userMsg,
          order_type:this.order_type,
          join_order_id:this.join_order_id,
          freight:this.extend.freight,
          coupon_id:this.coupon_id,
        };
        console.log(params);
        this.ifload = true;
        this.$post_('order/buy/create_order', params, (res) => {
          console.log(res);
          this.ifload = false;
          if (res.code == '0') {
            this.msg = res.msg;
            this.showAlert = true;
            this.order_id = res.data.order_id;
            this.totalFee = res.data.total_fee;
            this.selectPay();
          } else {
            this.msg = res.msg;
            this.showAlert = true;
            // this.url = '/page/order/cart_list';
          }
        });
      },
      selectPay() {
        this.showPay = !this.showPay;
      },
      closePay() {
        this.selectPay();
        this.$router.push({
          path: '/page/order/order_list',
          query: {
            type: '1'
          }
        });
      },
      payAfter() {
        setTimeout(() => {
          this.showPay = !this.showPay;
          this.$router.push({
            path: '/page/order/order_list',
            query: {
              type: '2'
            }
          });
        }, 1000)
      }
    },
  }
</script>

<style type="text/css" scoped>
  .content {
    position: fixed;
    top: 40px;
    bottom: 60px;
    left: 0px;
    width: 100%;
    overflow: scroll;
    background: #f0f0f0;
  }

  .address {
    display: flex;
    width: 100%;
    padding: 10px;
    background: #fff;
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
  }

  .address .info .user span {
    display: inline-block;
    width: 49%;
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

  .add {
    text-align: 40px;
    background: #fff;
    line-height: 40px;
    letter-spacing: 2px;
  }

  .add .iconfont {
    font-size: 24px;
    vertical-align: middle;
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

  .coupons{
    line-height: 40px;
    height: 40px;
    background: #fff;
    margin-top: 1px;
    display: flex;
  }
  .coupons span{
    flex: 1;
  }
  .coupons .name{
    text-align: left;
    text-indent:10px;
  }
  .coupons .value{
    text-align: right;
    padding-right: 10px;
  }
  .coupons .active{
    color:#FF0036;
  }

  .extra {
    background: #fff;
    margin-top: 1px;
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

  .settlement {
    width: 100%;
    position: fixed;
    bottom: 0px;
    left: 0px;
    height: 60px;
    line-height: 60px;
    background: #fff;
    border-top: 1px solid #ddd;
    color: #000;
    display: flex;
    padding: 0px;
  }

  .settlement .item {
    margin: 0px;
    font-size: 14px;
    flex: 1;
    height: 60px;
    letter-spacing: 1px;
  }

  .settlement .submit {
    background: #FF0036;
    color: #fff;
    letter-spacing: 3px;
  }

  .settlement .total b {
    color: #FF0036;
  }
</style>
