<template>
    <a v-if="!is_pay" v-on:click="open_pay()" href="javascript:void(0)" class="btn btn-sm btn-outline-primary btn-rounded mr-3 mb-sm-0 mb-2">
        <i class="las la la-dollar mr-1 scale5"></i>Pay
    </a>
</template>

<script>
    import axios from 'axios'

    export default {
        props: {
            order_id: {
                type: String,
            },
            is_pay: {
                type: Boolean,
                default: false,
            },
            order_name: {
                type: String,
            },
        },

        methods: {
            open_pay() {
                Swal.fire({
                    title: 'User: ' + this.order_name + ' đã thanh toán tiền ?',
                    text: "",
                    type: "warning", 
                    showCancelButton: !0, 
                    confirmButtonColor: "#DD6B55", 
                    confirmButtonText: "Đồng Ý",
                    allowOutsideClick: false
                }).then(async (result) => {
                    if (result.value) {
                        let post_data = {
                            'order_id': this.order_id,
                        }

                        const response = await axios.post('/api/order/admin_pay_order', post_data);

                        if (response.data.status) {
                            var order_id = this.order_id;
                            this.is_pay = true;
                            Swal.fire({
                                title: response.data.message,
                                type: 'success',
                                confirmButtonText: 'OK'
                            }).then(function(){
                                document.getElementById("text_status_"+order_id).innerHTML = response.data.html;
                            })
                        } else {
                            swal(response.data.message, "", "error");
                        }
                    }
                })
            },
        }
    }
</script>