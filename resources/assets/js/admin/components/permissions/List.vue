<template>
    <Row>
        <Col span="24" align="right" style="margin-bottom: 20px;">
            <Button @click="modal1 = true" type="primary">添加模块</Button>
        </Col>

        <Modal width="500"
                v-model="modal1"
                title="添加权限"
                @on-ok="ok">
            <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
                <FormItem label="模块名" prop="name">
                    <Input v-model="formValidate.name" placeholder="请输入模块名称"></Input>
                </FormItem>
                <FormItem label="标识符" prop="slug">
                    <Input v-model="formValidate.slug" placeholder="请输入模块标识符"></Input>
                </FormItem>
                <FormItem label="模块描述" prop="description">
                    <Input v-model="formValidate.description" placeholder="请输入模块描述"></Input>
                </FormItem>
            </Form>
        </Modal>
        <Col span="24">
            <Table stripe border :columns="columns" :data="permissions"></Table>
        </Col>
    </Row>
</template>

<script>

    export default {
        name: 'List',
        data() {
            return {
                modal1: false,
                permissions: [],
                columns: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: '模块',
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
                                    }
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
                            // slug不应该被修改
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
                                    }
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
                                    }
                                );
                            } else {
                                return h('span', {}, params.row.description)
                            }

                        }
                    },
                    {
                        title: '操作',
                        key: 'action',
                        width: 180,
                        align: 'left',
                        render: (h, params) => {
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px',
                                        display: params.row.isActive ? 'none' : 'inline-block'
                                    },
                                    on: {
                                        click: () => {
                                            params.row.isActive = 1
                                        }
                                    }
                                }, '修改'),
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px',
                                        display: params.row.isActive ? 'inline-block' : 'none'
                                    },
                                    on: {
                                        click: () => {
                                            let data = {
                                                id: params.row.id,
                                                name: params.row.name,
                                                description: params.row.description
                                            }
                                            let uri = '/admin-api/permission/update'
                                            axios.post(uri, data).then((res) => {
                                                if (res.data.status === 0) {
                                                    params.row.isActive = 0
                                                    this.$Message.success('更新成功!');
                                                } else if (res.data.status === 1) {
                                                    this.$Message.error(res.data.msg);
                                                } else {
                                                    this.$Message.error(res.data.msg);
                                                }
                                            })
                                        }
                                    }
                                }, '保存'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除这条数据吗?',
                                        transfer: true
                                    },
                                    on: {
                                        'on-ok': () => {
                                            let uri = '/admin-api/permission/delete'
                                            let data = {
                                                id: params.row.id
                                            }
                                            let self = this
                                            axios.post(uri, data).then((res) => {
                                                if (res.data.status === 0) {
                                                    // 删除此数据
                                                    self.permissions = self.permissions.filter((p) => {
                                                        return p.id !== params.row.id
                                                    })

                                                    this.$Message.success('删除成功!');
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
                                h('Button', {
                                    props: {
                                        type: 'default',
                                        size: 'small'
                                    },
                                    style: {
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

                formValidate: {
                    name: '',
                    slug: '',
                    description: ''
                },
                ruleValidate: {
                    name: [
                        { required: true, message: '名称不能为空', trigger: 'blur' }
                    ],
                    slug: [
                        { required: true, message: '标识符不能为空', trigger: 'blur' },
                        { type: 'string',min:2 , message: '至少要有2位字符', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            ok () {
                this.$refs['formValidate'].validate((valid) => {
                    if (valid) {
                        let uri = '/admin-api/permission/add';
                        axios.post(uri, this.formValidate).then((res) => {
                            if (res.data.status === 0) {
                                this.permissions.push(res.data.permission)
                                this.$Message.success('添加成功');
                            } else if (res.data.status === 1) {
                                this.permission.hasError = 1
                            } else {
                                this.permission.hasError = 2
                            }
                        })
                    } else {
                        this.$Message.error('数据填写有误!');
                    }
                })
            }
        },
        mounted(){
            this.$Loading.start()
            // 从接口获取系统中设置的所有权限
            let uri = '/admin-api/permissions'
            axios.get(uri).then((res) => {
                this.$Loading.finish()
                this.permissions = res.data
            }).catch((err) => {
                this.$Message.error('数据加载失败')
                this.$Loading.error()
                console.log(err);
            })
        }
    }
</script>

