<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>物流管理</el-breadcrumb-item>
                <el-breadcrumb-item>设置物流公司</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
    
	 	<div class="container">
			<el-row style="text-align: center;padding-bottom: 10px;">
				<el-col :span="18" >
					<h3>设置常用的物流公司</h3>
				</el-col>
				<el-col :span="6">
					<el-button @click="save" type="primary">保存</el-button>
				</el-col>
			</el-row>
			
			<el-checkbox-group v-model="choseList">
				<el-row>
					<el-col :span="4" v-for="(item,i) in data" :key="i" style="line-height: 30px;"> 
						<el-checkbox :label="item.id" >{{item.e_name}}</el-checkbox>
					</el-col>
				</el-row>
				
			</el-checkbox-group>	 	
		</div>
			
		
 	</div>
</template>

<script type="text/javascript">
export default{
	data() {
		return {
			ifload:true,
			data:[],
			choseList:[],
		}
	},
	created() {
		this.orderId = this.$route.query.order_id;
		this.$nextTick(()=>{
			this.getData();
		})
	},
	computed:{
	},
	methods:{
		getData() {
			this.$post_('logistics/express/express_list',{},(res) => {
				console.log(res);  
				if(res.code=='0'){
					this.data = res.data;
					this.data.forEach((val) => {
						if(val.e_chose=='1'){
							this.choseList.push(val.id);
						}
					})
					this.ifload = false;
				}
			});
		},
		save() {
			let choseList = JSON.stringify(this.choseList);
			this.$post_('logistics/express/express_set',{chose:choseList},(res) => {
				if(res.code=='0'){
					this.$message.success(res.msg);
				}else{
					this.$message.error(res.msg);
				}
			});
		}
	}
}
</script>

<style type="text/css">

</style>