<template>
  	<div class="recommend" ref="recommend" v-show="showSelectP">
  		<div class="box">
  			<div class="box_top">
  				所在地区
  				<span @click="closebox">X</span>
  			</div>
  			<div class="box_mid">
  				<div @click="sarea(1)">{{area1 || '请选择'}}</div>
  				<div @click="sarea(2)">{{area2?area2:''}}</div>
  				<div @click="sarea(3)">{{area3?area3:''}}</div>
  			</div>
  			<div class="place_list" v-show="shownum == 1">
  				<div :class="place.area_name == area1?'choosed':''" v-for="place in P1" @click="select_place1(place.area_name,place.area_id,2)">
  					{{place.area_name}}
  				</div>
  			</div>
  			<div class="place_list" v-show="shownum == 2">
  				<div :class="place.area_name == area1?'choosed':''" v-for="place in P2" @click="select_place2(place.area_name,place.area_id,3)">
  					{{place.area_name}}
  				</div>
  			</div>
  			<div class="place_list" v-show="shownum == 3">
  				<div :class="place.area_name == area1?'choosed':''" v-for="place in P3" @click="select_place3(place.area_name,place.area_id,4)">
  					{{place.area_name}}
  				</div>
  			</div>
  		</div>
	</div>	
</template>
<script>
import {post_} from '@/components/js/common.js' 
export default{
	props:{
		showSelectP:false
	},
	data (){
		return {
			P1:{},
			P2:{},
			P3:{},
			area1:'',
			area2:'',
			area3:'',
			shownum:1,
			area_id:'',
			city_id:''
		}
	},
	watch:{
		area1(){
			this.area2 = ""
			this.area3 = ""
		},
		area2(){
			this.area3 = ""
		}
	},
	created(){
		this.init()
	},
	methods:{
		init(){
		    post_('order/address/area_list',{parent_id:0,deep:1},(res)=>{
	            this.P1 = res.data
	            //console.log(res)
	        });	
		},
		select_place1(name,id,deep){
			this.province_id = id
			this.shownum = deep
			this.area1 = name
			var data = {
				parent_id:id,
				deep:deep
			}
			post_('order/address/area_list',data,(res)=>{
	            this.P2 = res.data
	        });	
		},

		select_place2(name,id,deep){
			this.city_id = id
			this.shownum = deep
			this.area2 = name
			var data = {
				parent_id:id,
				deep:deep
			}
			post_('order/address/area_list',data,(res)=>{
	            this.P3 = res.data
	            //console.log(this.P3)
	        });	
		},

		select_place3(name,id,deep){
			this.area_id = id
			this.shownum = deep
			this.area3 = name
			var palce_ = this.area1+' '+this.area2+' '+this.area3
			this.$emit('closebox',palce_,this.province_id,this.city_id,id)
		},
		sarea(num){
			this.shownum = num
		},
		closebox(){
			this.$emit('closebox','')
		}
	}
}
</script>

<style scoped>
.choosed{
	color:red;
}
.place_list{
	position: fixed;
	height:320px; 
	bottom: 0;
	width: 100%;
	overflow: auto;
	padding: 10px;
}
.place_list div{
	line-height: 40px;
}
.box_mid{
	line-height: 40px;
	color:red;
	border-bottom: 1px solid #eee;
	display: flex;
	text-align: center;
}
.box_mid div{
	flex: 1;
}
.box_top{
	line-height: 40px;
	text-align: center;
	font-size: 16px;
	border-bottom: 1px solid #eee;
}
.box_top span{
	position: absolute;
	right: 10px;
}
.box{
	position: fixed;
	height: 400px;
	background: #fff;
	width: 100%;
	bottom:0;
}
.recommend{
	position: fixed;
	top:0;
	width: 100%;
	bottom:0;
	z-index: 1000;
	text-align: left;
	background: rgba(0,0,0,0.5);
}
.recommend-content,.slider-wrapper{
	height: 100%;
  	overflow: hidden;
}  
</style>