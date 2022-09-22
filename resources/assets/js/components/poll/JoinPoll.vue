<template>
    <div class="text-center pt-5 pb-5">
        <button v-on:click="join_poll()" type="button" class="btn btn-rounded btn-primary">
            <span class="btn-icon-left text-primary"><i class="fa fa-plus" aria-hidden="true"></i> </span>
            Tham Gia
        </button>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        props: {
            poll_id: {
                type: String,
            },
            user_name: {
                type: String,
            },
        },

        methods: {
            join_poll() {
                Swal.fire({
                    title: 'Bạn muốn tham gia ?',
                    html: '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text">Họ tên điểm danh :</span></div><input type="text" name="user_name" id="user_name" value="'+this.user_name+'" class="form-control"></div>',
                    type: "warning", 
                    showCancelButton: !0, 
                    confirmButtonColor: "#DD6B55", 
                    confirmButtonText: "Đồng Ý",
                    allowOutsideClick: false
                }).then(async (result) => {
                    if (result.value) {
                        let post_data = {
                            'poll_id': this.poll_id,
                            'user_name': document.getElementById('user_name').value
                        }

                        const response = await axios.post('/api/poll/add_order', post_data);

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