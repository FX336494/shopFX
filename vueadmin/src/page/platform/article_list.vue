<template>
    <div>
	 	<div class="container" style="border: none;">
            <el-row style="margin-bottom: 10px;">
                <el-col :span="3">
                    <el-button type="success" icon="el-icon-plus" @click="handleEidt(0)">添加文章</el-button>
                </el-col>
            </el-row>
			<el-table
				:data="list"
				border
				v-loading="ifload">
				<el-table-column
					prop="title"
					label="标题"
                    align="center"
					width="220">
				</el-table-column>
				<el-table-column
					prop="desc"
                    align="center"
					label="描述"
					width="260">
				</el-table-column>
				<el-table-column
					prop="logo"
                    align="center"
					label="Logo图"
					width="120">
					<template slot-scope="scope">
						<img :src="scope.row.logo" width="40%"/>
					</template>
				</el-table-column>
				<el-table-column
					prop="class_name"
                    align="center"
					label="分类"
					width="100">
				</el-table-column>
				<el-table-column
					prop="state"
                    align="center"
					label="状态"
					width="120">
					<template slot-scope="scope">
						<span v-show="scope.row.state=='1'" style="color:green;">正常</span>
                        <span v-show="scope.row.state=='0'" style="color:red;">关闭</span>
					</template>
				</el-table-column>
				<el-table-column
                    align="center"
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-document" @click="handleEidt(scope.row.id)">
						修改
						</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">
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
import upload from '@/components/utils/upload';
export default{
    components:{upload},
	data() {
		return {
			ifload:true,
			page:1,
			page_size:10,
			pages:0,
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
			this.$post_('platform/article/article_list',{},(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
                    this.pages = Number(res.extend.pages);
					this.ifload = false;
				}
			});
		},
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },

        //修改
		handleEidt(id) {
            this.$router.push({path:'/page/platform/article_edit',query:{id:id}})
		},


        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.curId = row.id;
			this.curIndex = index;
		},
        showImg(url) {
            this.form.img_url = url;
        },
		//删除
		delData() {
			this.ifload = true;
			let param = {id:this.curId};
			this.$post_('platform/article/article_del',param,(res) => {
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
