<template>
    <a v-on:click="order_cancel()" href="javascript:void(0)" class="btn btn-sm btn-danger btn-rounded mr-3 mb-sm-0 mb-2">
        <i class="las la la-trash mr-1 scale5"></i>Cancel
    </a>
</template>

<script>
    import axios from 'axios'

    export default {
        props: {
            order_id: {
                type: String,
            },
        },

        methods: {
            order_cancel() {
                Swal.fire({
                    title: 'Bấm đồng ý nếu muốn HỦY ?',
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

                        const response = await axios.post('/api/order/cancel_order', post_data);

                        if (response.data.status) {
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