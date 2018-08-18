<template>
    <div>
        <Row>
            <Col span="24" align="right" style="margin-bottom: 20px;">
                <Button @click="modal1 = true; formValidate = {
                    name: '',
                    slug: '',
                    description: ''
                }" type="primary">添加角色</Button>
            </Col>

            <Col span="24">
                <Table stripe border :columns="columns" :data="roles"></Table>
            </Col>
        </Row>

        <Modal width="500"
                v-model="modal1"
                title="添加角色"
                @on-ok="ok">
            <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
                <FormItem label="角色名" prop="name">
                    <Input v-model="formValidate.name" placeholder="请输入角色名称"></Input>
                </FormItem>
                <FormItem label="标识符" prop="slug">
                    <Input v-model="formValidate.slug" placeholder="请输入角色标识符"></Input>
                </FormItem>
                <FormItem label="角色描述" prop="description">
                    <Input v-model="formValidate.description" placeholder="请输入角色描述"></Input>
                </FormItem>
            </Form>
        </Modal>

        <AssignCinemas :modal2="modal2" :role="role" :cinemas="cinemas"/>
        <AssignPermissions :modal3="modal3" :role="role" :permissions="permissions"/>
    </div>
</template>

<script>
    import AssignCinemas from './AssignCinemas'
    import AssignPermissions from './AssignPermissions'
    export default {
        name: 'List',
        components: {AssignCinemas,AssignPermissions},
        data() {
            return {
                modal1:false,
                modal2:false,
                modal3:false,
                columns: [
                    {
                        title: '角色名',
                        key: 'name',
                        render: (h, params) => {
                            if (params.row.isActive) {
                                return h('Input', {
                                        props: {
                                            value: params.row.name,
                                            model: params.row.name
                                        },
                                        on: {
                                            'on-change': (event) => {
                                                params.row.old_name = params.row.name
                                                params.row.name = event.target.value
                                            }
                                        }
                                    },
                                );
                            } else {
                                return h('span', {}, params.row.name)
                            }
                        }
                    },
                    {
                        title: '标识符',
                        key: 'slug',
                        render: (h, params) => {
                            // 标识符不应该被修改
                            if (false) {
                                return h('Input', {
                                        props: {
                                            value: params.row.slug,
                                            model: params.row.slug
                                        },
                                        on: {
                                            'on-change': (event) => {
                                                params.row.old_slug = params.row.slug
                                                params.row.slug = event.target.value
                                            }
                                        }
                                    },
                                );
                            } else {
                                return h('span', {}, params.row.slug)
                            }
                        }
                    },
                    {
                        title: '描述',
                        key: 'description',
                        render: (h, params) => {
                            if (params.row.isActive) {
                                return h('Input', {
                                        props: {
                                            value: params.row.description,
                                            model: params.row.description
                                        },
                                        on: {
                                            'on-change': (event) => {
                                                params.row.old_description = params.row.description
                                                params.row.description = event.target.value
                                            }
                                        }
                                    },
                                );
                            } else {
                                return h('span', {}, params.row.description)
                            }
                        }
                    },
                    {
                        title: '操作',
                        key: 'action',
                        width: 220,
                        align: 'left',
                        render: (h, params) => {
                            if(params.row.slug === 'admin'){
                                return ''
                            }
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: params.row.usable ? 'warning' : 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            if(params.row.isActive){

                                                if(params.row.name.length < 2){
                                                    this.$Message.error('名称不能少于2个字符')
                                                }
                                                else if(params.row.name.length > 20){
                                                    this.$Message.error('名称最多有20个字符')
                                                }
                                                else if(params.row.description.length < 2){
                                                    this.$Message.error('描述不能少于2个字符')
                                                }
                                                else if(params.row.description.length > 20){
                                                    this.$Message.error('描述最多有20个字符')
                                                }
                                                else{
                                                    let data = {
                                                        id:params.row.id,
                                                        name: params.row.name,
                                                        description: params.row.description
                                                    }
                                                    let uri = '/admin-api/roles/update'
                                                    axios.post(uri, data).then((res) => {
                                                        if (res.data.status === 0) {
                                                            params.row.isActive = 0
                                                            this.$Message.success('更新成功')
                                                        } else if(res.data.status === 1) {
                                                            this.$Message.error(res.data.msg)
                                                        }else{
                                                            this.$Message.error(res.data.msg)
                                                        }
                                                    })
                                                }
                                            }else{
                                                params.row.isActive = 1
                                            }
                                        }
                                    }
                                }, params.row.isActive ? '保存' : '修改'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除这条数据吗?',
                                        transfer: true
                                    },
                                    on: {
                                        'on-ok': () => {
                                            let uri = '/admin-api/roles/delete'
                                            let data = {
                                                id: params.row.id
                                            }
                                            axios.post(uri, data).then((res) => {
                                                if (res.data.status === 0) {
                                                    // 删除此数据
                                                    this.roles = this.roles.filter((r)=>{
                                                        return r.id !== params.row.id
                                                    })
                                                    this.$Message.success('删除成功')
                                                }else{
                                                    this.$Message.error(res.data.msg)
                                                }
                                            })
                                        }
                                    }
                                }, [
                                    h('Button', {
                                        props:{
                                            type: 'error',
                                            size: 'small',
                                            placement: 'top'
                                        },
                                        style: {
                                            marginRight: '5px'
                                        }
                                    }, '删除')
                                ]),
                                /*h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            this.assign_cinemas(params.row)
                                        }
                                    }
                                }, '影院'),*/
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            this.assign_permissions(params.row)
                                        }
                                    }
                                }, '模块'),
                                h('Button', {
                                    props: {
                                        type: 'default',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px',
                                        display: params.row.isActive ? 'inline-block' : 'none'
                                    },
                                    on: {
                                        click: () => {
                                            params.row.isActive = 0
                                            params.row.name = params.row.old_name ? params.row.old_name : params.row.name
                                            params.row.slug = params.row.old_slug ? params.row.old_slug : params.row.slug
                                            params.row.description = params.row.old_description ?
                                                params.row.old_description : params.row.description
                                        }
                                    }
                                }, '取消')
                            ])
                        }
                    }
                ],
                roles: [],
                role:{

                },
                permissions: [],
                cinemas: [],

                formValidate: {
                    name: '',
                    slug: '',
                    description: ''
                },
                ruleValidate: {
                    name: [
                        { required: true, message: '名称不能为空', trigger: 'blur' },
                        { type: 'string',min:2 , message: '至少要有2位字符', trigger: 'blur' },
                        { type: 'string',max:20 , message: '最多有20位字符', trigger: 'blur' }
                    ],
                    slug: [
                        { required: true, message: '标识符不能为空', trigger: 'blur' },
                        { type: 'string',min:2 , message: '至少要有2位字符', trigger: 'blur' },
                        { type: 'string',max:20 , message: '最多有20位字符', trigger: 'blur' }
                    ],
                    description: [
                        { type: 'string',max:20 , message: '最多有20位字符', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            ok () {
                this.$refs['formValidate'].validate((valid) => {
                    if (valid) {
                        let uri = '/admin-api/roles/add';
                        axios.post(uri, this.formValidate).then((res) => {
                            if (res.data.status === 0) {
                                this.roles.unshift(res.data.role)
                                this.$Message.success('添加成功');
                            } else if (res.data.status === 1) {
                                this.$Message.error(res.data.msg);
                            } else {
                                this.$Message.error(res.data.msg);
                            }
                        })
                    } else {
                        this.$Message.error('数据填写有误!');
                    }
                })
            },
            assign_cinemas(role){
                this.role = role
                this.modal2 = true //显示modal
            },
            assign_permissions(role){
                this.role = role
                this.modal3 = true //显示modal
            },
        },
        mounted() {
            this.$Loading.start()
            let uriRole = '/admin-api/roles'
            axios.get(uriRole).then((res) => {
                this.$Loading.finish()
                this.roles = res.data
            }).catch((err) => {
                this.$Message.error('数据加载失败')
                this.$Loading.error()
                console.log(err);
            })

            // 从接口获取系统中设置的所有权限
            let uriP = '/admin-api/permissions'
            axios.get(uriP).then((res) => {
                this.permissions = res.data
            })

            // 从接口获取系统中设置的所有影院
            let uriC = '/admin-api/cinemas'
            axios.get(uriC).then((res) => {
                this.cinemas = res.data
            })
        }
    }
</script>

<style scoped>
    ul.list-group{
        max-height:400px;
        overflow: auto;
        display: block;
        list-style-type: disc;
        -webkit-margin-before: 1em;
        -webkit-margin-after: 1em;
        -webkit-margin-start: 0px;
        -webkit-margin-end: 0px;
        -webkit-padding-start: 40px;
    }

    li.list-group-item:first-child {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    li.list-group-item {
        position: relative;
        display: block;
        padding: 10px 15px;
        margin-bottom: -1px;
        background-color: #fff;
        border: 1px solid #ddd;
    }
</style>