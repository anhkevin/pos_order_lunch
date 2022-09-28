<template>
    <a v-on:click="copy_list_by_class()" href="javascript:void(0)" class="btn btn-sm btn-outline-warning btn-rounded mr-3 mb-sm-0 mb-2">
        <i class="las la-copy mr-3 scale5"></i>{{ this.btn_name }}
    </a>
</template>

<script>
    
    export default {

        props: ['cp_class', 'cp_title', 'btn_name'],

        methods: {
            async copy_list_by_class() {
                var text = this.cp_title + '\r\n';
                var list_name = document.querySelectorAll('.' + this.cp_class);

                list_name.forEach((item, index) => {
                    text += (index+1) + '. ' + item.textContent + '\r\n';
                })

                await navigator.clipboard.writeText(text);
                alert('Copied!');
            },
        }
    }
</script>