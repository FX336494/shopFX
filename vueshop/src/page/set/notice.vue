<template>
  <div class="wrapper">
    <topTile :name="headerName" :showBack="true"></topTile>
    <scroll ref="scroll" @scroll="scroll" :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
      <div class="content">
        <div class="list">
          <router-link :to="{path:'/page/set/notice_detail',query:{id:item.id}}" tag='div' class="item" v-for="(item,index) in list"
            :key="index">
            <div class="title">{{item.title}}</div>
            <div class="desc">{{item.desc}}</div>
            <div class="info">
              <p class="time">{{item.update_time*1000 | formatDate}}</p>
            </div>
            <i class="iconfont icon-right"></i>
          </router-link>
        </div>

      </div>
    </scroll>
  </div>
</template>

<script type="text/javascript">
  import {formatDate} from '@/components/js/common.js'
  import topTile from '@/components/common/top_title'
  import scroll from '@/components/common/scroll'
  export default {
    components: {
      topTile,
      scroll
    },
    data() {
      return {
        headerName: '公告列表',
        BSstyle: "position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
        list: [],
      }
    },
    created() {
      this.$nextTick(() => {
        this._get_notices();
      })
    },
    actived() {

    },
    methods: {
      _get_notices() {
        this.$post_('platform/article/article_list', {}, (res) => {
          console.log(res);
          this.list = res.data;
        });
      },
      scroll() {

      }
    },
    filters: {
      formatDate(time) {
        let date = new Date(time);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    }
  }
</script>

<style type="text/css" scoped>
  .content,
  .list,
  .item {
    width: 100%;
  }

  .content .list .item {
    position: relative;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
    text-indent: 5px;
    background: #fff;
    padding: 5px;
  }

  .content .list .item .title {
    font-size: 16px;
    font-weight: 600;
    width: 95%;
  }

  .content .list .item p {
    margin: 0px;
    line-height: 20px;
    font-size: 12px;
  }

  .content .list .item .desc {
    width: 95%;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
  }

  .content .list .item .iconfont {
    position: absolute;
    left: 90%;
    top: 13px;
    font-size: 30px;
  }
</style>
