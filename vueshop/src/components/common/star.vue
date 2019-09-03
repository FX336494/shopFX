<template>
	<div class="wrapper">
		<span v-for="star in stars">
			<img :src="star_nums>=star.id?star_on:star_off" :style="'width:'+starsize" @click="score(star)"/>
		</span>
		<span class="desc">{{star_desc[star_nums]}}</span>
	</div>
</template>

<script type="text/javascript">

export default{
	props:{
		starsize:{type:String,default:"20px"}
	},		
	data() {
		return {
			star_nums:5,
			star_on:require('@/assets/images/star_on.png'),
			star_off:require('@/assets/images/star_off.png'),
			stars:[
				{'id':1},
				{'id':2},
				{'id':3},
				{'id':4},
				{'id':5},
			],	
			star_desc:[
				'请评价',
				'非常差',
				'差',
				'一般',
				'好',
				'非常好'
			],
		}
	},
	created() {

	},	
	methods:{
		score(star) {
			this.star_nums = star.id;
			this.$emit("starNums",this.star_nums);
		}

	}
}
</script>

<style type="text/css" scoped>
	.wrapper{
		width: 100%;
		background: #fff;
	}
	.wrapper img{
		margin-left: 5px;
		vertical-align: top;
	}
	.wrapper .desc{
		display: inline-block;
		padding: 0px 8px;
		vertical-align: middle;
	}
</style>