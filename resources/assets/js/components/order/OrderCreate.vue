<template>
    <div class="">
        <div class="modal-backdrop fade show" v-if="alert || alert_success"></div>
        <div class="modal fade show" v-if="alert || alert_success" tabindex="-1" role="dialog" style="display: block; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog">
                <div v-if="alert_success" class="modal-content alert alert-success left-icon-big alert-dismissible fade show">
                    <button type="button" class="close" v-on:click="close_modal()" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button>
                    <div class="media">
                    <div class="alert-left-icon-big">
                        <span><i class="mdi mdi-check-circle-outline"></i></span>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-1 mb-2">Congratulations!</h5>
                        <p class="mb-0">{{ this.alert_success }}</p>
                    </div>
                    </div>
                </div>

                <div v-if="alert" class="modal-content alert alert-danger left-icon-big alert-dismissible fade show">
                    <button type="button" class="close" v-on:click="close_modal()" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span>
                    </button>
                    <div class="media">
                    <div class="alert-left-icon-big">
                        <span><i class="mdi mdi-alert"></i></span>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-1 mb-2">Failed!</h5>
                        <p class="mb-0">{{ this.alert }}</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <h1 style="margin: 0;">{{ this.title }}</h1>
        <div class="row">
            <div class="col-md-8">
                <div>
                    <div class="dishes_result">
                        <p v-if="loading">Loading...</p>
                    </div>

                    <ul class="list-group box-select-product">
                        <li v-for="(dish, index) in filter_dish_by_menu" :key="index" class="list-group-item">
                            <h4>{{ dish.dish_type_name }}</h4>
                            <div v-for="(sub_dish, index) in dish.dishes" :key="index">
                                <div class="row">
                                    <div class="col-2" style="padding-left: 0;">
                                        <div class="box-order-image">
                                            <img :src="sub_dish.dish_photo" alt="">
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <h4><strong>{{ sub_dish.name }}</strong></h4>
                                        <p>{{ sub_dish.description }}</p>
                                    </div>
                                    <div v-if="sub_dish.is_available" class="col-4" style="display: flex; gap: 10px; align-items: center;">                                        
                                        <div v-if="sub_dish.discount_price" style="width: 100px;">
                                            <span style="color: red; font-size: 18px;display: block;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(sub_dish.discount_price) }}</span>
                                            <del style="opacity: .7; font-size: 15px;display: block;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(sub_dish.price) }}</del>
                                        </div>
                                        <div v-else style="color: red; font-size: 18px;width: 100px;">
                                            {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(sub_dish.price) }}
                                        </div>
                                        <div>
                                            <div class="btn-adding" v-on:click="add_product_cart(sub_dish.id,sub_dish.name,sub_dish.price,sub_dish.discount_price,sub_dish.dish_type_name)" style="font-size: 20px;cursor: pointer;font-weight: 700;line-height: 20px;width: 22px;height: 22px;background-color: #ee4d2d;text-align: center;color: #fff;display: inline-block;border-radius: 4px;">
                                                +
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="col-4 adding-food-cart txt-right">
                                        <div class="btn-over">Hết hàng</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div v-if="shop_infor.name">

                    <div class="card overflow-hidden cart-shop-overflow">
                        <div class="text-center p-2 overlay-box">
                            <!-- <div class="accordion__header" style="position: absolute;right: 0;top: 0;border: 0;color: black;font-weight: 900;"><span class="accordion__header--indicator"></span></div> -->
                            <h3 class="mt-3 mb-1 text-white"><a :href="this.url_shopeefood" class="text-white" target="_blank"><strong>{{ shop_infor.name }}</strong></a></h3>
                            <p class="text-yellow mb-0" v-if="shop_infor.address">{{ shop_infor.address }}</p>
                        </div>
                        <span class="card-title pl-3 pt-2">Danh sách món đã chọn</span>
                        <ul v-if="productItems.length > 0" class="list-group list-group-flush">
                            <li style="padding: 5px 15px;" v-for="(item, index) in productItems" :key="item.id" class="shodow list-group-item">
                                <span class="mb-0" style="width: 180px !important;display: inline-block;">{{ item.name }}</span>
                                <span class="mb-0" style="width: 30px !important;display: inline-block;">x{{ item.number }}</span>
                                <strong v-if="item.discount_price" style="color: red;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(item.number) * parseFloat(item.discount_price)) }}</strong>
                                <strong v-else style="color: red;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(item.number) * parseFloat(item.price)) }}</strong>
                                <div class="btn-subtract" v-on:click="remove_product_cart(index)" style="font-size: 20px;cursor: pointer;font-weight: 700;line-height: 13px;width: 15px;height: 15px;background-color: rgb(238, 77, 45);text-align: center;color: rgb(255, 255, 255);display: inline-block;border-radius: 10px;margin-left: 15px;">
                                    -
                                </div>
                            </li>
                        </ul>
                        <p v-else class="text-center">No Items Selected</p>
                        <div class="card-footer border-0 mt-0" v-if="productItems.length > 0">
                            <textarea id="comment" name="comment" placeholder="Ghi chú thêm.." v-model="comment" rows="1" style="width: 100%; padding: 5px;"></textarea>
                            <button class="btn btn-primary btn-lg btn-block" v-on:click="add_order()">
                                <i class="fa fa-cart-plus"></i> Đặt món</button>		
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

    export default {

        props: ['url_shopeefood','ship_fee','voucher','title','alert','shop_type_id'],

        data () {
            var today = new Date();

            return {
                loading: false,
                saved: false,
                comment: '',
                result_dish: [],
                filter_dish_by_menu: [],
                shop_infor: {},
                productItems: [],
                date_today: today.getFullYear()+"_"+(today.getMonth()+1)+"_"+today.getDate(),
                alert_success: '',
                key_storage: this.shop_type_id
            }
        },

        created() {
            this.get_dish();

            if (sessionStorage.length > 0) {

                let items_card = JSON.parse(sessionStorage.getItem("stored_card_pos"+this.key_storage));

                if (items_card === null) {
                    items_card = [];
                }
                items_card.forEach(element => {
                    this.productItems.push(element);
                });
            }
        },

        methods: {
            close_modal() {
                this.alert = '';
                this.alert_success = '';
            },
            async add_order() {
                let post_data = {
                    'products': this.productItems,
                    'shop_type_id':this.shop_type_id,
                    'comment': this.comment
                }

                const response = await axios.post('/api/order/add', post_data);

                if (response.data.status) {
                    this.productItems = [];
                    sessionStorage.removeItem('stored_card_pos'+this.key_storage);

                    this.alert_success = response.data.message;
                } else {
                    this.alert = response.data.message;
                }
            },
            add_product_cart: function (id, name, price, discount_price, dish_type_name) {
                
                let number_product = 1;
                const indexOfObject = this.productItems.findIndex(element => {
                    if (element.id == id) {
                        number_product += parseFloat(element.number);
                        return true;
                    }
                    return false;
                });

                if (number_product > 1) {
                    this.productItems[indexOfObject].number = number_product;
                } else {
                    var item_product = {
                        id: id,
                        name: name,
                        price: price,
                        discount_price: discount_price,
                        number:number_product,
                        dish_type_name:dish_type_name
                    };
                    this.productItems.push(item_product);
                }

                sessionStorage.setItem('stored_card_pos'+this.key_storage, JSON.stringify(this.productItems));
            },
            remove_product_cart: function (indexOfObject) {
                let number_product = parseFloat(this.productItems[indexOfObject].number) - 1;

                if (number_product > 0) {
                    this.productItems[indexOfObject].number = number_product;
                } else {
                    this.productItems.splice(indexOfObject, 1);
                }

                sessionStorage.setItem('stored_card_pos'+this.key_storage, JSON.stringify(this.productItems));
            },
            async get_dish() {
                
                this.saved = false
                this.loading = true

                const response = await axios.post('/api/get_url', {url: this.url_shopeefood,shop_type_id:this.shop_type_id})

                if(response.data.dishes.result == 'success') {

                    this.loading = false;

                    this.result_dish = this.filter_dishes(response.data.dishes.reply.menu_infos);

                    this.filter_dish_by_menu = this.chunk_dish_by_name(this.result_dish)
                    
                }

                if(response.data.detail.result == 'success') {

                    this.loading = false;

                    let delivery_detail = response.data.detail.reply.delivery_detail;
                    

                    let fields = ['name', 'address', 'delivery', 'res_photos', 'is_open', 'delivery_id'];

                    this.shop_infor = this.filter_infor_shop(delivery_detail, fields)
                }

                
            },
            filter_infor_shop(data, fields) {

                let arr = Object.entries(data);

                let arr_save = {};

                arr.forEach((item, index) => {

                    if(fields.includes(item[0])) {
                        switch(item[0]) {
                            case 'delivery':
                                arr_save['ship'] = item[1].shipping_fee.value
                                arr_save['is_open'] = item[1].is_open
                                break;
                            case 'res_photos':
                                arr_save['photo'] = item[1][0].photos[10].value
                                break;
                            case 'delivery_id':
                                arr_save['delivery_id'] = item[1]
                                break;
                            default:
                                arr_save[item[0]] = item[1]
                                arr_save['voucher'] = 0
                                break;
                        }                        
                    }

                })

                return arr_save
            },

            filter_dishes(data) {

                let arr_dish = []

                data.forEach((item, index) => {

                    item.dishes.forEach((dish, index) => {
                        arr_dish.push({
                            'dish_type_name': item.dish_type_name,
                            'id': dish.id,
                            'name': dish.name,
                            'price': dish.price.value,
                            'price_text': dish.price.text,
                            'discount_price': dish.discount_price ? dish.discount_price.value : 0,
                            'discount_price_text': dish.discount_price ? dish.discount_price.text : 0,
                            'dish_photo': dish.photos[1].value,
                            'description': dish.description,
                            'is_available': dish.is_available,
                        })
                    })
                })

                return arr_dish
            },

            chunk_dish_by_name(data) {
                let dishes = [];

                let current_name = data[0].dish_type_name
                let pos = 0

                dishes[pos] = {
                    dish_type_name: '',
                    dishes: []
                }

                data.forEach((dish, index) => {               
                    if(current_name != dish.dish_type_name) {
                        pos++
                        current_name = dish.dish_type_name     
                        
                        dishes[pos] = {
                            dish_type_name: current_name,
                            dishes: [dish]
                        }              
                    }
                    else {
                        dishes[pos].dish_type_name = current_name
                        dishes[pos].dishes.push(dish)
                    }
                })

                return dishes
            },

            async save_collection() {

                let post_data_shop = {
                    'shop_infor': this.shop_infor,
                    'dishes': this.result_dish
                }

                const response_shop = await axios.post('/api/create_shop', post_data_shop)

                let post_data_dish = {
                    'dishes': this.result_dish,
                    'delivery_id': response_shop.data.result.delivery_id,
                };

                const response_dish = await axios.post('/api/create_dishes', post_data_dish)


                if(response_shop.data.status == 1 && response_dish.data.status == 1) {
                    this.saved = true;
                } 
            },

            scrollToTop() {
                window.scrollTo(0,0);
            },

            remove_old_localStorage() {
                var arr = []; // Array to hold the keys
                // Iterate over localStorage and insert the keys that meet the condition into arr
                for (var i = 0; i < sessionStorage.length; i++){
                    let key_today = "stored_card_pos"+this.key_storage;
                    if (sessionStorage.key(i) != key_today && 
                        sessionStorage.key(i).substring(0,15) == 'stored_card_pos') {
                        arr.push(sessionStorage.key(i));
                    }
                }

                // Iterate over arr and remove the items by key
                for (var i = 0; i < arr.length; i++) {
                    sessionStorage.removeItem(arr[i]);
                }
            }
        }
    }
</script>
