<template>

    <Modal width="1090"
            v-model="modal"
            title="分配模块"
            @on-ok="ok">

        <Transfer
                :data="not_assigned"
                :target-keys="assigned"
                filterable
                :filter-method="filterMethod"
                @on-change="handleChange2"></Transfer>
    </Modal>
</template>

<script>
    export default {
        name: 'AssignPermissions',
        data() {
            return {
                assigned: [],
                not_assigned: []
            }
        },
        props: ['permissions', 'role'],
        computed:{
            modal: {
                get(){
                    return this.$parent.modal3
                },
                set(value){
                    this.$parent.modal3 = value
                }
            }
        },
        methods: {
            handleChange2 (newTargetKeys) {
                this.assigned = newTargetKeys;
            },
            filterMethod (data, query) {
                return data.label.indexOf(query) > -1;
            },
            ok () {
                let uri = '/admin-api/roles/role-permissions-assign'
                let data = {
                    id: this.role.id,
                    assigned_ids: this.assigned
                }
                axios.post(uri, data).then((res) => {
                    if(res.data.status === 0){
                        this.$Message.success('已保存');
                    }
                })
            },
            getData: function () {
                let self = this
                let uri = '/admin-api/roles/role-permissions'
                let params = {
                    params: {id: this.role.id}
                }
                axios.get(uri, params).then((res) => {
                    self.assigned = []
                    res.data.forEach((r) => {
                        self.assigned.push(r.id)
                    })

                    // 迭代全部permissions，找出不在assigned中的
                    self.not_assigned = []
                    self.permissions.forEach((p) => {
                        self.not_assigned.push({
                            key: p.id,
                            label: p.name,
                            description: p.description,
                            disabled: false
                        })
                    })
                })
            }
        },
        watch: {
            role: {
                handler(newValue, oldValue) {
                    this.role = newValue
                    this.getData()
                },
                deep: true
            }
        }
    }
</script>
