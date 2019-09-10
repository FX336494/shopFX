<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<scroll ref="scroll" @scrollEnd="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="content">
				

			</div>
		</scroll>
	</div>
</template>

<script type="text/javascript">
import {post_} from '@/components/js/common.js' 
import topTile from '@/components/common/top_title'
import scroll from '@/components/common/scroll'
export default{
	components: {
      	topTile,
      	scroll
	},			
	data() {
		return {
			headerName:'title',
			BSstyle:"position:absolute;top:40px;bottom:50px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
		}
	},
	created() {

	},
	mounted() {

	},
	actived(){

	},		
	methods:{
		scroll() {

		}
	}
}
</script>

<style type="text/css" scoped>
	
</style>