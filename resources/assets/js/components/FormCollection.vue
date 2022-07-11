<template>
    <div class="container">
        <div style="display: flex; gap: 5px;">
            <form action="" method="get" class="form-inline" @submit.prevent="get_dish">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="url_shopeefood" placeholder="Enter link">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <button class="btn btn-warning" @click="save_collection" :disabled="saved">
                <span v-if="!saved">Save DB</span>
                <span v-else>Saved</span>
            </button>
        </div>

        <br/>

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
                                        <div v-if="sub_dish.discount_price">
                                            <span style="color: red; font-size: 18px;">{{ sub_dish.discount_price_text }}</span>
                                            <del style="opacity: .7; font-size: 15px;">{{ sub_dish.price_text }}</del>
                                        </div>
                                        <div v-else style="color: red; font-size: 18px;">
                                            {{ sub_dish.price_text }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color: white;" class="panel panel-default" v-if="shop_infor.name">
                    <div>
                        <img :src="shop_infor.photo" style="width: 100%;" alt="" />   
                        <div style="padding: 10px;">
                            <h3>{{ shop_infor.name }}</h3>

                            <div>
                                <div v-if="shop_infor.is_open" style="color: green">Opening</div>
                                <div v-else style="color: red;">Close</div>
                            </div>
                            <p>{{ shop_infor.address }}</p>
                            <p>Ship: {{ shop_infor.ship }}</p>
                            <p>Voucher: Null</p>
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
        data () {
            return {
                loading: false,
                url_shopeefood: '',

                saved: false,

                result_dish: [],
                filter_dish_by_menu: [],

                shop_infor: {},
                
            }
        },

        methods: {
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
            }
        },
    }
</script>
