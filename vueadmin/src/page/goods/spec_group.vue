<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-cascades"></i>商品管理</el-breadcrumb-item>
                <el-breadcrumb-item>规格列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <div>
                <el-button icon="el-icon-add" @click="handleAdd"  type="success">添加分组 </el-button>
            </div>
            <el-table :data="tableData" border class="table" ref="multipleTable">

                <el-table-column prop="id" label="ID" width="100"></el-table-column>
                
                <el-table-column prop="type_name" label="分组名称" > </el-table-column>

                <el-table-column label="操作" width="240" align="center">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-edit" @click="groupSpecEdit(scope.$index, scope.row)">
                        添加组规格</el-button>                    
                        <el-button type="text" icon="el-icon-edit" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                    </template>
                </el-table-column> 
            </el-table>
        </div>

        <!-- 编辑弹出框 -->
        <el-dialog :title="id>0?'编辑':'添加'" :visible.sync="editVisible" width="30%">
            <el-form ref="form" :model="form" label-width="100px">
                <el-form-item label="分组名称">
                    <el-input v-model="form.type_name"></el-input>
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

        <!-- 规格分组 显示规格 -->
        <el-dialog title="分组规格选择" :visible.sync="editGroup" width="30%">

            <el-checkbox-group v-model="checkedSpec" @change="specChange">
            <el-checkbox v-for="spec in allSpec" :label="spec.id" :key="spec.id">{{spec.spec_name}}</el-checkbox>
            </el-checkbox-group>

            <span slot="footer" class="dialog-footer">
                <el-button @click="editGroup = false">取 消</el-button>
                <el-button type="primary" @click="saveGroup">确 定</el-button>
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
                editGroup:false,
                form: {
                    type_name: '',
                },
                id:0,

                typeId:0,
                checkedSpec:[],
                allSpec:[],
                checkAll: false,
                isIndeterminate: true,            
            }
        },
        created() {
            this.getData();
        },
        activated() {

            this.getData();
        },        

        methods: {
            // 获取数据
            getData() {
                this.$post_('goods/goods-spec/spec_group_list',{},(res)=>{
                    console.log(res);
                    this.tableData = res.data;
                });
            },

            //添加
            handleAdd(){
                this.form.type_name = '';
                this.form.id = 0;
                this.id = 0;               
                this.editVisible = true;
            },

            //修改
            handleEdit(index, row) {
                this.id = row.id;
                const item = this.tableData[index];
                this.form = {
                    type_name: item.type_name,
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
                this.$post_('goods/goods-spec/edit_spec_group',this.form,(res)=>{
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
                this.$post_('goods/goods-spec/del_spec_group',{id:this.id},(res)=>{
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

            groupSpecEdit(index, row) {
                this.typeId = row.id;
                this.$post_('goods/goods-spec/spec_group_spec',{type_id:row.id},(res)=>{
                    console.log(res);
                    this.allSpec = res.data.speces;
                    this.checkedSpec = res.data.owner_spec;
                    this.editGroup = true;
                })
            },
            //保存组规格
            saveGroup() {
                console.log(this.checkedSpec);
                let params = {
                    checked_spec:JSON.stringify(this.checkedSpec),
                    type_id:this.typeId,
                };
                this.$post_('goods/goods-spec/edit_group_spec',params,(res)=>{
                    console.log(res);
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                    }else{
                        this.$message.warning(res.msg);
                    }
                    this.editGroup = false;
                })                
            },

            specChange(value) {
                let checkedCount = value.length;
                this.checkAll = checkedCount === this.allSpec.length;
                this.isIndeterminate = checkedCount > 0 && checkedCount < this.allSpec.length;
            }

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
