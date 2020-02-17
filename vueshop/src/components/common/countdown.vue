<template>
    <div class="box">
      <span class="item" v-show="day>0">{{day}}</span><span v-show="day>0">天</span>
      <span class="item" v-show="hour>0">{{hour}}</span> <span v-show="hour>0">时</span>
      <span class="item">{{minute}}</span>分
      <span class="item">{{second}}</span>秒
    </div>
</template>

<script type="text/javascript">
  var interval
  export default {
    props: {
      endTime: {
        type: Number,
        default: 0
      }, //结束时间戳 毫秒
    },
    data() {
      return {
        headerName: '倒计时',
        intDiff:0,
        day:0,
        hour:0,
        minute:0,
        second:0
      }
    },
    created() {
      console.log(this.endTime);
      this.countDown(this.endTime);
    },
    activated() {
      this.countDown(this.sec);
    },
    methods: {
      countDown(intDiff) {
        interval = setInterval(() => {
          const endTime = new Date(this.endTime)
          const nowTime = new Date();
          let leftTime = parseInt((endTime.getTime() - nowTime.getTime()) / 1000)
          this.day = parseInt(leftTime / (24 * 60 * 60))
          this.hour = this.formate(parseInt(leftTime / (60 * 60) % 24))
          this.minute = this.formate(parseInt(leftTime / 60 % 60))
          this.second = this.formate(parseInt(leftTime % 60))
          if (leftTime <= 0) {
            this.flag = true
            window.clearInterval(interval);
          }
        }, 1000)
      },
			formate(time) {
				if (time >= 10) {
					return time
				} else {
					return `0${time}`
				}
			}
    },
    beforeDestroy() {
      window.clearInterval(interval);
    }
  }
</script>

<style type="text/css" scoped>
  .item{
    background: orange;
    color:#fff;
    padding: 3px 6px;
    font-size: 12px;
    margin-right: 2px;
    border-radius: 3px;
  }
</style>
