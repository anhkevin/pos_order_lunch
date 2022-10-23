<template>
    <div class="text-center pt-5 pb-5">
        <button v-on:click="join_poll()" type="button" class="btn btn-rounded btn-primary">
            <span class="btn-icon-left text-primary"><i class="fa fa-plus" aria-hidden="true"></i> </span>
            Tham Gia
        </button>

        <button v-on:click="open_cancel()" type="button" class="btn btn-sm btn-dark mt-5 mb-2 w-100">Logs cancel</button>

        <button v-on:click="open_cancel()" type="button" class="btn btn-sm btn-dark w-100">DS ko đi</button>
    </div>
</template>

<script>

    export default {

        methods: {
            async open_cancel() {
                swal("Vui lòng Login để sử dụng!", "", "error");
            },
            join_poll() {
                swal("Vui lòng Login để sử dụng!", "", "error");
            },
        }
    }
</script>