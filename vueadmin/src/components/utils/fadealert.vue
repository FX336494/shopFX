<template>
	<div>
		<transition name="fade">
			<div v-if="shows" id="layer">
			    <span class="msg" :class="perCls" >
			    	<i id="icon" v-show="clsType>1" :class="iconCls"></i>
			    	<span class="text">{{msg}}</span>
			    </span>
			</div>
		</transition>
	</div>
</template>

<script>
export default{
	props:{
		msg:{type:String,default:'提示信息'},
		clsType:{type:Number,default:1},
		url:{type:String,default:''},
	},
	data() {
		return {
			shows:false,
			perCls:'default',
			iconCls:'',
		}
	},
	created() {
		this._init_class();

		this.shows = true;
		var intervar_num = '1'
		var timer = setInterval(()=>{
			if(intervar_num == 0){
				clearInterval(timer)
				this.shows = false
				this.$emit('hideFade');
				if(this.url){
					this.$router.push({path:this.url});
				}
			}
			intervar_num --
		},500)		
	},
	methods:{
		_init_class() {
			if(this.clsType==1)
				this.perCls = 'default';
			if(this.clsType==2){
				this.iconCls = "iconfont icon-zhengque";
				this.perCls = 'success';
			}
			if(this.clsType==3){
				this.iconCls = "iconfont icon-cuowu"
				this.perCls = 'error';					
			}
		
		}
	},
}
</script>

<style rel="stylesheet" scoped>
	#layer{
		position: fixed;
		top:45%;
		left: 0px;
		width:100%;
		text-align: center;
		z-index: 9999999;
	}
	#layer .msg{
		padding:7px 10px;
		line-height: 30px;
		display: inline-block;
		background: rgba(0,0,0,0.6);
		color:#fff;
		border-radius: 5px;
	}
	#layer .msg .text{
		display: block;
	}
	#layer .iconfont{
		color:#fff;
		font-size: 40px;
		vertical-align: middle;
	}
	#layer .success{
		background: rgba(2,95,4,0.6);
	}
	#layer .error{
		background: rgba(255,0,0,0.5);
	}

	.fade-enter-active, .fade-leave-active {
	  transition: opacity .5s;
	}
	.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
	  opacity: 0;
	}
</style>