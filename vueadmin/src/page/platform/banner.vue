<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>平台设置</el-breadcrumb-item>
                <el-breadcrumb-item>轮播图</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

	 	<div class="container">
        <el-row style="margin-bottom: 10px;">
            <el-col :span="3">
                <el-button type="success" icon="el-icon-plus" @click="addData">添加图片</el-button>
            </el-col>
        </el-row>
			<el-table
				:data="list"
				border
				v-loading="ifload">

				<el-table-column
					prop="img_url"
					label="图片"
                    align="center"
					width="200">
					<template slot-scope="scope">
						<img :src="scope.row.img_url?scope.row.img_url:avatarDefault" style="width:100%;"/>
                    </template>
				</el-table-column>
				<el-table-column
					prop="link"
                    align="center"
					label="跳转url"
					width="280">
				</el-table-column>

				<el-table-column
                    align="center"
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-document" @click="handleEidt(scope.row)">
						修改
						</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">
						删除
						</el-button>
                    </template>
				</el-table-column>
			</el-table>
		</div>

        <!-- 编辑弹出框 -->
        <el-dialog :title="curId>0?'编辑':'添加'" :visible.sync="editVisible" width="50%">
            <el-form ref="form" :model="form" label-width="100px">
                <el-form-item label="图片">
                   <img :src="form.img_url" alt="" style="width: 30%;">
                   <upload @showImg="showImg" :uploadType="'2'" :size="32" ></upload>
                </el-form-item>
                <el-form-item label="跳转URL">
                    <el-input v-model="form.link"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="editVisible = false">取 消</el-button>
                <el-button type="primary" @click="saveEdit">确 定</el-button>
            </span>
        </el-dialog>



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
            avatarDefault:require("../../assets/avatar_default.png" ),
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
			this.$post_('platform/banner/banner_list',{},(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
					this.ifload = false;
				}
			});
		},

        //添加
        addData() {
            this.editVisible = true;
            this.curId = 0;
            this.curIndex = '';
            this.form.img_url = '';
            this.form.link = '';
        },
        //修改
		handleEidt(row) {
            this.editVisible = true;
            this.curId = row.id;
            this.form.img_url = row.img_url;
            this.form.link = row.link;
		},
        saveEdit() {
            this.form.id = this.curId;
            this.$post_('platform/banner/banner_edit',this.form,(res) =>{
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.ifload = false;
					this.editVisible = false;
                    this.getData();
				}else{
					this.$message.error(res.msg);
				}
            });
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
			this.$post_('platform/banner/banner_del',param,(res) => {
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
