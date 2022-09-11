<template>
    <li class="nav-item">
        <div class="d-flex weather-detail">
            <span><i class="las la-wallet"></i>{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(this.total_money) }}</span> 
            <b class="text-danger" v-if="this.total_money_debt > 0">-{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(this.total_money_debt) }}</b>
        </div>
    </li>
</template>

<script>
    import axios from 'axios'

    export default {
        data () {

            return {
                total_money: 0,
                total_money_debt: 0,
            }
        },

        methods: {
            async load_header() {
                const response = await axios.post('/api/layout/load_header');

                if (response.data.status) {
                    this.total_money = response.data.total_money;
                    this.total_money_debt = response.data.total_money_debt;
                }

            },
        },

        mounted() {
            this.load_header();
        }
    }
</script>