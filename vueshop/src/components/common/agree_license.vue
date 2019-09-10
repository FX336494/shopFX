<template>
  <div class="bg_color" v-show="ifshow">
    <div class="alert_">
      <div class="hint_content">
        <div style="font-weight:600;font-size:17px;" v-html="agreement_tile"></div>
        <div style="font-size:15px;" v-html="agreement_context"></div>
      </div>
    </div>
    <div class="back_reg" @click="back_reg">
      返回注册
    </div>
  </div>
</template>
<script>
import {post_} from '@/components/js/common'  
  export default {
    props: {
      ifshow: {
        type:Boolean,
        default:true
      },
      type:{
        type:Number,
        default:1
      }
    },
    data(){
      return {
        agreement_tile:'',
        agreement_context:''
      }
    },
    created(){
      this.init()
    },
    methods:{
      init(){
        var data = {
          type:this.type
        }
        post_("common/agreement",data,(res)=>{
          console.log(res)
            this.agreement_context = res.data.content
            this.agreement_tile = res.data.title
        })
      },
      back_reg(){
        this.$emit("backToreg")
      }
    }
  }
</script>
<style scoped>
.hint_content{
 
}
.back_reg{
  position: absolute;
  bottom: 40px;
  width: 80%;
  left: 10%;
  border:1px solid #fff;
  color:#fff;
  line-height: 45px;
  text-align: center;
  font-size: 17px;
}
.bg_color{
  position: fixed;
  z-index: 9999;
  top:0;
  bottom: 0;
  width: 100%;
  background: rgba(0,0,0,0.8);
}
.alert_{
  background: #fff;
  position: fixed;
  width: 90%;
  left: 5%;
  top:20px;
  bottom:100px;
  padding:15px;
  border-radius: 20px;
  overflow: auto;
}
.clear{
  clear:both;
}    
</style>
