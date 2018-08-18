<template>

    <Modal width="1090"
            v-model="modal"
            title="分配影院"
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
        name: 'AssignCinemas',

        props: ['cinemas', 'role'],
        data() {
            return {
                assigned: [],
                not_assigned: []
            }
        },
        computed: {
            modal: {
                get() {
                    return this.$parent.modal2
                },
                set(value) {
                    this.$parent.modal2 = value
                }
            }
        },
        methods: {
            handleChange2(newTargetKeys) {
                this.assigned = newTargetKeys;
            },
            filterMethod(data, query) {
                return data.label.indexOf(query) > -1;
            },

            ok() {
                let uri = '/admin-api/roles/role-cinemas-assign'
                let params = {
                    id: this.role.id,
                    assigned_codes: this.assigned
                }
                axios.post(uri, params).then((res) => {
                    if (res.data.status === 0) {
                        this.$Message.success('已保存');
                    }
                })
                return false
            },
            cancel() {
                this.$Message.info('已取消');
            },

            getData: function () {
                let self = this
                let uri = '/admin-api/roles/role-cinemas'
                let params = {
                    params: {id: this.role.id}
                }
                axios.get(uri, params).then((res) => {
                    self.assigned = []
                    res.data.forEach((r) => {
                        self.assigned.push(r.cinema_code)
                    })

                    // 迭代全部cinemas，找出不在assigned中的
                    self.not_assigned = []
                    self.cinemas.forEach((c) => {
                        self.not_assigned.push({
                            key: c.cinema_code,
                            label: c.cinema_name,
                            description: '',
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
