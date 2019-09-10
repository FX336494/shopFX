<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-cascades"></i>商品管理</el-breadcrumb-item>
                <el-breadcrumb-item>分类列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <div>
                <el-button icon="el-icon-add" @click="handleAdd"  type="success">添加一级分类</el-button>
            </div>
            <el-table :data="tableData" border class="table" ref="multipleTable" @selection-change="handleSelectionChange">
                <el-table-column prop="id" label="ID" sortable width="100"></el-table-column>
                <el-table-column prop="icon" label="图标" width="120" >
                    <template  slot-scope="scope">
                        <i :class="scope.row.icon"></i>
                        <img :src="scope.row.img" style="width:32px;" />
                    </template>
                </el-table-column>
                <el-table-column prop="category_name" label="分类名称" width="160" :formatter="formatname">
                </el-table-column>
                <el-table-column prop="type_id" width="140"  label="所属规格组" :formatter="formatter"></el-table-column>
                <el-table-column prop="index_menu" width="140"  label="显示商城首页">
                    <template slot-scope="scope">
                        <span>{{scope.row.index_menu=='1'?'显示':'不显示'}}</span>
                    </template>
                </el-table-column>

                <el-table-column label="操作" align="center">
                    <template slot-scope="scope">
                        <el-button v-show="scope.row.level<2" type="text" icon="el-icon-edit" @click="addSub(scope.$index, scope.row)">添加子菜单</el-button>
                        <el-button type="text" icon="el-icon-edit" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <!-- 编辑弹出框 -->
        <el-dialog :title="idx>0?'编辑':'添加'" :visible.sync="editVisible" width="30%">
            <el-form ref="form" :model="form" label-width="100px">
                <el-form-item label="分类名称">
                    <el-input v-model="form.category_name"></el-input>
                </el-form-item>

                <el-form-item label="规格组">
                  <el-select v-model="form.type_id" placeholder="请选择">
                    <el-option
                      v-for="(item,index) in specGroup"
                      :key="index"
                      :label="item"
                      :value="index">
                    </el-option>
                  </el-select>
                </el-form-item>
                <el-form-item label="图标">
                    <span><upload :size="32" @showImg="showImg"></upload></span>
                    <img :src="form.img" v-show="form.img" style="width:32px;">
                </el-form-item>
                <el-form-item label="描述">
                    <el-input v-model="form.desc"></el-input>
                </el-form-item>
                <el-form-item label="是否显示首页">
                    <el-radio v-model="form.index_menu" label="1">显示</el-radio>
                    <el-radio v-model="form.index_menu" label="0">不显示</el-radio>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="editVisible = false">取 消</el-button>
                <el-button type="primary" @click="saveEdit">确 定</el-button>
            </span>
        </el-dialog>

        <!-- 删除提示框 -->
        <el-dialog title="提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">删除不可恢复，并会删除其下的所有子分类，是否确定删除？</div>
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
                specGroup:[],
                cur_page: 1,
                multipleSelection: [],
                editVisible: false,
                delVisible: false,
                showIcon:false,
                form: {
                    category_name: '',
                    desc:'',
                    type_id: 0,
                    img:'',
                    parent_id:0,
                    id:0,
                    index_menu:0,
                },
                idx: -1,
                id:0,
                level:-1,
            }
        },
        created() {
            this.getData();
        },
        activated() {
            this.form.pid = 0;
            this.getData();
        },

        methods: {
            // 获取 easy-mock 的模拟数据
            getData() {
                this.$post_('goods/goods-class/class_list',{},(res)=>{
                    this.tableData = res.data;
                    this.specGroup = res.extend.spec_group;
                });
            },
            formatter(row, column) {
                // console.log(row);
                if(row.type_id<1) return '无';
                return this.specGroup[row.type_id];
            },
            formatname(row, column) {
                if(row.level<1 || row.pid<=0) return row.category_name;
                let blank = "\xa0\xa0".repeat(row.level);
                let pre = blank+'|';
                let line = '-';
                let line2 = line.repeat(row.level);
                return pre+line2+row.category_name;
            },

            //添加
            handleAdd(){
                this.form.category_name = '';
                this.form.type_id = 0;
                this.form.desc = '';
                this.form.index_menu = '0';
                this.img = '';
                this.parent_id = 0;
                this.idx = -1;
                this.id = 0;
                this.editVisible = true;
            },

            //修改
            handleEdit(index, row) {
                this.idx = index;
                this.id = row.id;
                const item = this.tableData[index];
                this.form = {
                    category_name: item.category_name,
                    type_id: item.type_id,
                    desc: item.desc,
                    img:item.img,
                    id:this.id,
                    index_menu:item.index_menu

                }
                this.editVisible = true;
            },
            handleDelete(index, row) {
                this.id = row.id;
                this.idx = index;
                this.delVisible = true;
            },
            handleSelectionChange(val) {
                // console.log(val);
                this.multipleSelection = val;
            },

            // 保存编辑
            saveEdit() {
                // console.log(this.form);return;
                this.$post_('goods/goods-class/edit_class',this.form,(res)=>{
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
                this.$post_('goods/goods-class/del_class',{id:this.id},(res)=>{
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
            //添加字菜单
            addSub(index,row){
                this.form.parent_id = row.id;
                this.id = 0;
                this.handleAdd();
            },
            showImg(imgUrl) {
                this.form.img = imgUrl;
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
