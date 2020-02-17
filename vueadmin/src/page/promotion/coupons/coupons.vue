<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>促销活动</el-breadcrumb-item>
                <el-breadcrumb-item>优惠券</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">
        <el-row style="margin-bottom: 10px;">
            <el-col :span="3">
                <el-button type="success" icon="el-icon-plus" @click="handleEidt(0)">添加优惠券</el-button>
            </el-col>
        </el-row>
			<el-table
				:data="list"
				border
				v-loading="ifload">

				<el-table-column
					prop="coupons_name"
					label="名称"
                    align="center"
					width="200">
				</el-table-column>
				<el-table-column
					prop="coupons_money"
                    align="center"
					label="面额"
					width="80">
				</el-table-column>
				<el-table-column
					prop="consume_amount"
                    align="center"
					label="消费金额"
					width="120">
				</el-table-column>
				<el-table-column
					prop="total_nums"
                    align="center"
					label="发放总数"
					width="80">
				</el-table-column>
				<el-table-column
					prop="giveout_nums"
                    align="center"
					label="已发放"
					width="80">
				</el-table-column>                
				<el-table-column
					prop="end_time"
                    align="center"
					label="结束时间"
					width="120">
					<template slot-scope="scope">
						<span>{{scope.row.end_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>

				<el-table-column
                    align="center"
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-document" @click="handleEidt(scope.row.instance_id)">
						修改
						</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">
						删除
						</el-button>
                    </template>
				</el-table-column>
			</el-table>
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
export default{
    components:{},
	data() {
		return {
			ifload:true,
            avatarDefault:require("../../../assets/avatar_default.png" ),
			list:[],

            form: {
                img_url: '',
                link: '',
            },

            //当前操作对象
			curId:0,
			curIndex:-1,
			delVisible:false,
            editVisible:false,


		}
	},
	created() {
		this.$nextTick(()=>{
			this.getData();
		})
	},

	methods:{
		getData() {
			this.ifload = true;
			this.$post_('promotion/coupons/instance_list',{},(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
					this.ifload = false;
				}
			});
		},

        //添加
        handleEidt(id) {
            this.$router.push({path:'/page/promotion/coupons/coupons_edit',query:{id:id}})
        },

        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.curId = row.instance_id;
			this.curIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {instance_id:this.curId};
			this.$post_('promotion/coupons/del_instance',param,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.list.splice(this.curIndex,1);
					this.ifload = false;
					this.delVisible = false;
				}else{
					this.$message.error(res.msg);
				}
			})
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
