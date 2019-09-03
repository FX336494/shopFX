<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="content">
      <div class="list">
        <div class="item">
          <div class="goods_item" v-for="(goods,index) in order.order_goods" :key="index">
            <div class="goods">
              <div class="info">
                <img :src="goods.goods_image" />
                <span class="text">描述相符</span>
                <div class="star" @click="selectit(index)">
                  <star @starNums="starNums"></star>
                </div>
              </div>
            </div>

            <div class="eval_info">
              <textarea v-model='order.order_goods[index].eval_text' placeholder="亲，写点什么吧，您的意见对其他买家有很大帮助！">

							</textarea>
              <div class="eval_image">
                <span class="item" v-for="(imgurl,i) in goods.eval_image" :key="i">
                  <img :src="imgurl" />
                </span>
                <span class="item" @click="selectit(index)">
                  <upload :uploadType="'2'" @showImg="showImg"></upload>
                </span>
              </div>
            </div>
          </div>
          <div class="sub">
            <button class="btn" @click="_submit">发布</button>
          </div>
        </div>
      </div>
    </div>
    <loading :ifload="ifload"></loading>
    <fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :clsType="clsCode" :url="url">
    </fadeAlert>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import star from '@/components/common/star'
  import upload from '@/components/common/upload'
  import loading from '@/components/common/loading'
  import fadeAlert from '@/components/common/fadealert'
  export default {
    components: {
      topTile,
      star,
      upload,
      loading,
      fadeAlert
    },
    data() {
      return {
        headerName: '发表评价',
        ifload: true,
        showAlert: false,
        msg: '',
        clsCode: 3,
        url: '',
        orderId: 0,
        order: [],
        eval_text: [],
        curIndex: '',
      }
    },
    created() {
      this.orderId = this.$route.query.order_id;
      this.$nextTick(() => {
        this._get_order();
      });
    },
    methods: {
      _get_order() {
        this.$post_('order/order/get_order_info', {order_id: this.orderId}, (res) => {
          console.log(res);
          this.order = res.data;
          this.order.order_goods.forEach((v, k) => {
            this.$set(v, 'eval_image', '');
            this.$set(v, 'eval_text', '');
            this.$set(v, 'scores', '5');
            this.order.order_goods[k] = v;
          })
          this.ifload = false;
        });
      },
      starNums(starNums) {
        console.log(starNums);
        this.$nextTick(() => {
          let index = this.curIndex;
          this.order.order_goods[index].scores = starNums;
        })

      },
      showImg(imgUrl) {
        let index = this.curIndex;
        if (!this.order.order_goods[index].eval_image) {
          console.log(imgUrl);
          let img = [imgUrl];
          this.order.order_goods[index].eval_image = img;
        } else {
          this.order.order_goods[index].eval_image.push(imgUrl)
        }
        console.log(this.order);

      },
      selectit(index) {
        this.curIndex = index;
      },
      //发布评价
      _submit() {
        this.ifload = true;
        let goodsData = this.order.order_goods;
        let goodsOrder = JSON.stringify(goodsData);
        let params = {
          order_id: this.order.order_id,
          store_name: this.order.store_name,
          order_goods: goodsOrder
        };
        // console.log(params);return;
        this.$post_('order/evaluate/add_eval', params, (res) => {
          console.log(res);
          this.showAlert = true;
          this.msg = res.msg;
          if (res.code == '0') {
            this.url = '/page/member/index';
            this.clsCode = 2;
          }
        });
      }
    }
  }
</script>

<style type="text/css" scoped>
  .content {
    position: absolute;
    top: 45px;
    bottom: 0px;
    left: 0px;
    width: 100%;
    overflow: scroll;
    background: #f0f0f0;
  }

  .list {
    width: 100%;
  }

  .list .item {
    width: 95%;
    margin: 10px auto;


    border-radius: 10px;
    height: 100%;
    display: block;
    clear: both;
  }

  .list .item .store {
    line-height: 30px;
    font-size: 14px;
    text-align: left;
    text-indent: 10px;
    position: relative;
    background: #fff;
  }

  .list .item .store .state_text {
    position: absolute;
    right: 5px;
    top: 0px;
  }

  .list .item .goods_item {
    border-bottom: 10px solid #f0f0f0;
    background: #fff;
    padding: 1px 5px 8px 5px;
  }

  .list .item .goods {
    display: flex;
    width: 100%;
    margin-top: 5px;
  }

  .list .item .goods .info {
    flex: 5;
    /*background: orange;*/
    text-align: left;
    display: block;
    padding: 10px 0px;
    width: 100%;
    max-height: 80px;
    border-bottom: 1px solid #f0f0f0;
  }

  .list .item .goods .info img {
    width: 8%;
    vertical-align: middle;
    display: inline-block;
  }

  .list .item .goods .info .text {
    font-size: 12px;
    display: inline-block;
    padding: 0px 5px;
  }

  .list .item .goods .info .star {
    display: inline-block;
  }

  .list .item .eval_info {
    width: 100%;
  }

  .list .item .eval_info textarea {
    width: 100%;
    height: 80px;
    margin-top: 10px;
    border: none;
    padding: 8px;
  }

  .list .item .eval_info .eval_image {
    width: 100%;
    text-align: left;
    font-size: 10px;
  }

  .list .item .eval_info .eval_image .item {
    display: inline-block;
    width: 25%;
    /* height: 60px; */
    /*background: red;*/
    overflow: hidden;
    text-align: center;
    vertical-align: middle;
  }

  .list .item .eval_info .eval_image .item img {
    width: 100%;

  }

  .list .sub {
    background: #fff;
    width: 100%;
    margin-top: -10px;
    height: 60px;
  }

  .list .sub .btn {
    background: #FF0036;
    color: #fff;
    letter-spacing: 8px;
  }
</style>
