<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <scroll ref="scroll" @scrollEnd="scroll_end" :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
      <div class="content">
        <div class="list">
          <div class="item" v-for="item in items">
            <div class="item_l">
              <img :src="item.member_avatar">
            </div>
            <div class="item_r">
              <div class="name">{{item.member_name}}</div>
              <div class="t">{{item.goods_name}}</div>
              <div class="eval_text" v-show="item.content">{{item.content}}</div>
              <div class="eval_text" v-show="!item.content">此用户没有填写评价</div>
              <div class="eval_img" v-for="img in item.eval_image">
                <img :src="img" />
              </div>
            </div>
          </div>
        </div>
        <div class="more" v-if="more">下拉加载显示更多...</div>
        <div class="more" v-if="!more">我也是有底线的<i class="iconfont icon-meiyoudingdan-01"></i></div>
      </div>
    </scroll>
    <loading :ifload="ifload"></loading>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import scroll from '@/components/common/scroll'
  import loading from '@/components/common/loading'
  export default {
    components: {
      topTile,
      scroll,
      loading
    },
    data() {
      return {
        headerName: '商品评价',
        ifload: false,
        BSstyle: "position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
        goods_commonid: 0,
        type: '0',
        items: [],

        page: 1,
        page_size: 2,
        more: true,
      }
    },
    created() {
      this.goods_commonid = this.$route.query.goods_commonid;
      this.$nextTick(() => {
        this._get_evaluate();
      })
    },
    methods: {
      _get_evaluate() {
        if(!this.more) return;
        let param = {
          goods_commonid: this.goods_commonid,
          type: this.type,
          page: this.page,
          page_size: this.page_size,
        };
        this.ifload = true;
        this.$post_('goods/goods/goods_evaluate_list', param, (res) => {
          console.log(res);
          if (res.code == '0' && res.data.length) {
            res.data.forEach((val) => {
              this.items.push(val);
            })
          } else {
            this.more = false;
          }
          this.ifload = false;
          this.page++;
        })
      },
      scroll_end(pos, maxScrollY) {
        if (maxScrollY == pos) {
          //触发下拉加载
          this._get_evaluate();
        }
      },
    }
  }
</script>

<style type="text/css" scoped>
  .list {
    width: 100%;
    padding: 10px;
    background: #fff;
  }

  .list .item {
    width: 100%;
    font-size: 0px;
    margin-top: 20px;
  }

  .list div:first-child {
    margin-top: 0px;
  }

  .list .item .item_l {
    width: 10%;
    display: inline-block;
    vertical-align: top;
  }

  .list .item .item_l img {
    width: 100%;
    border-radius: 50%;
  }

  .list .item .item_r {
    width: 90%;
    padding-left: 10px;
    display: inline-block;
    font-size: 13px;
    text-align: left;
  }

  .list .item .item_r .name {
    font-size: 14px;
    font-weight: bold;
  }

  .list .item .item_r .t {
    color: gray;
  }

  .list .item .item_r .eval_text {
    margin-top: 10px;
  }

  .list .item .item_r .eval_img {
    width: 100%;
  }

  .list .item .item_r .eval_img img {
    width: 25%;
  }

  .more {
    height: 40px;
    line-height: 40px;
  }
</style>
