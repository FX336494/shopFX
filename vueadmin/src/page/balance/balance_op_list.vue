<template>
    <div>
	 	<div class="container" style="border: none;">

			<el-table
				:data="list"
				border
				v-loading="ifload">
				<el-table-column
					prop="member_id"
					label="会员ID"
                    align="center"
					width="120">
				</el-table-column>
				<el-table-column
					prop="member_name"
                    align="center"
					label="会员姓名"
					width="130">
				</el-table-column>
				<el-table-column
					prop="nums"
                    align="center"
					label="余额数量"
                    sortable
					width="100">
				</el-table-column>
				<el-table-column
					prop="type"
                    align="center"
					label="操作类型"
					width="100">
					<template slot-scope="scope">
						<span>{{scope.row.type=='1'?'增加':'减少'}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="balance_type"
                    align="center"
					label="账本类型"
					width="100">
					<template slot-scope="scope">
						<span>{{typeName[scope.row.balance_type]}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="operator"
                    align="center"
					label="操作人"
					width="100">
				</el-table-column>
				<el-table-column
					prop="create_time"
                    align="center"
					label="操作时间">
					<template slot-scope="scope">
						<span>{{scope.row.create_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>

			</el-table>

            <div class="pagination">
                <el-pagination background
					@current-change="handleCurrentChange"
					layout="prev, pager, next"
					:total="pages"
					:page-size="page_size">
                </el-pagination>
            </div>
		</div>

 	</div>
</template>

<script type="text/javascript">
export default{
	data() {
		return {
			ifload:true,
            avatarDefault:require("../../assets/avatar_default.png" ),
			list:[],
            typeName:[],

			page:1,
			page_size:10,
			pages:0,
		}
	},
	created() {
		this.$nextTick(()=>{
			this.getData();
		})
	},
	computed:{
	},
	methods:{
		getData() {
			let params = {
				page:this.page,
				page_size:this.page_size,
			}
			this.ifload = true;
			this.$post_('finance/balance/balance_op_list',params,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
					this.pages = Number(res.extend.pages);
                    this.typeName = res.extend.type_name;
					this.ifload = false;
				}
			});
		},
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },
        //余额明细
        opBalance() {
            this.$router.push({path:'/page/balance/'});
        },

	}
}
</script>

<style type="text/css">
    thead tr th{
        text-align: center;
    }
    .red{
        color: #ff0000;
    }
	.search{
		margin-bottom: 10px;
	}
</style>
