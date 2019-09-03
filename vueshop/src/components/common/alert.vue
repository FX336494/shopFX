<template>
  <div class="bg_color" v-show="ifshow">
    <div class="alert_">
      <div class="top_hint">
        <b>提示：</b>
      </div>
      <div class="hint_content">{{content}}</div>
      <div class="bottom_btn">
        <div class="del_btn" @click="del_">取消</div>
        <div class="sure_btn" @click="sure_">确定</div>
        <div class="clear"></div>
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
      del_(){
        this.$emit("callback_",0)
      },
      sure_(){
        this.$emit("callback_",1)
      }
    }
  }
</script>
<style scoped>
.bottom_btn{
  margin-top: 10px;
  font-size: 16px;
}
.del_btn{
  float: left;
  color:red;
}
.sure_btn{
  float: right;
  color:green;
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
