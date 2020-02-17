<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>促销管理</el-breadcrumb-item>
                <el-breadcrumb-item>拼团管理</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-tabs v-model="activeName" type="card">
				<el-tab-pane label="拼团活动" name="activity">
					<activity v-if="activeName=='activity'"></activity>
				</el-tab-pane>
				<el-tab-pane label="拼团实例" name="instance">
					<instance v-if="activeName=='instance'"></instance>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script type="text/javascript">
	import activity from '@/page/promotion/pintuan/pintuan_activity';
	import instance from '@/page/promotion/pintuan/pintuan_instance';
	export default{
		components:{activity,instance},
		data() {
			return {
				activeName: 'activity',
			}
		},
		created() {
            if(this.$route.query.tab){
                this.activeName = this.$route.query.tab;
            }
		},

		methods: {
            operateSuc() {
                this.activeName = 'open_order';
            }
		}
	}
</script>

<style>
</style>
