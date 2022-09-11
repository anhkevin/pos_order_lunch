<template>
    <a v-if="!is_pay" v-on:click="open_pay()" href="javascript:void(0)" class="btn btn-outline-primary btn-rounded mr-3 mb-sm-0 mb-2">
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
            }

        },

        methods: {
            open_pay() {
                Swal.fire({
                    title: 'Bạn muốn trừ tiền từ Ví ?',
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

                        const response = await axios.post('/api/order/pay_order_type', post_data);

                        if (response.data.status) {
                            this.is_pay = true;
                            document.getElementById("text_status_"+this.order_id).innerHTML = response.data.html;
                            Swal.fire({
                                title: response.data.message,
                                type: 'success',
                                confirmButtonText: 'OK'
                            }).then(function(){
                                window.location.reload();
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