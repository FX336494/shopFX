<template>
    <div>
	 	<div class="container" style="border: none;">
            <el-page-header @back="backPintuan" content="拼团订单管理" style="margin-bottom: 20px;">
            </el-page-header>
			<el-table
				:data="list"
				border
				v-loading="ifload">
				<el-table-column
					prop="open_order_id"
					label="开团id"
                    align="center"
					width="80">
				</el-table-column>
				<el-table-column
					prop="open_member_id"
                    align="center"
					label="会员id"
					width="100">
				</el-table-column>
				<el-table-column
					prop="member_name"
                    align="center"
					label="会员姓名"
					width="100">
				</el-table-column>
				<el-table-column
					prop="goods_image"
					label="产品图"
                    align="center"
					width="120">
					<template slot-scope="scope">
						<img :src="scope.row.goods_image" style="width:48px;"/>
                    </template>
				</el-table-column>
				<el-table-column
					prop="goods_name"
                    align="center"
					label="产品名称"
					width="160">
				</el-table-column>
				<el-table-column
					prop="state"
                    align="center"
					label="状态"
					width="80">
					<template slot-scope="scope">
						<span>{{pintuanStateText[scope.row.state]}}</span>
					</template>
				</el-table-column>
				<el-table-column
					prop="open_nums"
                    align="center"
					label="开团人数"
					width="80">
				</el-table-column>
				<el-table-column
					prop="join_nums"
                    align="center"
					label="参加人数"
					width="80">
				</el-table-column>

				<el-table-column
					prop="desc"
                    align="center"
					label="描述"
					width="160">
				</el-table-column>

				<el-table-column label="操作">
                    <template slot-scope="scope">
                        --
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
import {download} from '@/components/js/request'
export default{
    components:{},
	data() {
		return {
			ifload:true,
			list:[],

            instanceId:0,
            pages:0,
            page:1,
            page_size:10,
            pintuanStateText:[],
		}
	},
	created() {
        this.instanceId = this.$route.query.instance_id?this.$route.query.instance_id:0;
		this.$nextTick(()=>{
			this.getData();
		})
	},

	methods:{
		getData() {
			this.ifload = true;
            let params = {
                instance_id:this.instanceId,
                page:this.page,
                page_size:this.page_size
            };
            this.$post_('promotion/pintuan/pintuan_order_list',params,(res) =>{
                this.list = res.data;
                this.pages = Number(res.extend.pages);
                this.pintuanStateText = res.extend.pintuanStateText;
                this.ifload = false;
            })
		},
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },
        backPintuan() {
            this.$router.push({path:'/page/promotion/pintuan/pintuan',query:{tab:'instance'}})
        }
	}
}
</script>

<style type="text/css">
	.search{
		margin-bottom: 10px;
	}
</style>
