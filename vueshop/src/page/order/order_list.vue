<template>
  <div class="wrapper">
    <topTitle :name="headerName" :showBack="true"></topTitle>
    <div class="tab">
      <div @click="get_order_list(0)" class="item" :class="type=='0'?'active':''">全部</div>
      <div @click="get_order_list(1)" class="item" :class="type=='1'?'active':''">待付款</div>
      <div @click="get_order_list(2)" class="item" :class="type=='2'?'active':''">待发货</div>
      <div @click="get_order_list(3)" class="item" :class="type=='3'?'active':''">待收货</div>
      <div @click="get_order_list(4)" class="item" :class="type=='4'?'active':''">待评价</div>
    </div>
    <scroll ref="scroll" @scrollEnd="scroll" :data="orderList" :listenScroll="true" :probeType="3" :BSstyle="BSstyle">

      <div class="content">
        <div class="list">
          <div class="item" v-for="(order,index) in orderList" :key="index">
            <div class="store">
              <span>
                订单号:{{order.pay_sn}}
              </span>
              <span class="state_text" v-show="order.evaluation_state=='0'">
                {{stateText[order.order_state]}}
              </span>
              <span class="state_text" v-show="order.evaluation_state=='1'">
                已评价
              </span>
            </div>

            <div class="goods" @click="orderDetail(order)" v-for="(goods,k) in order.order_goods" :key="k">
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

            <div class="extra">
              <div class="desc">
                <div class="promotion" v-if="order.order_type>'1'">
                  <span class="type">
                    {{order.promotion.promotion_text}}
                  </span>
                  <span style="color: orangered;" v-show="order.promotion.promotion_desc">
                    {{order.promotion.promotion_desc}}
                  </span>

                </div>
                <div class="total">
                  <span>运费：{{order.shipping_fee}}</span>
                  <span>优惠券抵扣：{{order.order_common.coupon_info.coupon_money}}</span>
                  <span>合计：{{order.order_amount}}</span>
                </div>
              </div>

              <div class="order_state">
                <span class="cancel" @click="cancel(order,index)" v-show="order.order_state=='1'">
                  取消订单
                </span>
                <span class="pay" @click="pay(order)" v-show="order.order_state=='1'">
                  请付款
                </span>
                <span @click="order_delivery(order)" v-show="order.order_state>='3'">
                  查看物流
                </span>
                <span class="delivery" @click="order_receive(order,index)" v-show="order.order_state=='3'">
                  确认收货
                </span>
                <span class="delivery" @click="order_evaluation(order,index)" v-show="order.order_state=='4' && order.evaluation_state=='0'">
                  我要评价
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="more" v-if="showMore && isMore && haveOrder">下拉加载显示更多...</div>
        <div class="more" v-if="showMore && !isMore && haveOrder">我也是有底线的...</div>

        <div class="noorder" v-if="!haveOrder">
          <div>
            <i class="iconfont icon-meiyoudingdan-01"></i>
          </div>
          你还没有相关订单
        </div>
        <loading :ifload="ifload" v-if="isMore"></loading>
      </div>
    </scroll>
    <pay
      v-if="showPay"
      @closePay="selectPay"
      @payAfter="payAfter"
      :order_id="payOrder.order_id"
    >
    </pay>
    <fadeAlert :msg="msg" v-if="showAlert" @hideFade="hideFade" :clsType="clsCode">
    </fadeAlert>
  </div>
</template>

<script type="text/javascript">
  import scroll from '@/components/common/scroll'
  import topTitle from '@/components/common/top_title'
  import loading from '@/components/common/loading'
  import pay from '@/components/pay/pay'
  import fadeAlert from '@/components/common/fadealert'
  export default {
    components: {
      scroll,
      topTitle,
      loading,
      pay,
      fadeAlert
    },
    data() {
      return {
        headerName: '我的订单',
        BSstyle: "position:absolute;top:80px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
        type: '0', //订单状态
        orderList: [],
        page: 1,
        ifload: false, //显示加载
        haveOrder: true, //是否有订单
        showPay: false, //显示支付
        payOrder: [], //当前支付的订单
        isMore: true, //是否还有更多
        showMore: false, //是否显示下拉加载的文字
        perpage: 5, //每页显示条数
        isPost: false, //是否正在请求
        showAlert: false,
        clsCode: 1,
        msg: '操作成功',
        stateText: [],
        optype: '', //操作类型

      }
    },
    created() {
      this.type = this.$route.query.type;
      this.$nextTick(() => {
        this._init_data();
      })
    },
    mounted() {
      this.$nextTick(() => {
        this._init_data();
      })
    },
    actived() {},
    methods: {
      _init_data() {
        this.get_order_list(this.type);
      },

      get_order_list(type) {
        if (this.type != type) {
          this.orderList = [];
          this.page = 1;
          this.haveOrder = true;
          this.isMore = true;
        }
        this.ifload = true;
        this.type = type;
        let params = {
          state: type,
          page: this.page,
          perpage: this.perpage
        };
        console.log(params);
        if (this.isPost) return true;
        this.isPost = true;
        this.$post_('order/order/order_list', params, (res) => {
          console.log(res);
          this.stateText = res.extend.stateText;
          this.ifload = false;
          let data = res.data;
          if (data.length > 0) {
            data.forEach((val) => {
              this.orderList.push(val);
            })
            this.page++;
            if (data.length < this.perpage) {
              this.isMore = false;
            }
            this.showMore = true;
          } else {
            this.isMore = false;
          }
          if (this.orderList.length < 1) {
            this.haveOrder = false;
          }
          this.isLoad = true;
          this.isPost = false;
        });
      },

      scroll(pos, maxScrollY) {
        if (maxScrollY == pos) {
          this.get_order_list(this.type)
        }
      },
      pay(order) {
        this.selectPay();
        this.payOrder = order
      },
      selectPay(type) {
        this.showPay = !this.showPay;
      },
      payAfter() {
        setTimeout(() => {
          this.showPay = !this.showPay;
          this.get_order_list(2);
        }, 1000)
      },
      //取消订单
      cancel(order, index) {
        // this.orderList.splice(index,1);
        // console.log(this.orderList);
        // return;
        this.$post_('order/order/cancel_order', {
          order_id: order.order_id
        }, (res) => {
          console.log(res);
          if (res.code == '0') {
            this.clsCode = 2;
            this.showAlert = true;
            this.orderList.splice(index, 1);
            if (this.orderList.length < 1) this.haveOrder = false;
          }
        });
      },
      //确认收货
      order_receive(order, index) {
        this.optype = 'order_receive';
        this.$post_('order/order/order_receive', {
          order_id: order.order_id
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

      //订单详情
      orderDetail(order) {
        this.$router.push({
          path: '/page/order/order_detail',
          query: {order_id: order.order_id,type:'1'}
        })
      },

      //查看物流
      order_delivery(order) {
        this.$router.push({
          path: '/page/order/order_delivery',
          query: {
            order_id: order.order_id
          }
        })
      },
      //评价
      order_evaluation(order) {
        this.$router.push({
          path: '/page/order/evaluation',
          query: {
            order_id: order.order_id
          }
        })
      },
      hideFade() {
        this.showAlert = false;
        if (this.optype == 'order_receive') this.get_order_list(4)
      }
    }
  }
</script>

<style type="text/css" scoped>
  .content {
    width: 100%;
    margin-top: 10px;
    /*padding-bottom: 20px;*/
  }

  .tab {
    position: fixed;
    top: 40px;
    left: 0px;
    width: 100%;
    display: flex;
    background: #f0f0f0;
    color: #000;
  }

  .tab .item {
    flex: 1;
    height: 40px;
    line-height: 40px;
  }

  .tab .active {
    border-bottom: 2px solid #FF0036;
  }

  .list {
    width: 100%;
    height: 100%;
  }

  .list .item {
    width: 95%;
    margin: 10px auto;
    padding: 1px 5px 8px 5px;
    background: #fff;
    border-radius: 10px;
    height: 100%;
    display: block;
    clear: both;
  }

  .list .item .store {
    line-height: 30px;
    font-size: 14px;
    text-align: left;
    /*background: red;*/
    text-indent: 10px;
    position: relative;
  }

  .list .item .store .state_text {
    position: absolute;
    right: 5px;
    top: 0px;
  }

  .list .item .goods {
    display: flex;
    width: 100%;
    margin-top: 15px;
  }

  .list .item .goods .info {
    flex: 5;
    /*background: orange;*/
    text-align: left;
    padding: 5px;
  }

  .list .item .goods .info .img {
    display: block;
    float: left;
    width: 30%;
    /* max-height: 80px; */
  }

  .list .item .goods .info .text {
    display: block;
    float: left;
    width: 70%;
    text-align: left;
  }

  .list .item .goods .info img {
    width: 90%;
    vertical-align: middle;
    display: inline-block;
  }

  .list .item .goods .info .text {
    font-size: 14px;
    display: inline-block;
  }

  .list .item .goods .info .price .p1 {
    display: inline-block;
    width: 49%;
    color: #FF0036;
    font-size: 16px;
  }

  .list .item .goods .info .price .num {
    text-align: right;
    display: inline-block;
    width: 49%;
  }

  .list .item .extra {
    width: 100%;
    text-align: right;
  }
  .list .item .extra .desc{
    display: flex;
    width: 100%;
  }
  .list .item .extra .desc div{
    flex: 1;
  }
  .list .item .extra .desc .promotion{
    text-align: left;
    text-indent: 10px;
  }
  .list .item .extra .desc .promotion .type{
    background: orangered;
    color:#fff;
    display: inline-block;
    text-indent: 0px;
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 13px;
  }
  .list .item .extra .desc .total span{
    display: block;
  }

  .list .item .extra .order_state {
    width: 100%;
    margin-top: 10px;
  }

  .list .item .extra .order_state span {
    display: inline-block;
    padding: 5px 13px;
    border: 1px solid #555;
    border-radius: 15px;
  }

  .list .item .extra .order_state .pay {
    color: green;
    border-color: green;
  }

  .list .item .extra .order_state .delivery {
    color: #FF0036;
    border-color: #FF0036;
  }

  .more {
    height: 40px;
    line-height: 20px;
  }

  .noorder {
    height: 200px;
    margin-top: 50px;
    width: 100%;
    text-align: center;
  }

  .noorder .iconfont {
    font-size: 64px;
    color: #FF0036;
  }
</style>
