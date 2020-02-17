<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>订单管理</el-breadcrumb-item>
                <el-breadcrumb-item>订单列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">

			<div class="search">
				<el-row type="flex"  justify="space-between">
					<el-col :span="3">
						<el-input v-model="search.order_id" placeholder="订单ID" clearable></el-input>
					</el-col>
					<el-col :span="3">
						<el-input v-model="search.buyer_name" placeholder="下单人姓名" clearable></el-input>
					</el-col>
					<el-col :span="3">
						<el-select v-model="search.order_state" placeholder="订单状态">
							<el-option
							  v-for="(name,index) in stateText"
							  :key="index"
							  :label="name"
							  :value="index">
							</el-option>
						</el-select>
					</el-col>
					<el-col :span="3">
						<el-select v-model="search.order_type" placeholder="订单类型">
							<el-option
							  v-for="(name,index) in orderTypeText"
							  :key="index"
							  :label="name"
							  :value="index">
							</el-option>
						</el-select>
					</el-col>                    
					<el-col :span="12">
						<el-date-picker
						  v-model="search.date"
						  type="daterange"
						  format="yyyy-MM-dd"
						  range-separator="至"
						  start-placeholder="开始日期"
						  end-placeholder="结束日期">
						</el-date-picker>
					</el-col>
				</el-row>
				<el-row style="margin-top: 10px;">
					<el-col :span="4" :offset='20'>
						<el-button :loading="ifload" type="primary" @click="searchRes" icon="el-icon-search">搜索</el-button>
						<el-button :loading="ifload" type="danger" @click="exportExecl" icon="el-icon-download">导出</el-button>
					</el-col>
				</el-row>
			</div>

			<el-table
				:data="list"
				border
				v-loading="ifload"
				style="width: 100%">
				<el-table-column
					prop="order_id"
					label="订单ID"
					width="80">
				</el-table-column>
				<el-table-column
					prop="buyer_id"
					label="会员ID"
					width="80">
				</el-table-column>
				<el-table-column
					prop="buyer_name"
					label="下单人"
					width="80">
				</el-table-column>
				<el-table-column
					prop="buyer_phone"
					label="下单电话"
					width="120">
				</el-table-column>
				<el-table-column
					prop="order_amount"
					label="订单金额"
					width="100">
				</el-table-column>
				<el-table-column
					prop="order_type"
					label="订单类型"
					width="100">
					<template slot-scope="scope">
						<span>{{orderTypeText[scope.row.order_type]}}</span>
					</template>
				</el-table-column>

				<el-table-column
					prop="order_state"
					label="订单状态"
					width="80">
					<template slot-scope="scope">
						<span>{{stateText[scope.row.order_state]}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="create_time"
					label="下单时间"
					width="180">
					<template slot-scope="scope">
						<span>{{scope.row.create_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>
				<el-table-column
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-document" @click="dataView(scope.row)">
						查看详情
						</el-button>
                        <el-button v-show="scope.row.order_state<=1" type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">
						删除
						</el-button>
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

        <!-- 删除提示框 -->
        <el-dialog title="删除提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">删除不可恢复，是否确定删除？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="delData">确 定</el-button>
            </span>
        </el-dialog>

 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request'
export default{
	data() {
		return {
			ifload:true,
			list:[],

			page:1,
			page_size:10,
			pages:0,
			stateText:[], //订单状态描述
            orderTypeText:[],  //订单类型

			delId:0,
			delIndex:-1,
			delVisible:false,

			search:{
				order_id:'',
				buyer_name:'',
				date:'',
				order_state:'',
			}
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
				search:JSON.stringify(this.search),
			}
			this.ifload = true;
			this.$post_('order/order/order_list',params,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
					this.pages = Number(res.extend.pages);
					this.stateText = res.extend.orderState;
                    this.orderTypeText = res.extend.orderType;
					this.ifload = false;
				}
			});
		},
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },
		dataView(row) {
			this.$router.push({path:'/page/order/order_detail',query:{order_id:row.order_id}});
		},
		handleDel(index,row) {
			this.delVisible = true;
			this.delId = row.order_id;
			this.delIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {order_id:this.delId};
			this.$post_('order/order/order_del',param,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.list.splice(this.delIndex,1);
					this.ifload = false;
					this.delVisible = false;
				}else{
					this.$message.error(res.msg);
				}
			})
		},
		//搜索
		searchRes() {
			this.page = 1;
			this.getData();
		},
		//导出
		exportExecl() {
			let params = {
				page:this.page,
				page_size:1000,
				search:JSON.stringify(this.search),
			}
			this.$post_('order/order/export',params,(res) => {
				console.log(res);
				if(res.code=='0'){
					download(res.data.url);
				}
				// download(res,'111.xls');
			})
		}

	}
}
</script>

<style type="text/css">
    .red{
        color: #ff0000;
    }
	.search{
		margin-bottom: 10px;
	}
</style>
