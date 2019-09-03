<template>
		<div class="put_YZM_btn">
			<div class="btn_text" :class="vocodeDisable?'vocodeAble':'vocodeDisable'" @click="getVcode">
				{{vcode_text}}
			</div>
			<Onlyalert @Onlycallback_="Onlycallback_" :content="Onlycontent" :ifshow="Onlyif_show"></Onlyalert>
		</div>
</template>

<script>
import Onlyalert from '@/components/common/Only_alert'
import {post_} from '@/components/js/common'
export default{
	props:['mobile','type'],
	data() {
		return {
			vcode_text:'获取验证码',
			vocodeDisable:true,		
			Onlyif_show:false,
			Onlycontent:'',
		}
	},
	components:{
		Onlyalert
	},
	methods: {
		getVcode(){
			if(!this.vocodeDisable || this.mobile.length!=11){
				return
			}
			this.vocodeDisable = false
			var intervar_num = '120'
			this.vcode_text = intervar_num +'秒'
			var timer = setInterval(()=>{
				if(intervar_num == 0){
					clearInterval(timer)
					this.vocodeDisable = true
					this.vcode_text="获取验证码"
					return
				}
				intervar_num --
				this.vcode_text = intervar_num +'秒'
			},1000)
			var data = {
	            type:this.type,
	            mobile:this.mobile 
	        };
	        post_('common/sendsms',data,(res)=>{
	        	console.log(res);
	            if(res.code){
	            	this.Onlycontent = res.msg
					this.Onlyif_show = true
					clearInterval(timer)
					this.vocodeDisable = true
					this.vcode_text="获取验证码"

	            }else{
	            	this.Onlycontent = '';
	            	this.Onlyif_show = false;
	            }
	        });
		},	
		Onlycallback_() {
			this.Onlycontent = ''
			this.Onlyif_show = false
			return						
		},
	}
}
</script>

<style rel="stylesheet" scoped>
	.put_YZM_btn{
		/*flex:3;*/
		width: 100%;
	}
	.vocodeDisable{
		background: #f8b4af;
	}
	.vocodeAble{
		background: #dc1b1b;
	}

	.btn_text{
		width: 90%;
		margin-left:5%;
		line-height: 40px;
		margin-top: 5px; 
		border-radius: 7px;
		color: #fff;
	}
</style>