<template>
    <div class="container">
        <form action="" method="get" class="form-inline" @submit.prevent="get_dish">
            <div class="form-group">
                <label for="exampleInputEmail1">Link</label>
                <input type="text" class="form-control" v-model="url_shopeefood" placeholder="Enter link">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <br/>

        <div class="row">
            <div class="col-md-7">
                <div>
                    <div class="dishes_result">
                        <p v-if="loading">Loading...</p>
                    </div>

                    <ul class="list-group">
                        <li v-for="(dish, index) in result_dish" :key="index" class="list-group-item">
                            <h4>{{ dish.dish_type_name }}</h4>
                            <div>
                                <div v-for="(sub_dish, index) in dish.dishes" :key="index" class="row">
                                    <div class="col-md-2">
                                        <div style="width: 70px;">
                                            <img :src="sub_dish.photos[1].value" alt="" style="width: 100%; border-radius: 100%;">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h4><strong>{{ sub_dish.name }}</strong></h4>
                                        <p>{{ sub_dish.description }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        {{ sub_dish.price.text }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-5">
                <div style="background-color: white;">
                    <div v-if="shop_infor != null">
                        <div class="row">
                            <div class="col-md-3">
                                <img :src="shop_infor.photos[1].value" style="width: 100%;" alt="" />
                            </div>
                            <div class="col-md-9">
                                <h3>{{ shop_infor.name }}</h3>
                                <p>{{ shop_infor.address }}</p>
                                <p>{{ shop_infor.phones[0] }}</p>
                                <p>{{ shop_infor.rating.avg }} ({{ shop_infor.rating.total_review }})</p>
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
        data () {
            return {
                loading: false,
                url_shopeefood: '',
                result_dish: [],
                shop_infor: null
            }
        },

        methods: {
            async get_dish() {
                this.loading = true;

                const response = await axios.post('http://localhost:88/api/get_url', {url: this.url_shopeefood})

                if(response.data.dishes.result == 'success') {
                    this.loading = false;
                    this.result_dish = response.data.dishes.reply.menu_infos;
                }
                if(response.data.detail.result == 'success') {
                    this.loading = false;
                    this.shop_infor = response.data.detail.reply.delivery_detail;
                }
            },
        },
    }
</script>
