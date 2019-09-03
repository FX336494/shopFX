<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <scroll ref="scroll" @scrollEnd="scrollEnd" :data="dataList" :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
      <div class="content">
        <div class="list">
          <div class="item" v-for="(item,index) in dataList" :key="index">
            <div class="text">
              <span class="title">{{item.type_text}}</span>
              <span class="amount" :class="item.log_type=='1'?'add':'reduce'">
                {{item.log_type=='1'?'+':'-'}}{{item.balance}}
              </span>
            </div>
            <p class="time">2019年8月26日 22:03</p>
          </div>
          <div class="more" v-if="more">下拉加载显示更多...</div>
          <div class="more" v-if="!more">我也是有底线的<i class="iconfont icon-meiyoudingdan-01"></i></div>
        </div>

      </div>
    </scroll>
    <loading :ifload="ifload"></loading>
  </div>
</template>

<script type="text/javascript">
  import topTile from '@/components/common/top_title'
  import loading from '@/components/common/loading'
  import scroll from '@/components/common/scroll'
  export default {
    components: {
      topTile,
      scroll,
      loading,
      scroll
    },
    data() {
      return {
        headerName: '积分明细',
        ifload: false,
        BSstyle: "position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
        dataList: [],
        page: 1,
        pageSize: 2,
        more: true,
      }
    },
    created() {
      this.$nextTick(() => {
        this.getData();
      })
    },
    methods: {
      getData() {
        if (!this.more) return;
        let param = {
          page: this.page,
          page_size: this.pageSize,
        }
        this.ifload = true;
        this.$post_('member/balance/balance_log', param, (res) => {
          console.log(res);
          this.ifload = false;
          if (res.code == '0' && res.data.length > 0) {
            res.data.forEach((val) => {
              this.dataList.push(val);
            })
          } else {
            console.log('没有更多了');
            this.more = false;
          }
          this.page++;
        });
      },

      //下拉加载
      scrollEnd(pos, maxScrollY) {
        if (maxScrollY == pos) {
          this.getData()
        }
      },
    }

  }
</script>

<style type="text/css" scoped>
  .wrapper .content {
    width: 100%;
  }

  .content .list {
    width: 100%;
  }

  .content .list .item {
    width: 100%;
    display: block;
    background: #fff;
    margin-bottom: 1px;
    text-align: left;
    text-indent: 10px;
    padding: 8px 0px 4px 0px;
  }

  .content .list .item .text {
    position: relative;
    width: 100%;
  }

  .content .list .item .text .title {
    font-size: 18px;
    line-height: 30px;
  }

  .content .list .item .text .amount {
    position: absolute;
    right: 10px;
    top: 0px;
    font-size: 16px;
    font-weight: bold;
  }

  .content .list .item .text .add {
    color: green;
  }

  .content .list .item .text .reduce {
    color: red;
  }

  .content .list .item .time {
    font-size: 12px;
    color: #777;
    letter-spacing: 1px;
  }

  .more {
    height: 40px;
    line-height: 40px;
  }
</style>
