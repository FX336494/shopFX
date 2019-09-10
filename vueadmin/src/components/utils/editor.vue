<template>
    <div v-loading="ifload">
        <el-upload
                class="uploader"
                :action="serverUrl"
                name="file"
                :headers="header"
                :show-file-list="false"
				:http-request="uploadImg"
                :before-upload="beforeUpload">
        </el-upload>
        <quill-editor
            v-model="content"
            ref="myTextEditor"
            @change="onEditorChange($event)"
            :options="editorOption">
        </quill-editor>

	</div>
</template>
<script type="text/javascript">
	import {baseUrl,ajax_upload} from '@/components/js/request.js';
    import 'quill/dist/quill.core.css';
    import 'quill/dist/quill.snow.css';
    import 'quill/dist/quill.bubble.css';
    import { quillEditor, Quill } from 'vue-quill-editor';
	import {container, ImageExtend, QuillWatch} from 'quill-image-extend-module';
	export default{
		components:{quillEditor},
		props:{
            initContent:{type:String,default:''},
        },
		data() {
			return {
				ifload:false,
                uploadType:'image',
                content:'',
                serverUrl: baseUrl+'/common/qiniu',  // 这里写你要上传的图片服务器地址
                header: {},  // 有的图片服务器要求请求头需要有token之类的参数，写在这里
                editorOption: {
                    placeholder: '',
                    theme: 'snow',  // or 'bubble'
                    modules: {
                        toolbar: {
                            container: container,  // 工具栏
                            handlers: {
                                'image': function (value) {
                                    if (value) {
                                        document.querySelector('.uploader input').click()
                                    } else {
                                        this.quill.format('image', false);
                                    }
                                },
                                'video': function(value) {
                                    if(value) {
                                        console.log('video');
                                        document.querySelector('.uploader input').click()
                                    } else {
                                        this.quill.format('image', false);
                                    }
                                },
                            }
                        }
                    }
                },
			}
		},
		created() {
			this.$nextTick(()=>{
                 console.log(this.initContent);
				this.content = this.initContent;
			})
		},
		methods:{
            // 上传图片前
            beforeUpload(res, file) {
				 this.ifload = true
			},
			uploadImg(res) {
				console.log(res.file);
				var formdata = new FormData();
				formdata.append('file', res.file);
				ajax_upload('common/upload',formdata,(res)=>{
					console.log(res);
					// this.imgUrl = res.data.url;
					this.uploadSuccess(res)
					this.ifload = false;
				});
			},


            // 上传图片成功
            uploadSuccess(res) {
                // res为图片服务器返回的数据
                // 获取富文本组件实例
                let quill = this.$refs.myTextEditor.quill
                // 如果上传成功
                if (res.code === '0' && res.data !== null) {
                    // 获取光标所在位置
                    let length = quill.getSelection().index;
                    // 插入图片  res.data为服务器返回的图片地址
                    let ext = res.data.url.split('.').pop();
                    let videoArr = ['mp4','flv','rmvb','swf'];
                    if(videoArr.indexOf(ext)>-1){
                        this.uploadType = 'video'
                    }else{
                        this.uploadType = 'image';
                    }
                    quill.insertEmbed(length,this.uploadType, res.data.url)
                    // 调整光标到最后
                    quill.setSelection(length + 1)
                } else {
                    this.$message.error('图片插入失败')
                }
			},
            onEditorChange(e) {
                // console.log(this.content);
                this.$emit('getContent',this.content)
            },
		},
	}
</script>

<style>
	.uploader{
		display: none;
	}
</style>
