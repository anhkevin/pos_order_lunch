<template>
    <div class="stepper" id="stepper">
        <div class="stepper__step stepper__step--finish">
            <button v-on:click="update_status_order('select_product')" class="stepper__step-icon" v-bind:class="this.is_select_product ? 'stepper__step-icon--finish' : ''">
                <svg enable-background="new 0 0 32 32" viewBox="0 0 32 32" x="0" y="0" class="shopee-svg-icon icon-order-order">
                    <g>
                        <path d="m5 3.4v23.7c0 .4.3.7.7.7.2 0 .3 0 .3-.2.5-.4 1-.5 1.7-.5.9 0 1.7.4 2.2 1.1.2.2.3.4.5.4s.3-.2.5-.4c.5-.7 1.4-1.1 2.2-1.1s1.7.4 2.2 1.1c.2.2.3.4.5.4s.3-.2.5-.4c.5-.7 1.4-1.1 2.2-1.1.9 0 1.7.4 2.2 1.1.2.2.3.4.5.4s.3-.2.5-.4c.5-.7 1.4-1.1 2.2-1.1.7 0 1.2.2 1.7.5.2.2.3.2.3.2.3 0 .7-.4.7-.7v-23.7z" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3"></path>
                        <g>
                            <line fill="none" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3" x1="10" x2="22" y1="11.5" y2="11.5"></line>
                            <line fill="none" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3" x1="10" x2="22" y1="18.5" y2="18.5"></line>
                        </g>
                    </g>
                </svg>
            </button>
            <div class="stepper__step-text">Chọn món</div>
        </div>
        <div class="stepper__step stepper__step--finish">
            <button v-on:click="update_status_order('ordered')" class="stepper__step-icon" v-bind:class="this.is_ordered ? 'stepper__step-icon--finish' : ''">
                <svg enable-background="new 0 0 32 32" viewBox="0 0 32 32" x="0" y="0" class="shopee-svg-icon icon-order-shipping">
                    <g>
                        <line fill="none" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" x1="18.1" x2="9.6" y1="20.5" y2="20.5"></line>
                        <circle cx="7.5" cy="23.5" fill="none" r="4" stroke-miterlimit="10" stroke-width="3"></circle>
                        <circle cx="20.5" cy="23.5" fill="none" r="4" stroke-miterlimit="10" stroke-width="3"></circle>
                        <line fill="none" stroke-miterlimit="10" stroke-width="3" x1="19.7" x2="30" y1="15.5" y2="15.5"></line>
                        <polyline fill="none" points="4.6 20.5 1.5 20.5 1.5 4.5 20.5 4.5 20.5 18.4" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3"></polyline>
                        <polyline fill="none" points="20.5 9 29.5 9 30.5 22 24.7 22" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3"></polyline>
                    </g>
                </svg>
            </button>
            <div class="stepper__step-text">Đặt hàng</div>
        </div>
        <div class="stepper__step stepper__step--finish">
            <button v-on:click="update_status_order('request_pay')" class="stepper__step-icon" v-bind:class="this.is_request_pay ? 'stepper__step-icon--finish' : ''">
                <svg enable-background="new 0 0 32 32" viewBox="0 0 32 32" x="0" y="0" class="shopee-svg-icon icon-order-paid">
                    <g>
                        <path clip-rule="evenodd" d="m24 22h-21c-.5 0-1-.5-1-1v-15c0-.6.5-1 1-1h21c .5 0 1 .4 1 1v15c0 .5-.5 1-1 1z" fill="none" fill-rule="evenodd" stroke-miterlimit="10" stroke-width="3"></path>
                        <path clip-rule="evenodd" d="m24.8 10h4.2c.5 0 1 .4 1 1v15c0 .5-.5 1-1 1h-21c-.6 0-1-.4-1-1v-4" fill="none" fill-rule="evenodd" stroke-miterlimit="10" stroke-width="3"></path>
                        <path d="m12.9 17.2c-.7-.1-1.5-.4-2.1-.9l.8-1.2c.6.5 1.1.7 1.7.7.7 0 1-.3 1-.8 0-1.2-3.2-1.2-3.2-3.4 0-1.2.7-2 1.8-2.2v-1.3h1.2v1.2c.8.1 1.3.5 1.8 1l-.9 1c-.4-.4-.8-.6-1.3-.6-.6 0-.9.2-.9.8 0 1.1 3.2 1 3.2 3.3 0 1.2-.6 2-1.9 2.3v1.2h-1.2z" stroke="none"></path>
                    </g>
                </svg>
            </button>
            <div class="stepper__step-text">Yêu cầu thanh toán</div>
        </div>
        
        <div class="stepper__step stepper__step--finish">
            <button v-on:click="update_status_order('auto_pay')" class="stepper__step-icon" v-bind:class="this.is_auto_pay ? 'stepper__step-icon--finish' : ''">
                <svg enable-background="new 0 0 32 32" viewBox="0 0 32 32" x="0" y="0" class="shopee-svg-icon icon-order-received">
                    <g>
                        <polygon fill="none" points="2 28 2 19.2 10.6 19.2 11.7 21.5 19.8 21.5 20.9 19.2 30 19.1 30 28" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3"></polygon>
                        <polyline fill="none" points="21 8 27 8 30 19.1" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3"></polyline>
                        <polyline fill="none" points="2 19.2 5 8 11 8" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3"></polyline>
                        <line fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" x1="16" x2="16" y1="4" y2="14"></line>
                        <path d="m20.1 13.4-3.6 3.6c-.3.3-.7.3-.9 0l-3.6-3.6c-.4-.4-.1-1.1.5-1.1h7.2c.5 0 .8.7.4 1.1z" stroke="none"></path>
                    </g>
                </svg>
            </button>
            <div class="stepper__step-text">Chốt Đơn <br>(trừ tiền từ Ví)</div>
        </div>
        
        <div class="stepper__line">
            <div class="stepper__line-background"></div>
            <div class="stepper__line-foreground" v-bind:style="this.style_stepper_line"></div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {

        props: ['order_type'],

        data () {
            
            return {
                style_stepper_line: '',
                is_select_product: true,
                is_ordered: false,
                is_request_pay: false,
                is_auto_pay: false
            }
        },

        methods: {
            async load_stepper() {
                let post_data = {
                    'order_type': this.order_type,
                }

                const response = await axios.post('/api/order/get_stepper', post_data);

                if (response.data.status) {
                    this.is_select_product = response.data.is_select_product;
                    this.is_ordered = response.data.is_ordered;
                    this.is_request_pay = response.data.is_request_pay;
                    this.is_auto_pay = response.data.is_auto_pay;

                    var width_stepper = 0;
                    var stepper_width = parseFloat(document.getElementById("stepper").offsetWidth/4+30);
                    if (!this.is_auto_pay) {
                        width_stepper += stepper_width;
                    } 
                    if (!this.is_request_pay) {
                        width_stepper += stepper_width;
                    } 
                    if (!this.is_ordered) {
                        width_stepper += stepper_width;
                    }
                    if (!this.is_select_product) {
                        width_stepper += stepper_width;
                    }

                    this.style_stepper_line = 'width: calc((100% - 140px - '+width_stepper+'px) * 1); background: rgb(45, 194, 88);';
                }
            },

            async update_status_order(status_order) {
                let post_data = {
                    'order_type': this.order_type,
                    'status_order': status_order,
                }

                const response = await axios.post('/api/order/update_status_order', post_data);

                if (response.data.status) {
                    Swal.fire({
                        title: response.data.message,
                        type: 'success',
                        confirmButtonText: 'OK'
                    }).then(function(){
                        window.location.reload();
                    })
                } else {
                    if (response.data.message) {
                        swal(response.data.message, "", "error");
                    }
                }
            },
        },

        mounted() {
            this.load_stepper();
        }
    }
</script>