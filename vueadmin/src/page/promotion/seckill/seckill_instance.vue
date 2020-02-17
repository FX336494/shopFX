<template>
    <div>
	 	<div class="container" style="border: none;">
            <el-row style="margin-bottom: 10px;">
                <el-col :span="3">
                    <el-button type="success" icon="el-icon-plus" @click="add(0)">添加</el-button>
                </el-col>
            </el-row>
			<el-table
				:data="list"
				border
				v-loading="ifload">
				<el-table-column
					prop="title"
					label="活动名称"
                    align="center"
					width="120">
				</el-table-column>
				<el-table-column
					prop="image"
                    align="center"
					label="活动图"
					width="120">
					<template slot-scope="scope">
						<img :src="scope.row.image" width="40%"/>
					</template>
				</el-table-column>
				<el-table-column
					prop="start_time"
                    align="center"
					label="开始时间"
					width="120">
					<template slot-scope="scope">
						<span>{{scope.row.start_time*1000 | formatDate}}</span>
					</template>
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
					prop="trailer_time"
                    align="center"
					label="预告时间"
					width="120">
					<template slot-scope="scope">
						<span>{{scope.row.trailer_time*1000 | formatDate}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="state"
                    align="center"
					label="状态"
					width="80">
					<template slot-scope="scope">
						<span>{{scope.row.state=='1'?'开启':'关闭'}}</span>
                    </template>
				</el-table-column>
				<el-table-column
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="success" size="mini" @click="seckillDoods(scope.row.instance_id)">
						商品管理
						</el-button>
                        <el-button type="primary" size="mini"  @click="add(scope.row.instance_id)">
						修改
						</el-button>
                        <el-button type="danger" size="mini" @click="handleDel(scope.$index, scope.row)">
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
import {download} from '@/components/js/request'
import upload from '@/components/utils/upload';
export default{
    components:{upload},
	data() {
		return {
			ifload:true,
			list:[],

            form: {
                img: '',
                class_name: '',
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
            this.$post_('promotion/seckill/instance_list',{},(res) =>{
                this.list = res.data;
                this.ifload = false;
            })
		},

        //编辑
		add(id) {
            this.$router.push({path:'/page/promotion/seckill/seckill_instance_edit',query:{id:id}});
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
			this.$post_('promotion/seckill/del_instance',param,(res) => {
                this.ifload = false;
                this.delVisible = false;
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.list.splice(this.curIndex,1);
				}else{
					this.$message.error(res.msg);
				}
			})
		},
        seckillDoods(insId) {
            this.$router.push({path:'/page/promotion/seckill/seckill_goods',query:{instance_id:insId}})
        }
	}
}
</script>

<style type="text/css">
	.search{
		margin-bottom: 10px;
	}
</style>
