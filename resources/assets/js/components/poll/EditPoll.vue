<template>
    <a v-on:click="open_edit_poll()" href="javascript:void(0)" class="btn btn-sm btn-primary btn-rounded ml-3 mr-3 mb-sm-0 mb-2">
        <i class="las la la-edit mr-1 scale5"></i>Edit
    </a>
</template>

<script>
    import axios from 'axios'

    export default {

        props: ['poll_id','poll_name','poll_description','poll_money'],

        methods: {
            open_edit_poll() {
                Swal.fire({
                    title: 'Thay đổi thông tin:',
                    html: '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text" style="min-width: 125px;">Name :</span></div><input type="text" name="poll_name" id="poll_name" value="'+this.poll_name+'" class="form-control"></div>' 
                    + '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text" style="min-width: 125px;">Description :</span></div><textarea class="form-control" rows="4" name="poll_description" id="poll_description">'+this.poll_description+'</textarea></div>'
                    + '<div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text" style="min-width: 125px;">Tiền phải đóng :</span></div><input type="number" name="poll_money" id="poll_money" value="'+this.poll_money+'" class="form-control"></div>',
                    type: "", 
                    showCancelButton: !0, 
                    confirmButtonColor: "#DD6B55", 
                    confirmButtonText: "Cập nhật",
                    allowOutsideClick: false
                }).then(async (result) => {
                    if (result.value) {
                        let post_data = {
                            'poll_id': this.poll_id,
                            'poll_name': document.getElementById('poll_name').value,
                            'poll_description': document.getElementById('poll_description').value,
                            'poll_money': document.getElementById('poll_money').value,
                        }

                        const response = await axios.post('/api/poll/edit', post_data);

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