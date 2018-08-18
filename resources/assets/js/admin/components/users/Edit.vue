<template>
    <div class="row">
        <div class="col-md-4">
            <table class="table table-bordered">
                <caption>编辑用户资料</caption>
                <tbody>
                <tr>
                    <th width="80">名称</th>
                    <td>{{user.name}}</td>
                </tr>
                <tr>
                    <th>邮箱</th>
                    <td>{{user.email}}</td>
                </tr>
                <tr>
                    <th>是否可用</th>
                    <td><span v-show="user.name">{{user.usable ? '可用': '不可用'}}</span></td>
                </tr>
                <tr>
                    <th>角色</th>
                    <td>{{user.role.name}}</td>
                </tr>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="2" align="center">
                        <button class="btn btn-sm">更新</button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Edit',
        data() {
            return {
                user: {
                    role:{ id: null}
                }
            }
        },
        mounted() {
            // 切换头部导航高亮
            this.$parent.topBar = 'rbac'

            this.user.id = this.$router.currentRoute.params.id
            // 获取user的信息
            let params = {
                params: {id: this.user.id}
            }
            let uri = '/admin-api/users/info'
            axios.get(uri, params).then((res) => {
                this.user = res.data
            })
        }
    }
</script>