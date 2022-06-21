<template>
    <div class="container">
        <div style="display: flex; gap: 5px;">
            <form action="" method="get" class="form-inline" @submit.prevent="get_dish">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="url_shopeefood" placeholder="Enter link">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <button class="btn btn-warning">Import DB</button>
        </div>

        <br/>

        <div class="row">
            <div class="col-md-8">
                <div>
                    <div class="dishes_result">
                        <p v-if="loading">Loading...</p>
                    </div>

                    <ul class="list-group">
                        <li v-for="(dish, index) in result_dish" :key="index" class="list-group-item">
                            <h4>{{ dish.dish_type_name }}</h4>
                            <div v-for="(sub_dish, index) in dish.dishes" :key="index">
                                <div class="row">
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
                                        <div v-if="sub_dish.discount_price" style="color: red; font-size: 20px;">{{ sub_dish.discount_price_text }}</div>
                                        <div v-if="sub_dish.price && !sub_dish.discount_price" style="color: red; font-size: 20px;">{{ dish.price_text }}</div>
                                        <del v-else style="opacity: .7; font-size: 15px;">{{ sub_dish.price_text }}</del>
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
                result_dish: [],
                shop_infor: {},
            }
        },

        methods: {
            async get_dish() {

                this.loading = true;

                const response = await axios.post('http://localhost:88/api/get_url', {url: this.url_shopeefood})

                if(response.data.dishes.result == 'success') {

                    this.loading = false;
                    let filter_data_dishes = this.filter_dishes(response.data.dishes.reply.menu_infos);
                    this.result_dish = this.chunk_dish_by_name(filter_data_dishes)
                    
                }

                if(response.data.detail.result == 'success') {

                    this.loading = false;

                    let delivery_detail = response.data.detail.reply.delivery_detail;
                    

                    let fields = ['name', 'address', 'delivery', 'res_photos', 'is_open'];

                    this.shop_infor = this.filter_infor_shop(delivery_detail, fields)
                }

                
            },
            filter_infor_shop(data, fields) {

                let arr = Object.entries(data);

                let arr_save = {};

                arr.forEach((item, index) => {
                    if(fields.includes(item[0])) {
                        if(item[0] == 'delivery') {
                            arr_save['ship'] = item[1].shipping_fee.minimum_fee
                            arr_save['is_open'] = item[1].is_open

                        } else if(item[0] == 'res_photos') {
                            arr_save['photo'] = item[1][0].photos[10].value
                        } else {
                            arr_save[item[0]] = item[1]
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
                            'shop_id': '1'
                        })
                    })
                })

                return arr_dish
            },

            chunk_dish_by_name(data) {
                let dishes = [];
                console.log(typeof dishes)

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
            }
        },
    }
</script>
