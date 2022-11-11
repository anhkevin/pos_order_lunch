<template>
    <div class="custom-control custom-switch toggle-switch text-left ml-4 inline-block">
        <input type="checkbox" class="custom-control-input" id="shopStatus" value="1" v-model="is_close" v-on:change="update_status()">
        <label class="custom-control-label fs-14 text-black pr-2" for="shopStatus"> Đóng Link</label>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        props: ['shop_type_id', 'shop_is_close'],

        data () {
            return {
                is_close: this.shop_is_close,
            }
        },

        created() {

            if (this.is_close == 0) {
                this.is_close = "";
            }
        },

        methods: {
            update_status() {
                let title = 'Bạn muốn mở order này ?';
                if(this.is_close) {
                    title = 'Bạn muốn đóng order này ?';
                }
                Swal.fire({
                    title: title,
                    text: "",
                    type: "warning", 
                    showCancelButton: !0, 
                    confirmButtonColor: "#DD6B55", 
                    confirmButtonText: "Đồng Ý",
                    allowOutsideClick: false
                }).then(async (result) => {

                    if (result.value) {
                        let post_data = {
                            'shop_type_id': this.shop_type_id,
                            'is_close': this.is_close,
                        }

                        const response = await axios.post('/api/shop/update_status', post_data);

                        if (response.data.status) {
                            Swal.fire({
                                title: response.data.message,
                                type: 'success',
                                confirmButtonText: 'OK'
                            }).then(function(){
                                window.location.reload();
                            })
                        } else {
                            if (this.is_close) {
                                this.is_close = false;
                            } else {
                                this.is_close = true;
                            }

                            swal(response.data.message, "", "error");
                        }
                    } else {
                        if (this.is_close) {
                            this.is_close = false;
                        } else {
                            this.is_close = true;
                        }
                    }
                })
            },
        }
    }
</script>