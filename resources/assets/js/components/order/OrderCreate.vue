<template>
    <div class="container">
        <div class="alert alert-danger" v-if="alert" style="color: #842029;background-color: #f8d7da;border-color: #f5c2c7;">{{ this.alert }}</div>
        <div class="alert alert-success" v-if="alert_success" style="color: #0f5132;background-color: #d1e7dd;border-color: #badbcc;">{{ this.alert_success }}</div>
        <h1 style="margin: 0;">{{ this.title }}</h1>
        <div class="row">
            <div class="col-md-8">
                <div>
                    <div class="dishes_result">
                        <p v-if="loading">Loading...</p>
                    </div>

                    <ul class="list-group">
                        <li v-for="(dish, index) in filter_dish_by_menu" :key="index" class="list-group-item">
                            <h4>{{ dish.dish_type_name }}</h4>
                            <div v-for="(sub_dish, index) in dish.dishes" :key="index">
                                <div class="row" style="margin-bottom: 10px;">
                                    <div class="col-md-2">
                                        <div style="width: 70px;">
                                            <img :src="sub_dish.dish_photo" alt="" style="width: 100%; border-radius: 5px;">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-7">
                                        <h4><strong>{{ sub_dish.name }}</strong></h4>
                                        <p>{{ sub_dish.description }}</p>
                                    </div>
                                    <div class="col-md-3" style="display: flex; gap: 10px; align-items: center;">                                        
                                        <div v-if="sub_dish.discount_price" style="width: 100px;">
                                            <span style="color: red; font-size: 18px;display: block;">{{ sub_dish.discount_price_text }}</span>
                                            <del style="opacity: .7; font-size: 15px;display: block;">{{ sub_dish.price_text }}</del>
                                        </div>
                                        <div v-else style="color: red; font-size: 18px;width: 100px;">
                                            {{ sub_dish.price_text }}
                                        </div>
                                        <div>
                                            <div class="btn-adding" v-on:click="add_product_cart(sub_dish.id,sub_dish.name,sub_dish.price,sub_dish.discount_price,sub_dish.dish_type_name), scrollToTop()" style="font-size: 20px;cursor: pointer;font-weight: 700;line-height: 20px;width: 22px;height: 22px;background-color: #ee4d2d;text-align: center;color: #fff;display: inline-block;border-radius: 4px;">
                                                +
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color: #c7ccca;padding: 10px;" v-if="shop_infor.name">
                    <div style="background-color: #f1d3d3;padding: 5px;" v-if="shop_infor.name">
                        <div>
                            <img :src="shop_infor.photo" style="width: 100%;display: none;" alt="" />   
                            <div>
                                <h3 style="margin: 0;"><a :href="this.url_shopeefood" target="_blank">{{ shop_infor.name }}</a></h3>

                                <div style="display: none;">
                                    <div v-if="shop_infor.is_open" style="color: green">Opening</div>
                                    <div v-else style="color: red;">Close</div>
                                </div>
                                <div>{{ shop_infor.address }}</div>
                                <div><b>Ship:</b> {{ this.ship_fee }}</div>
                                <div><b>Voucher:</b> {{ this.voucher }}</div>
                            </div>                
                        </div>
                    </div>
                    <div>  
                        <div>
                            <h3>Danh sách món đã chọn</h3>

                            <!--<ul>-->
                            <ul style="padding: 0 0 0 20px;margin: 0;">
                                <li v-for="(item, index) in productItems" :key="item.id" class="shodow">
                                    {{ item.name }}
                                    <div style="border-bottom: 1px solid #aaa;padding-bottom: 5px;margin-bottom: 10px;">
                                        <span style="font-weight: bold;min-width: 80px;display: inline-block;">Số lượng: {{ item.number }}</span> - 
                                        <span v-if="item.discount_price" style="font-weight: bold;color: red;min-width: 80px;display: inline-block;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(item.number) * parseFloat(item.discount_price)) }}</span>
                                        <span v-else style="font-weight: bold;color: red;min-width: 80px;display: inline-block;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(item.number) * parseFloat(item.price)) }}</span>
                                        <div class="btn-subtract" v-on:click="remove_product_cart(index)" style="font-size: 20px;cursor: pointer;font-weight: 700;line-height: 20px;width: 22px;height: 22px;background-color: rgb(238, 77, 45);text-align: center;color: rgb(255, 255, 255);display: inline-block;border-radius: 4px;margin-left: 15px;">
                                            -
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <!--</ul>-->
                            <div v-if="productItems.length > 0" style="text-align: center;">
                                <textarea id="comment" name="comment" v-model="comment" rows="3" style="width: 100%;"></textarea>
                                <button class="btn btn-success" type="submit" v-on:click="add_order(), scrollToTop()">Đặt món</button>
                            </div>
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

        props: ['url_shopeefood','ship_fee','voucher','title','alert'],

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
                alert_success: ''
            }
        },

        created() {
            this.get_dish();
            this.remove_old_localStorage()

            if (localStorage.length > 0) {

                let items_card = JSON.parse(localStorage.getItem("stored_card_pos"+this.date_today));

                if (items_card === null) {
                    items_card = [];
                }
                items_card.forEach(element => {
                    this.productItems.push(element);
                });
            }
        },

        methods: {
            async add_order() {
                let post_data = {
                    'products': this.productItems,
                    'comment': this.comment
                }

                const response = await axios.post('/api/order/add', post_data);

                if (response.data.status) {
                    this.productItems = [];
                    localStorage.removeItem('stored_card_pos'+this.date_today);

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

                localStorage.setItem('stored_card_pos'+this.date_today, JSON.stringify(this.productItems));
            },
            remove_product_cart: function (indexOfObject) {
                let number_product = parseFloat(this.productItems[indexOfObject].number) - 1;

                if (number_product > 0) {
                    this.productItems[indexOfObject].number = number_product;
                } else {
                    this.productItems.splice(indexOfObject, 1);
                }

                localStorage.setItem('stored_card_pos'+this.date_today, JSON.stringify(this.productItems));
            },
            async get_dish() {
                
                this.saved = false
                this.loading = true

                const response = await axios.post('/api/get_url', {url: this.url_shopeefood})

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
                for (var i = 0; i < localStorage.length; i++){
                    let key_today = "stored_card_pos"+this.date_today;
                    if (localStorage.key(i) != key_today && 
                        localStorage.key(i).substring(0,15) == 'stored_card_pos') {
                        arr.push(localStorage.key(i));
                    }
                }

                // Iterate over arr and remove the items by key
                for (var i = 0; i < arr.length; i++) {
                    localStorage.removeItem(arr[i]);
                }
            }
        }
    }
</script>
