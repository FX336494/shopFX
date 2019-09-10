<template>
	<div class="title_">
		<span @click="goBack" v-show="showBack" class="glyphicon glyphicon-menu-left"></span>
		<div>{{name}}</div>
	</div>
</template>

<script>
	export default{
		props:{
			name:'',
			showBack:false
		},
		data(){
			return {

			}
		},
		methods:{
			goBack(){
				this.$router.go(-1)
			}
		}
	}
</script>

<style scoped>
	.title_{
		height: 45px;
		line-height: 45px;
		text-align: center;
		color:#000;
		font-size: 16px;
		background: #f0f0f0;
	}
	span{
		position: absolute;
		left: 10px;
		line-height: 45px;
	}
</style>