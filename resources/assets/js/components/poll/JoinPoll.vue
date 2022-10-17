<template>
    <div class="text-center pt-5 pb-5">
        <button v-on:click="join_poll()" type="button" class="btn btn-rounded btn-primary">
            <span class="btn-icon-left text-primary"><i class="fa fa-plus" aria-hidden="true"></i> </span>
            Tham Gia
        </button>

        <button v-on:click="open_cancel(1)" type="button" class="btn btn-sm btn-dark mt-5 mb-2 w-100">Logs cancel</button>

        <button v-on:click="open_cancel(0)" type="button" class="btn btn-sm btn-dark w-100">DS ko đi</button>
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
            async open_cancel(join) {
                let post_data = {
                    'order_type': this.poll_id,
                    'is_join': join
                }

                const response = await axios.post('/api/poll/get_cancel', post_data);

                if (response.data.status) {
                    if (response.data.orders) {
                        var text = '<tbody>';
                        var lists = response.data.orders;

                        lists.forEach((item, index) => {
                            text += '<tr><td class="center">'+(index+1)+'</td> <td class="left strong cp-join-poll">'+item.address+'</td></tr>';
                        })

                        text += '</tbody>';

                        var title = 'Danh sách Logs Cancel';
                        if (join != 1) {
                            title = 'Danh sách đăng ký nhưng ko đi';
                        }

                        Swal.fire({
                            html: '<h4 class="card-title">'+title+'</h4><div class="table-responsive table-bordered"><table class="table table-striped"><thead><tr><th class="center">#</th> <th>Name</th>   </tr></thead> '+text+'</table></div>',
                            showCancelButton: 0, 
                            showConfirmButton: 0,
                        });
                    }
                } else {
                    swal(response.data.message, "", "error");
                }
            },
            join_poll() {
                Swal.fire({
                    title: 'Bạn muốn tham gia ?',
                    html: '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text">Họ tên điểm danh :</span></div><input type="text" name="user_name" id="user_name" onclick="select_text(user_name)" value="'+this.user_name+'" class="form-control"></div>',
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