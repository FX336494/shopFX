<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-cascades"></i>商品管理</el-breadcrumb-item>
                <el-breadcrumb-item>规格列表</el-breadcrumb-item>
                <el-breadcrumb-item>规格值列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <div>
                <el-button icon="el-icon-add" @click="handleAdd"  type="success">添加规格值 </el-button>
            </div>
            <el-table :data="tableData" border class="table" ref="multipleTable">

                <el-table-column prop="id" label="ID" width="100"></el-table-column>
                
                <el-table-column prop="value_name" label="值名称" > </el-table-column>
                <el-table-column prop="show_name" label="显示名称" > </el-table-column>
                <el-table-column prop="sort" label="排序" width="100"> </el-table-column>

                <el-table-column label="操作" width="240" align="center">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-edit"  @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <!-- 编辑弹出框 -->
        <el-dialog :title="id>0?'编辑':'添加'" :visible.sync="editVisible" width="30%">
            <el-form ref="form" :model="form" label-width="100px">
                <el-form-item label="规格值">
                    <el-input v-model="form.value_name"></el-input>
                </el-form-item>
                <el-form-item label="显示名称">
                    <el-input v-model="form.show_name"></el-input>
                </el-form-item>                
                          
                <el-form-item label="排序">
                    <el-input v-model="form.sort"></el-input>
                </el-form-item>                
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="editVisible = false">取 消</el-button>
                <el-button type="primary" @click="saveEdit">确 定</el-button>
            </span>
        </el-dialog>

        <!-- 删除提示框 -->
        <el-dialog title="提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">删除不可恢复，是否确定删除？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button type="primary" @click="deleteRow">确 定</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import upload from '@/components/utils/upload';
    export default {
        name: 'goods_class',
        components:{upload},
        data() {
            return {
                tableData: [],
                cur_page: 1,
                editVisible: false,
                delVisible: false,
                form: {
                    value_name: '',
                    show_name:'',
                    spec_id:0,
                    sort:'',
                },
                id:0,

                specId:'',
            }
        },
        created() {
            this.specId = this.$route.query.spec_id;
            this.form.spec_id = this.specId;
            this.getData();
        },        
        activated() {
            this.specId = this.$route.query.spec_id;
            this.form.spec_id = this.specId;
            this.getData();
        },        

        methods: {
            // 获取数据
            getData() {
                this.$post_('goods/goods-spec/spec_value_list',{spec_id:this.specId},(res)=>{
                    console.log(res);
                    this.tableData = res.data;
                });
            },

            //添加
            handleAdd(){
                this.form.value_name = '';
                this.form.show_name = '';
                this.form.sort = 0;
                this.form.id = 0;
                this.spec_id = this.specId;
                this.id = 0;               
                this.editVisible = true;
            },

            //修改
            handleEdit(index, row) {
                this.id = row.id;
                const item = this.tableData[index];
                this.form = {
                    value_name: item.value_name,
                    show_name: item.show_name,
                    sort: item.sort,
                    id:this.id,

                }
                this.editVisible = true;
            },
            handleDelete(index, row) {
                this.id = row.id;
                this.delVisible = true;
            },

            // 保存编辑
            saveEdit() {
                this.$post_('goods/goods-spec/edit_spec_val',this.form,(res)=>{
                    console.log(res);
                    if(res.code=='0'){
                        this.getData();
                        this.$message.success(res.msg);
                    }else{
                        this.$message.warning(res.msg);
                    }
                })
                this.editVisible = false;
            },
            // 确定删除
            deleteRow(){
                this.$post_('goods/goods-spec/del_spec_val',{id:this.id},(res)=>{
                    console.log(res);
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        this.getData();
                    }else{
                        this.$message.warning(res.msg);
                    }
                })
                this.delVisible = false;
            },

        }
    }

</script>

<style scoped>
    .iconfont{
        font-size: 20px;
        /*font-weight: bold;*/
    }
    .handle-box {
        margin-bottom: 20px;
    }

    .handle-select {
        width: 120px;
    }

    .handle-input {
        width: 300px;
        display: inline-block;
    }
    .del-dialog-cnt{
        font-size: 16px;
        text-align: center
    }
    .table{
        width: 100%;
        font-size: 14px;
    }
    .red{
        color: #ff0000;
    }
</style>
