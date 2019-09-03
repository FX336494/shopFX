<template>
  <div class="bg_color" v-show="ifshow">
    <div class="alert_">
      <div class="top_hint">
        <b>提示：</b>
      </div>
      <div class="hint_content">{{content}}</div>
      <div class="bottom_btn" @click="sure_">
        确定
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props: {
      content: '',
      ifshow: {
        type:Boolean,
        default:false
      }
    },
    data(){
      return {

      }
    },
    methods:{
      sure_(){
        this.$emit("Onlycallback_")
      }
    }
  }
</script>
<style scoped>
.bottom_btn{
  margin-top: 10px;
  width: 90%;
  background: #0aaeef;
  line-height: 40px;
  border-radius: 5px;
  text-align: center;
  color:#fff;
  margin-left: 5%;
}
.hint_content{
  font-size: 15px;
  text-align: left;
  padding-left: 30px;
}
.top_hint{
  text-align: left;
  font-size: 16px;
  line-height: 30px;
}
.bg_color{
  position: fixed;
  z-index: 9999;
  top:0;
  bottom: 0;
  left: 0;
  width: 100%;
  background: rgba(100,100,100,0.5);
}
.alert_{
  background: #fff;
  position: relative;
  width: 80%;
  left: 10%;
  top:150px;
  padding:15px;
}
.clear{
  clear:both;
}
      
</style>
