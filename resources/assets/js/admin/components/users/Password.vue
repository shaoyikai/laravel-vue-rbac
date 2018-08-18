<template>
    <Row>
        <Col span="8">
            <Form ref="form" :model="form" :rules="rules">
                <FormItem prop="password_old" label="旧密码">
                    <Input type="password" v-model="form.password_old" placeholder="新密码" />
                </FormItem>
                <FormItem prop="password_new" label="新密码">
                    <Input type="password" v-model="form.password_new" placeholder="新密码" />
                </FormItem>
                <FormItem prop="password_new_repeat" label="确认新密码">
                    <Input type="password" v-model="form.password_new_repeat" placeholder="确认新密码" />
                </FormItem>

                <FormItem>
                    <Button type="primary" @click="handleSubmit('form')">保存</Button>
                </FormItem>
            </Form>
        </Col>
    </Row>

</template>
<script>
    export default {
        name:'ChangePassword',
        data () {
            return {
                form: {
                    password_old: '',
                    password_new: '',
                    password_new_repeat: ''
                },
                rules: {
                    password_old: [
                        { required: true, message: '请输入旧密码', trigger: 'blur' },
                        { type: 'string', min: 6, message: '密码长度不能低于6个字符', trigger: 'blur' },
                        { type: 'string', max: 20, message: '密码长度不能多于20个字符', trigger: 'blur' }
                    ],
                    password_new: [
                        { required: true, message: '请输入新密码', trigger: 'blur' },
                        { type: 'string', min: 6, message: '密码长度不能低于6个字符', trigger: 'blur' },
                        { type: 'string', max: 20, message: '密码长度不能多于20个字符', trigger: 'blur' }
                    ],
                    password_new_repeat: [
                        { required: true, message: '请再次输入新密码', trigger: 'blur' },
                        { type: 'string', min: 6, message: '密码长度不能低于6个字符', trigger: 'blur' },
                        { type: 'string', max: 20, message: '密码长度不能多于20个字符', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        // 获取user的信息
                        let data = {
                            password_old:this.form.password_old,
                            password_new:this.form.password_new
                        }
                        let uri = '/base/change-password'
                        axios.post(uri, data).then((res) => {
                            if(res.status !== 200){
                                this.$Message.error('服务器暂无响应，请稍后再试');
                            }else{
                                if(res.data.status === 0){
                                    this.$Message.success('修改成功！');
                                }else{
                                    this.$Message.error(res.data.msg)
                                }
                            }
                        })


                    } else {
                        this.$Message.error('验证失败！');
                    }
                })
            }
        },

        mounted() {
            // 切换头部导航高亮
            this.$parent.topBar = 'monitor'
        }
    }
</script>