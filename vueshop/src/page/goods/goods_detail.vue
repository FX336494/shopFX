<template>
  <div class="wrappers">
    <div class="header">
      <div class="title" ref="title">
        <div class="item">
          <span class="icon" @click="$router.go(-1)"><i class="iconfont icon-left"></i></span>
        </div>
        <div class="item img" ref="titleGoodsImage">
          <img :src="goods.goods_image" />
        </div>
        <div class="item" @click="to_cart">
          <span class="icon"><i class="iconfont icon-gouwuche"></i></span>
        </div>
      </div>

      <div class="goods_nav" ref="goods_nav">
        <div class="item" @click="tab_select(1)">
          <span :class="titleTab==1?'active':''"><i v-show="titleTab==1" class="iconfont icon-icon-test "></i>宝贝</span>
        </div>
        <div class="item" @click="tab_select(2)">
          <span :class="titleTab==2?'active':''"><i v-show="titleTab==2" class="iconfont icon-icon-test"></i>评价</span>
        </div>
        <div class="item" @click="tab_select(3)">
          <span :class="titleTab==3?'active':''"><i v-show="titleTab==3" class="iconfont icon-icon-test"></i>详情</span>
        </div>
      </div>
    </div>
    <scroll ref="scroll" @scroll="scroll" :ifloaded="ifloaded" :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
      <div class="content" ref="content">
        <div ref="part1">
          <div class="slide_img">
            <carousel>
              <div class="swiper-slide" v-for="(item,index) in productImgs" :key="index">
                <img :src="item" />
              </div>
            </carousel>
          </div>

          <div class="goods_info">
            <div class="name">
              <p>{{goods.goods_name}}</p>
            </div>
            <div class="price">
              <span>
                ￥<b>{{goods.goods_price}}</b>
                <p class="del">￥{{goods.goods_marketprice}}</p>
              </span>
              <span>销量：{{goods.goods_salenum}}</span>
            </div>
            <div class="extra">
              <div class="item">
                <span>
                  运费：{{goods.goods_freight}}
                </span>
              </div>
              <div class="item">
                <span>浏览量：{{goods.goods_click}}</span>
              </div>
            </div>
          </div>
        </div>

        <div ref="part2">
          <div class="evaluate_title" ref="evaluate">
            <!-- <p><i class="iconfont icon-daohangpingjia"></i>评价</p> -->
          </div>
          <div class="evaluate_content">
            <div class="title">
              <span>宝贝评价({{goods.evaluate_nums}})</span>
              <span class="more" @click="evaluate_list">
                查看全部
                <i class="iconfont icon-right"></i>
              </span>
            </div>

          </div>
        </div>
        <div ref="part3">
          <div class="detail_title" ref="detail">
            <p><i class="iconfont icon-xiangqing"></i>商品详情</p>
          </div>
          <div id="goods_detail">
            <div v-html="goods.goods_body"></div>
          </div>

        </div>

        <!-- <div style="height:50px;"></div> -->
      </div>
    </scroll>
    <div class="footer_cart">
      <div class="rows others">
        <div class="item" @click="collect" :class="goods.is_collect=='1'?'active':''">
          <i class="iconfont icon-shoucang1"></i>
          <br />
          <span class="text"><span v-show="goods.is_collect=='1'">已</span>收藏</span>
        </div>
        <div class="item" @click="to_cart">
          <i class="iconfont icon-gouwuche">
            <!-- <span class="nums">{{$store.state.cartnum}}</span> -->
          </i>
          <br />
          <span class="text">购物车</span>
        </div>
      </div>
      <div class="rows cart" @click="joinCart">加入购物车</div>
      <div class="rows buy" @click="buyNow">立即购买</div>
    </div>

    <!-- 规格组件 -->
    <specSelect v-if="showCart" @hiddenSpec="hiddenSpec" @cartEmit="cartAfer" :buy_type="buyType" :goods="goods">
    </specSelect>

    <fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :clsType="clsCode"></fadeAlert>
    <loading :ifload="ifload"></loading>


  </div>
</template>

<script type="text/javascript">
  import scroll from '@/components/common/scroll'
  import carousel from '@/components/common/carousel'
  import fadeAlert from '@/components/common/fadealert'
  import specSelect from '@/components/goods/spec_select'
  import loading from '@/components/common/loading'
  import Vue from 'vue'
  export default {
    components: {
      scroll,
      carousel,
      specSelect,
      fadeAlert,
      loading
    },
    data() {
      return {
        goodsCommonid: 0,
        goodsId: 0,
        goods: [],
        productImgs: [],
        ifloaded: false,
        topImg: false,
        BSstyle: "position:absolute;top:0;bottom:50px;left:0px;width:100%;overflow:hidden;",
        titleTab: 1,
        showCart: false,
        buyType: 1, //购买方式 1 加入购物车 2直接购买
        msg: '加入购物车成功',
        showAlert: false,
        clsCode: 1,
        ifload: true,
      }
    },
    created() {
      console.log(this.$store.state);
      this.goodsCommonid = this.$route.query.goods_commonid;
      this.$nextTick(() => {
        this._getGoodsInfo();
      })
    },
    methods: {
      _getGoodsInfo() {
        console.log('获取产品信息');
        let params = {
          goods_commonid: this.goodsCommonid
        };
        console.log(params);
        this.$post_('goods/goods/goods_detail', params, (res) => {
          console.log(res);
          this.ifload = false;
          this.goods = res.data;
          res.data.images.forEach((val) => {
            this.productImgs.push(val.image_url);
          });
        });
      },
      scroll(pos, maxScrollY) {
        //头部样式控制
        this.contentStyle(pos);
      },
      contentStyle(pos) {
        let posY = Math.abs(pos.y);
        // console.log(posY);
        if (pos.y <= -0) {
          let showVal = 0.005 * posY;
          this.$refs.goods_nav.style.opacity = showVal;
          this.$refs.title.style.background = "rgba(255,255,255," + showVal + ")";
          this.$refs.titleGoodsImage.style.opacity = showVal;

          let part1 = this.$refs.part1.clientHeight;
          let part2 = this.$refs.part2.clientHeight;
          let part3 = this.$refs.part3.clientHeight;

          if (posY > part1 - 100 && posY < part1 + part2 - 100) {
            this.titleTab = 2;
          } else if (posY > part1 + part2 - 100) {
            this.titleTab = 3;
          } else {
            this.titleTab = 1;
          }
        }
      },

      tab_select(tab) {
        this.titleTab = tab;
        if (tab == 1) {
          this.$refs.scroll.scrollToElement(this.$refs.content, 1, false, -0)
        } else if (tab == 2) {
          this.$refs.scroll.scrollToElement(this.$refs.evaluate, 1, false, -90)
        } else if (tab == 3) {
          this.$refs.scroll.scrollToElement(this.$refs.detail, 1, false, -90)
        }
      },
      joinCart() {
        this.buyType = 1;
        this.showCart = !this.showCart;
      },
      buyNow() {
        this.buyType = 2;
        this.showCart = true;
      },
      hiddenSpec() {
        this.showCart = false

      },
      cartAfer(param) {
        this.hiddenSpec();
        this.showAlert = true;
        this.clsCode = param.code > 0 ? 3 : 2;
        this.msg = param.msg;
      },
      to_cart() {
        this.$router.push('/page/order/cart_list')
      },
      collect() {
        let oldCollet = this.goods.is_collect;
        this.$post_('goods/goods/favorites_goods', {goods_commonid: this.goodsCommonid}, (res) => {
          console.log(res);
          if (res.code == '0') {
            this.showAlert = true;
            this.msg = res.msg;
            let newVal = (oldCollet == '1') ? '0' : '1';
            Vue.set(this.goods, 'is_collect', newVal);
          }
        });
      },
      evaluate_list() {
        this.$router.push({
          path: '/page/goods/goods_evaluate',
          query: {
            goods_commonid: this.goodsCommonid
          }
        });
      }
    },
  }
</script>

<style type="text/css" scoped>
  .iconfont {
    vertical-align: middle;
  }

  .wrappers {
    width: 100%;
    height: 100%;
  }

  .wrappers .header {
    position: fixed;
    top: 0px;
    left: 0px;
    width: 100%;
    z-index: 99999;
  }

  .wrappers .goods_nav,
  .wrappers .title {
    display: flex;
    width: 100%;
  }

  .wrappers .goods_nav {
    height: 30px;
    line-height: 30px;
    background: #fff;
    opacity: 0;
    display: hidden;
    border-bottom: 1px solid #ddd;
  }

  .goods_nav .item .active {
    color: orange;
  }

  .wrappers .title {
    height: 60px;
    line-height: 60px;
    background: rgba(255, 255, 255, 0);

  }

  .header .title .icon {
    background: rgba(0, 0, 0, 0.3);
    display: inline-block;
    width: 30px;
    height: 30px;
    margin-top: 10px;
    line-height: 30px;
    color: #fff;
    border-radius: 15px;
  }

  .goods_nav .item {
    flex: 1;
    font-size: 12px;
  }

  .title .item {
    flex: 1;
  }

  .title .img {
    flex: 3;
    height: 60px;
    opacity: 0;
  }

  .title .img img {
    height: 50px;
  }

  .content {
    width: 100%;
    /*height: 100%;*/
    margin: 0;
    padding: 0;
    /*border:3px solid green;	*/
  }

  .content .slide_img {
    width: 100%;
    z-index: 9;
    /* height: 250px; */
    /* overflow:hidden; */
  }

  .content .goods_info {
    margin-top: 10px;
    border-top: 1px solid #f0f0f0;
    padding-top: 8px;
    text-align: left;
    text-indent: 15px;
    padding-bottom: 10px;
  }

  .content .goods_info .price {
    width: 100%;
    display: flex;
    color: orange;
  }

  .content .goods_info .price .del {
    text-decoration: line-through;
    color: #888;
    font-size: 12px;
  }

  .content .goods_info .price span {
    width: 100%;
    display: inline-block;
    flex: l;
  }

  .content .goods_info .price span b {
    font-size: 20px;
  }

  .content .goods_info .price span:last-child {
    text-align: right;
    padding-right: 15px;
    color: #000;
  }

  .content .goods_info .extra {
    width: 100%;
    display: flex;
    /*text-align: center;*/
  }

  .content .goods_info .extra .item {
    flex: 1;
    width: 100%;
  }

  .content .goods_info .extra div:last-child {
    padding-right: 15px;
    text-align: right;
  }

  .evaluate_title {
    width: 100%;
    height: 10px;
    text-align: center;
    background: #f0f0f0;
    letter-spacing: 3px;
  }

  .evaluate_content {
    width: 100%;
    text-align: left;
    text-indent: 10px;
    font-size: 14px;
    line-height: 30px;
  }

  .evaluate_content .title {
    width: 100%;
    position: relative;
  }

  .evaluate_content .title .more {
    position: absolute;
    top: 0px;
    right: 15px;
    color: #FF0036;
    display: inline-block;
  }

  .detail_title {
    width: 100%;
    height: 40px;
    line-height: 40px;
    text-align: center;
    background: #f0f0f0;
    letter-spacing: 3px;
  }

  #goods_detail {
    padding: 15px;
    width: 100%;
  }

  #goods_detail>>>img {
    width: 100%;
  }

  .footer_cart {
    position: fixed;
    width: 100%;
    height: 60px;
    bottom: 0px;
    left: 0px;
    background: #fff;
    border-top: 1px solid #f0f0f0;
    display: flex;
    padding: 0px;
  }

  .footer_cart .rows {
    display: inline-block;
    width: 100%;
    flex: 1;
  }

  .footer_cart .others .item {
    width: 40%;
    display: inline-block;
    margin: 0px;
    padding-top: 3px;
    font-size: 10px;
  }

  .footer_cart .others .item .iconfont {
    font-size: 25px;
  }

  .footer_cart .others .active {
    color: red;
  }

  .footer_cart .others .icon-gouwuche {
    position: relative;
  }

  .footer_cart .others .nums {
    position: absolute;
    top: -2px;
    right: -5px;
    font-size: 8px;
    width: 18px;
    height: 18px;
    line-height: 18px;
    border-radius: 9px;
    background: #fff;
    color: #FF0036;
    border: 1px solid #FF0036;
  }

  .footer_cart .cart,
  .footer_cart .buy {
    line-height: 60px;
    background: #FF9500;
    font-size: 14px;
    color: #fff;
    letter-spacing: 2px;
  }

  .footer_cart .buy {
    background: #FF0036;
  }
</style>
