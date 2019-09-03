<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>商品管理</el-breadcrumb-item>
				<el-breadcrumb-item><i class="el-icon-lx-calendar"></i>商品列表</el-breadcrumb-item>
                <el-breadcrumb-item>商品编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-tabs v-model="activeName" type="card">
				<el-tab-pane label="基本信息" name="base">
					<goodsBase v-if="activeName=='base'" :goodsCommonid="goodsCommonid" @baseSuccess="baseSuccess"></goodsBase>
				</el-tab-pane>
				<el-tab-pane label="产品详情" name="detail" v-if="goodsCommonid>0">
					<goodsDetail v-if="activeName=='detail'" :goodsCommonid="goodsCommonid" ></goodsDetail>
				</el-tab-pane>
				<el-tab-pane label="商品图片" name="images" v-if="goodsCommonid>0">
					<goodsImages v-if="activeName=='images'" :goodsCommonid="goodsCommonid" ></goodsImages>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script type="text/javascript">
	import goodsBase from '@/components/goods/goods_base';
	import goodsDetail from '@/components/goods/goods_detail';
	import goodsImages from '@/components/goods/goods_images';
	export default{
		components:{goodsBase,goodsDetail,goodsImages},
		data() {
			return {
				activeName: 'base',
				goodsCommonid:0,
			}
		},
		created() {
			this.goodsCommonid = Number(this.$route.query.goods_commonid);
		},
		methods: {
			//添加成功
			baseSuccess(goodsCommonid) {
				this.goodsCommonid = Number(goodsCommonid);
				this.activeName = 'detail';
			}
		}
	}
</script>

<style>
</style>
