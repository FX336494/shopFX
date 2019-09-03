<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>平台管理</el-breadcrumb-item>
				<el-breadcrumb-item>
                   <router-link to="/page/platform/article"> <i class="el-icon-lx-calendar"></i>文章列表</router-link>
                </el-breadcrumb-item>
                <el-breadcrumb-item>文章编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-row>
				<el-col :xs="24" :sm="20" :md="20" :lg="20" :xl="20">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="文章标题">
							<el-input v-model="form.title"></el-input>
						</el-form-item>
						<el-form-item label="文章分类">
							<el-select v-model="form.class_id" placeholder="请选择">
                                <el-option
                                    v-for="item in articleClass"
                                    :key="item.id"
                                    :label="item.class_name"
                                    :value="item.id"
                                >

                                </el-option>
                            </el-select>
						</el-form-item>
						<el-form-item label="文章简述">
							<el-input v-model="form.desc"></el-input>
						</el-form-item>
						<el-form-item label="文章Logo">
                            <img :src="form.logo" v-show="form.logo" style="max-width: 150px;" />
                            <upload :uploadType="'2'" :size="32" @showImg="showImg"></upload>
						</el-form-item>
						<el-form-item label="文章内容">
							<editor v-if="initFlag" @getContent="getContent" :initContent="form.content"></editor>
						</el-form-item>
						<el-form-item label="文章状态">
							<!-- <el-input v-model="form.state"></el-input> -->
                            <el-switch
                              style="display: block"
                              v-model="form.state"
                              active-color="#13ce66"
                              inactive-color="#ff4949"
                              active-value="1"
                              inactive-value="0"
                              active-text="开启"
                              inactive-text="禁用">
                            </el-switch>
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
    import editor from '@/components/utils/editor';
    import upload from '@/components/utils/upload';
	export default{
		components:{loading,editor,upload},
		data() {
			return {
				ifload:false,
                initFlag:false, //用于富文本编辑器
				form:{
                    title:'',
                    class_id:'',
                    desc:'',
                    logo:'',
                    content:'',
                    state:'1',
                },
                articleId:0,
                articleClass:[],
			}
		},
		created() {
            this.articleId = this.$route.query.id;
			this.$nextTick(()=>{
				this.getData();
			})
		},
		methods: {
			getData() {
                this.ifload = true;
                this.$post_('platform/article/article_class',{},(res) =>{
                    this.articleClass = res.data;
                })
                if(this.articleId<1){
                    this.initFlag = true;
                    this.ifload = false;
                    return;
                }


                this.$post_('platform/article/article_info',{id:this.articleId},(res) =>{
                    if(res.code=='0'){
                        console.log(res);
                        this.form.title = res.data.title;
                        this.form.desc = res.data.desc;
                        this.form.class_id = res.data.class_id;
                        this.form.logo = res.data.logo;
                        this.form.content = res.data.content;
                        this.form.state = res.data.state;
                    }else{
                        this.$message.error(res.msg);
                    }
                    this.initFlag = true;
                    this.ifload = false;
                })

			},

            //获取编辑器内容
            getContent(content) {
                this.form.content = content;
            },
            //上传的图片
            showImg(imgUrl) {
                this.form.logo = imgUrl;
            },
            //保存数据
            saveData() {
                this.form.id = this.articleId;
                console.log(this.form);
                this.$post_('platform/article/article_edit',this.form,(res)=> {
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.ifload = false;
                    this.$router.push('/page/platform/article');
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
