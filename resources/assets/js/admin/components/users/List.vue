<template>
    <Row>
        <Col span="24" style="margin-bottom:20px;" align="right">
            <Button @click="modal1 = true" type="primary">添加用户</Button>

            <Modal width="500"
                   v-model="modal1"
                   title="添加用户"
                   @on-ok="ok">
                <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
                    <FormItem label="用户名" prop="name">
                        <Input v-model="formValidate.name" placeholder="请输入用户名"></Input>
                    </FormItem>
                    <FormItem label="手机号" prop="phone">
                        <Input v-model="formValidate.phone" placeholder="请输入手机号"></Input>
                    </FormItem>
                    <FormItem label="邮箱" prop="email">
                        <Input v-model="formValidate.email" placeholder="请输入邮箱"></Input>
                    </FormItem>
                    <FormItem label="密码" prop="password">
                        <Input v-model="formValidate.password" placeholder="请输入密码"></Input>
                    </FormItem>
                    <FormItem label="角色" prop="role_id">
                        <Select v-model="formValidate.role_id">
                            <Option v-for="r in roles" :key="r.id" :value="r.id">{{r.name}}</Option>
                        </Select>
                    </FormItem>
                </Form>
            </Modal>
        </Col>

        <Col span="24">
            <Table stripe border :columns="columns" :data="users"></Table>
            <br>

            <Page :total="total" :page-size="pageSize" :current="page" show-elevator
                  @on-change="get_page_data"></Page>
        </Col>
    </Row>
</template>

<script>
    import Edit from './Edit.vue'

    export default {
        name: 'List',
        data() {
            return {
                loadingBtn: false,
                modal1: false,
                users: [],
                roles: [],

                formValidate: {
                    name: '',
                    phone: '',
                    email: '',
                    password: '',
                    role_id: ''
                },
                ruleValidate: {
                    name: [
                        {required: true, message: '用户名不能为空', trigger: 'blur'},
                        {type: 'string', min: 2, message: '用户名最少要有2位', trigger: 'blur'},
                        {type: 'string', max: 20, message: '用户名最多有20位', trigger: 'blur'},
                    ],
                    phone: [
                        {required: true, message: '手机号不能为空', trigger: 'blur'},
                        {type: 'string', message: '请输入有效的11位手机号码', trigger: 'blur',min: 11, max: 11},
                    ],
                    email: [
                        {required: true, message: '邮箱不能为空', trigger: 'blur'},
                        {type: 'email', message: '邮箱格式不正确', trigger: 'blur'},
                        {type: 'string', max: 30, message: '邮箱最多有30位', trigger: 'blur'},
                    ],
                    password: [
                        {required: true, message: '密码不能为空', trigger: 'blur'},
                        {type: 'string', min: 6, message: '密码最少要有6位', trigger: 'blur'},
                        {type: 'string', max: 16, message: '密码最多有16位', trigger: 'blur'},
                    ]
                },
                total: 0,
                pageSize: 8,
                page: 1,

                columns: [
                    {
                        title: '名称',
                        key: 'name',
                        render: (h, params) => {
                            return h('div', [
                                h('Icon', {
                                    props: {
                                        type: 'person'
                                    }
                                }),
                                h('strong', params.row.name)
                            ]);
                        }
                    },
                    {
                        title: '手机号',
                        key: 'phone',
                        render: (h, params) => {
                            if (params.row.click_change) {
                                return h('Input', {
                                        props: {
                                            value: params.row.phone,
                                            model: params.row.phone
                                        },
                                        on: {
                                            'on-change': (e) => {
                                                console.log(e.target.value);
                                                params.row.phone = e.target.value
                                            }
                                        }
                                    }
                                )
                            }
                            else {
                                return h('span', {}, params.row.phone)
                            }
                        }
                    },
                    {
                        title: '邮箱',
                        key: 'email',
                        render: (h, params) => {
                            if (params.row.click_change) {
                                return h('Input', {
                                        props: {
                                            value: params.row.email,
                                            model: params.row.email
                                        },
                                        on: {
                                            'on-change': (e) => {
                                                params.row.email = e.target.value
                                            }
                                        }
                                    }
                                )
                            }
                            else {
                                return h('span', {}, params.row.email)
                            }
                        }
                    },
                    {
                        title: '角色',
                        key: 'roleName',
                        render: (h, params) => {
                            let options = []
                            this.roles.forEach((r) => {
                                options.push(
                                    h('Option', {
                                        props: {
                                            value: r.id
                                        }
                                    }, r.name)
                                )
                            })
                            if (params.row.click_change) {
                                return h('Select', {
                                        props: {
                                            value: params.row.role.id,
                                            model: params.row.selectValue
                                        },
                                        on: {
                                            'on-change': (value) => {
                                                params.row.selectValue = value
                                            }
                                        }
                                    },
                                    options
                                );
                            }
                            else {
                                return h('span', {}, params.row.roleName)
                            }

                        }
                    },
                    {
                        title: '操作',
                        key: 'action',
                        width: 260,
                        align: 'left',
                        render: (h, params) => {
                            let usableBtn = '';
                            if ((params.row.clickID1 === params.row.id) && this.loadingBtn) {
                                usableBtn = '...'
                            } else {
                                usableBtn = params.row.usable ? '禁用' : '启用'
                            }

                            if (params.row.role.slug == 'admin') {
                                /*return h('div', [
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
                                                let index = params.index
                                                this.$Modal.info({
                                                    title: '用户信息',
                                                    content: `名称：${this.users[index].name}<br>
                                                      邮箱：${this.users[index].email}<br>
                                                      角色：${this.users[index].roleName}`
                                                })
                                            }
                                        }
                                    }, '查看')
                                ]);*/
                            } else {
                                return h('div', [
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
                                                let index = params.index
                                                this.$Modal.info({
                                                    title: '用户信息',
                                                    content: `名称：${this.users[index].name}<br>
                                                      邮箱：${this.users[index].email}<br>
                                                      角色：${this.users[index].roleName}`
                                                })
                                            }
                                        }
                                    }, '查看'),
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
                                                this.edit_user(params.row.id)
                                            }
                                        }
                                    }, '编辑'),*/
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
                                                let self = this
                                                let id = params.row.id
                                                params.row.clickID1 = id
                                                this.loadingBtn = true
                                                this.users.forEach(function (u) {
                                                    if (u.id === id) {
                                                        let uri = '/admin-api/users/user-change-usable'
                                                        let data = {id: u.id,}
                                                        axios.post(uri, data).then((res) => {
                                                            params.row.clickID1 = ''
                                                            self.loadingBtn = false
                                                            if (res.data.status === 0) {
                                                                self.$Message.success('操作成功');
                                                                // 修改该user的usable值
                                                                u.usable = (u.usable === 1) ? 0 : 1

                                                            } else if (res.data.status === 1) {
                                                                self.$Message.error(res.data.msg)
                                                            } else {
                                                                self.$Message.error('发生了未知错误')
                                                            }
                                                        }).then((res) => {
                                                            self.loadingBtn = false
                                                        })
                                                    }
                                                })
                                                params.row.usable = params.row.usable ? 0 : 1
                                            }
                                        }
                                    }, usableBtn),
                                    h('Poptip', {
                                        props: {
                                            confirm: true,
                                            title: '您确定要删除这条数据吗?',
                                            transfer: true
                                        },
                                        on: {
                                            'on-ok': () => {
                                                let uri = '/admin-api/users/delete'
                                                let data = {
                                                    id: params.row.id
                                                }
                                                axios.post(uri, data).then((res) => {
                                                    if (res.data.status === 0) {
                                                        // 删除此数据
                                                        this.users = this.users.filter((r)=>{
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
                                    h('Button', {
                                        props: {
                                            type: 'primary',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '5px',
                                            display: params.row.click_change ? 'none' : 'inline-block'
                                        },
                                        on: {
                                            click: () => {
                                                params.row.click_change = params.row.click_change ? 0 : 1
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
                                            display: params.row.click_change ? 'inline-block' : 'none'
                                        },
                                        on: {
                                            click: () => {
                                                // 修改
                                                let uri = '/admin-api/users/role-change'
                                                let data = {
                                                    id: params.row.id,
                                                    email: params.row.email,
                                                    phone: params.row.phone,
                                                    role_id: params.row.selectValue
                                                }
                                                axios.post(uri, data).then((res) => {
                                                    if (res.data.status === 0) {
                                                        this.users.forEach((u, index) => {
                                                            if (u.id === params.row.id) {
                                                                u.click_change = 0
                                                                u.email = params.row.email
                                                                u.phone = params.row.phone
                                                                u.click_change = 0
                                                                u.role = res.data.role
                                                            }
                                                        })
                                                        this.$Message.success('已修改');
                                                    } else {
                                                        this.$Message.error('发生了未知错误')
                                                    }
                                                })

                                                params.row.click_change = params.row.click_change ? 0 : 1
                                                this.roles.forEach((v, index) => {
                                                    if (v.id === params.row.selectValue) {
                                                        params.row.roleName = v.name
                                                    }
                                                })
                                            }
                                        }
                                    }, '保存修改'),
                                    h('Button', {
                                        props: {
                                            type: 'default',
                                            size: 'small'
                                        },
                                        style: {
                                            marginRight: '5px',
                                            display: params.row.click_change ? 'inline-block' : 'none'
                                        },
                                        on: {
                                            click: () => {
                                                params.row.click_change = params.row.click_change ? 0 : 1
                                            }
                                        }
                                    }, '取消'),
                                ]);
                            }
                        }
                    }
                ],
            }
        },
        methods: {
            get_page_data: function (page) {
                this.page = page
                this.load_data()
            },
            edit_user: function (id) {
                // 编辑用户
                this.$router.push({path: '/admin/users/edit/' + id, component: Edit})
            },
            load_data: function () {
                this.$Loading.start();
                // 所有用户
                let uri = '/admin-api/users'
                let data = {
                    'params': {
                        page: this.page,
                        pageSize: this.pageSize
                    }
                }
                axios.get(uri, data).then((res) => {
                    this.users = res.data.data
                    $.each(res.data.data, (k, val) => {
                        this.users[k] = {
                            id: val.id,
                            phone: val.phone,
                            name: val.name,
                            email: val.email,
                            role: val.role,
                            usable: val.usable,
                            click_change: val.click_change,
                            roleName: val.role ? val.role.name : '',
                            selectValue: val.role ? val.role.id : '',
                        }
                    })

                    // 分页信息
                    this.total = res.data.total
                    this.page = res.data.current_page
                    this.$Loading.finish();
                }).catch((err) => {
                    this.$Message.error('数据加载失败')
                    this.$Loading.error()
                    console.log(err);
                })
            },
            ok: function () {
                this.$refs['formValidate'].validate((valid) => {
                    if (valid) {
                        let uri = '/admin-api/users/add';
                        axios.post(uri, this.formValidate).then((res) => {
                            if (res.data.status === 0) {
                                // this.users.push(res.data.user)
                                this.$Message.success('添加成功');
                                this.load_data()
                            }
                            else {
                                this.$Message.error(res.data.msg);
                            }
                        })
                    } else {
                        this.$Message.error('数据填写有误!');
                    }
                })
            },
            load_roles: function () {
                let uri = '/admin-api/roles'
                axios.get(uri).then((res) => {
                    this.roles = res.data
                })
            }
        },
        mounted() {

            // 延迟2秒加载
            setTimeout(this.load_roles, 2000)
            this.load_data()
        }
    }
</script>