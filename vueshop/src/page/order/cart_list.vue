<template>
  <div class="cart_list">
    <topTile :name="headerName" :showBack="true"></topTile>
    <div class="list">
      <div class="item" v-show="cartList[0]">
        <div class="cart" v-for="(cartItem,index) in cartList">
          <div class="op" @click="select_it(index)">
            <i v-show="!cartItem.is_choose" class="iconfont icon-gouxuan1"></i>
            <i v-show="cartItem.is_choose" class="iconfont icon-gouxuan"></i>
          </div>
          <div class="info">
            <div class="img">
              <img :src="cartItem.goods_image" />
            </div>
            <div class="text">
              <div class="name">{{cartItem.goods_name}}</div>
              <div class="price">
                <div class="p1"> 价格：￥{{cartItem.goods_price}}</div>
                <div class="num">
                  <i class="iconfont icon-jianshao_l" @click="edit_nums(index,-1)"></i>
                  <input type="text" v-model="cartItem.goods_num">
                  <span><i class="iconfont icon-add" @click="edit_nums(index,1)"></i></span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
      <div class="nocart" v-show="isShow && !cartList[0]">
        <i class="iconfont icon-gouwuche"></i>
        <span>购物车竟然是空的</span>
        <p>不要亏待了自己，买买买</p>
        <router-link tag='span' to='/page/index' class="buy">买买买GO</router-link>
      </div>
    </div>

    <div class="settlement" v-if="cartList[0]">
      <div class="item select" @click="select_all">
        <span>
          <i v-show="!isAll" class="iconfont icon-gouxuan1"></i>
          <i v-show="isAll" class="iconfont icon-gouxuan"></i>
          全选
        </span>
      </div>
      <div class="item total">
        <span>合计:</span>
        <span><b>￥{{totalMoney}}</b></span>
      </div>
      <div class="item submit" @click="settlement">结算</div>
    </div>
  </div>
</template>

<script>
  import topTile from '@/components/common/top_title'
  import Vue from 'vue'
  export default {
    components: {
      topTile,
      scroll
    },
    data() {
      return {
        cartList: [],
        headerName: '购物车列表',
        isAll: false, //是否是全选状态
        isShow: false
      }
    },
    created() {
      this._init_data();
    },
    methods: {
      _init_data() {
        this.$post_('order/cart/cart_list', {}, (res) => {
          if (res.code == '0') {
            this.cartList = res.data;
          }
          this.isShow = true;

        });
      },
      //修改数量
      edit_nums(index, num) {

        let curNum = parseInt(this.cartList[index]['goods_num']);
        let newNum = curNum + parseInt(num);
        let params = {
          cart_id: this.cartList[index]['cart_id'],
          nums: newNum
        }
        this.$post_('order/cart/edit_cart', params, (res) => {
          if (res.code == '0') {
            this.cartList[index]['goods_num'] = newNum;
            if (newNum <= 1) this.cartList[index]['goods_num'] = 1;
          }
        });
      },
      //选中
      select_it(index) {

        if (!this.cartList[index]['is_choose']) {
          Vue.set(this.cartList[index], 'is_choose', 1);
        } else {
          Vue.set(this.cartList[index], 'is_choose', 0);
        }
      },
      //全选
      select_all() {
        let choose = !this.isAll;
        for (var index in this.cartList) {
          Vue.set(this.cartList[index], 'is_choose', choose);
        }
        this.isAll = choose;
      },
      //结算
      settlement() {
        let selectIds = [];

        for (var index in this.cartList) {
          if (this.cartList[index].is_choose == '1') {
            selectIds.push(this.cartList[index].cart_id);
          }
        }
        if (selectIds.length < 1) return false;
        let cartIds = selectIds.join('|');
        this.$router.push({
          path: '/page/order/create_order',
          query: {
            cart_ids: cartIds,
            ifcart: 1
          }
        });
      }
    },
    computed: {
      totalMoney() {
        let total = 0;
        let selectNums = 0;
        let totalNums = 0;

        for (var index in this.cartList) {
          totalNums++;
          if (this.cartList[index].is_choose == '1') {
            selectNums++;
            let price = this.cartList[index]['goods_price'];
            let nums = this.cartList[index]['goods_num'];
            total += price * nums
          }
        }
        this.isAll = (selectNums == totalNums && totalNums > 0);
        return total;
      }
    }

  }
</script>

<style rel="stylesheet" scoped>
  html {
    background: #f0f0f0;
  }

  .list {
    position: fixed;
    top: 40px;
    bottom: 50px;
    left: 0px;
    width: 100%;
    overflow: scroll;
    background: #f0f0f0;
  }

  .list .item {
    width: 100%;
    margin-top: 10px;
    padding: 5px 5px;
    background: #fff;
  }

  .list .item .cart {
    width: 100%;
    /*height: 80px;*/
    display: flex;


  }

  .cart_list .item .cart .op {
    flex: 1;
    vertical-align: middle;
    line-height: 80px;
    padding: 10px 0px;
  }

  .cart_list .item .cart .info {
    flex: 5;
    /*background: orange;*/
    text-align: left;
    padding: 10px;
  }

  .cart_list .item .cart .op .iconfont {
    font-size: 24px;
    color: #888;
  }

  .cart_list .item .cart .op .icon-gouxuan {
    color: #FF0036;
  }

  .cart_list .item .cart .info .img {
    display: block;
    float: left;
    width: 30%;
    max-height: 80px;
    overflow: hidden;
  }

  .cart_list .item .cart .info .text {
    display: block;
    float: left;
    width: 70%;
    text-align: left;
  }

  .cart_list .item .cart .info img {
    width: 90%;
    vertical-align: middle;
    /*display: inline-block;*/
    display: table;
  }

  .cart_list .item .cart .info .text {
    font-size: 14px;
    display: inline-block;
  }

  .cart_list .item .cart .info .price .num {
    margin-top: 8px;
  }

  .cart_list .item .cart .info .price input {
    border: 1px solid #ddd;
    width: 30px;
    text-align: center;
    font-size: 15px;
  }

  .cart_list .item .cart .info .price .iconfont {
    font-size: 24px;
    vertical-align: middle;
    color: #000;
  }

  .cart_list .nocart {
    width: 100%;
    margin-top: 50px;
    height: 50px;
    padding: 5px 5px;
    color: #000;
    font-size: 20px;
  }

  .cart_list .nocart .iconfont {
    display: block;
    font-size: 80px;
    color: #FF0036;
  }

  .cart_list .nocart p {
    font-size: 12px;
    color: #555;
    line-height: 30px;
  }

  .cart_list .nocart .buy {
    display: inline-block;
    padding: 4px 18px;
    background: #FF0036;
    color: #fff;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #f0f0f0;
  }

  .settlement {
    width: 100%;
    position: fixed;
    bottom: 50px;
    left: 0px;
    height: 60px;
    line-height: 50px;
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

  .settlement .select .iconfont {
    font-size: 24px;
    /*vertical-align: middle;*/
    color: #000;
  }

  .settlement .select .icon-gouxuan {
    color: #FF0036;
  }

  .settlement .total b {
    color: #FF0036;
  }
</style>
