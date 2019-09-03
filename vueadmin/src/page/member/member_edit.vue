<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>会员管理</el-breadcrumb-item>
				<el-breadcrumb-item><i class="el-icon-lx-calendar"></i>会员列表</el-breadcrumb-item>
                <el-breadcrumb-item>会员编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="会员姓名">
							<el-input v-model="form.member_name" placeholder="输入会员姓名"></el-input>
						</el-form-item>
						<el-form-item label="联系电话">
							<el-input v-model="form.member_mobile" placeholder="输入联系电话"></el-input>
						</el-form-item>
						<el-form-item label="登录密码">
							<el-input v-model="form.loginpwd" placeholder="空值则为不修改" ></el-input>
						</el-form-item>
						<el-form-item label="支付密码">
							<el-input v-model="form.paypwd" placeholder="空值则为不修改" ></el-input>
						</el-form-item>

                        <el-form-item label="">
                            <el-button :loading="ifload" type="primary" @click="saveData">保存</el-button>
                        </el-form-item>
					</el-form>
				</el-col>
			</el-row>
		</div>

		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import loading from '@/components/utils/loading';
	export default{
		components:{loading},
		data() {
			return {
				ifload:true,

                member_id:'',

				form:{
                    member_name: '',
                    member_mobile: '',
                    loginpwd: '',
                    paypwd: '',
                },
			}
		},
		created() {
            this.member_id = this.$route.query.member_id;
			this.$nextTick(()=>{
				this.getData();
			})
		},
		methods: {
			getData() {
                this.ifload = true;
                let param = {member_id:this.member_id};
                this.$post_('member/member/member_info',param,(res) => {
                    console.log(res);
                    if(res.code=='0'){
                        this.form.member_name = res.data.member_name;
                        this.form.member_mobile = res.data.member_mobile;
                    }
                })
				this.ifload = false;
			},
            //保存数据
            saveData() {
                this.ifload = true;
                this.form.member_id = this.member_id;
                // this.form.grade = '1';
                this.$post_('member/member/member_edit',this.form,(res) =>{
                    console.log(res);
                    this.ifload = false;
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        this.$router.back(-1);
                    }else{
                        this.$message.error(res.msg);
                    }
                })
            }
		}
	}
</script>

<style scoped="scoped">
</style>
