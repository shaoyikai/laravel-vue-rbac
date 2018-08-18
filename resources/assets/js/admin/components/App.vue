<template>
    <div class="layout">
        <!-- 左侧导航 -->
        <Sider :style="{position: 'fixed', height: '100vh', left: 0, overflow: 'auto'}">
            <p style="height:64px; margin-top: 15px; text-align: center;">
                <img src="/images/logo-admin.png" alt="logo">
            </p>
            <p style="color:#fff;text-align:center;
            border-top:1px solid #636a7b;
            border-bottom:1px solid #636a7b;margin:10px;padding:20px;">
                <b style="padding-right:20px;">{{user.name}}</b>
                <Button type="default" @click="logout()" size="small">
                    <Icon type="ios-navigate"></Icon>
                    退出
                </Button>
            </p>
            <Menu :active-name="active_name" theme="dark" width="auto" :open-names="open_name">
                <Submenu name="3">
                    <template slot="title">
                        <Icon type="settings"></Icon>
                        系统管理
                    </template>
                    <MenuItem name="3-1" @click.native="route_to('/admin/users')">用户管理</MenuItem>
                    <MenuItem name="3-2" @click.native="route_to('/admin/roles')">角色管理</MenuItem>
                    <MenuItem name="3-3" @click.native="route_to('/admin/permissions')">权限资源</MenuItem>
                </Submenu>
                <Submenu name="4">
                    <template slot="title">
                        <Icon type="person"></Icon>
                        个人中心
                    </template>
                    <MenuItem name="4-2" @click.native="route_to('/admin/change-password')">修改密码</MenuItem>
                </Submenu>
            </Menu>
        </Sider>

        <!-- 右侧标题以及内容 -->
        <Layout :style="{marginLeft: '200px'}">
            <Header :style="{background: '#fff', boxShadow: '0 2px 3px 2px rgba(0,0,0,.1)'}">
                <h2>{{title}}</h2>
            </Header>

            <Content :style="{padding: '0 16px 16px'}">
                <Breadcrumb :style="{margin: '16px 0'}">
                    <BreadcrumbItem @click.native="route_to(bread_1.path)">
                        {{ bread_1.name }}
                    </BreadcrumbItem>
                    <BreadcrumbItem @click.native="route_to(bread_2.path)" v-if="bread_3.name">
                        {{ bread_2.name }}
                    </BreadcrumbItem>
                    <BreadcrumbItem @click.native="route_to(bread_3.path)" v-if="bread_3.name">
                        {{ bread_3.name }}
                    </BreadcrumbItem>
                </Breadcrumb>
                <Card>
                    <div style="min-height: 500px">
                        <div class="container">
                            <router-view/>
                        </div>
                    </div>
                </Card>
            </Content>
        </Layout>

        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            <input type="hidden" name="_token" :value="csrf_field">
        </form>

        <Modal v-model="modal1" @on-ok="logout_excute">
            <p>您确认要退出系统吗？</p>
        </Modal>
    </div>
</template>
<script>
    export default {
        name: 'App',
        data(){
            return {
                csrf_token:$('meta[name="csrf-token"]').attr('content'),
                modal1: false,
                user:{}
            }
        },
        computed: {
            open_name() {
                return this.$store.state.open_name
            },
            active_name() {
                return this.$store.state.active_name
            },
            title() {
                return this.$store.state.page_title
            },

            bread_1() {
                return this.$store.state.bread_1
            },
            bread_2() {
                return this.$store.state.bread_2
            },
            bread_3() {
                return this.$store.state.bread_3
            },

            csrf_field(){
                return this.csrf_token;
            }
        },
        methods: {
            go_home: function(){
                location.href="/home"
            },
            route_to: function (path) {
                this.$router.push({path: path})
            },
            logout: function () {
                this.modal1 = true
            },
            logout_excute:function(){
                document.getElementById('logout-form').submit();
            },
            get_base_info:function(){
                let uri = '/base/info'
                axios.get(uri).then((res)=>{
                    this.user = res.data.user
                })
            },
        },
        mounted(){
            this.get_base_info()
        }
    }
</script>
<style>
    .ivu-breadcrumb{
        color:#495060;
    }
    .ivu-breadcrumb>span:last-child{
        color:#999;
    }
</style>