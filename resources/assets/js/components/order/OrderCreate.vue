<template>

<div class="row">
    <div class="col-sm-7 col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="text-warning display-inline font-w600 mb-3">Chọn món: <strong style="color:red">{{ this.title }}</strong></h4>
                <stepper-order :order_column="this.order_column"></stepper-order>
                <div v-if="this.alert" class="row justify-content-center align-items-center mt-5 mb-5">
                    <div class="col-12">
                        <div class="form-input-content text-center error-page">
                            <h4><i class="fa fa-times-circle text-danger"></i> {{ this.alert }}</h4>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <div class="dishes_result">
                        <p v-if="loading">Loading...</p>
                    </div>

                    <ul class="list-group box-select-product">
                        <li v-for="(dish, index) in filter_dish_by_menu" :key="index" class="list-group-item">
                            <h4>{{ dish.dish_type_name }}</h4>
                            <div v-for="(sub_dish, index) in dish.dishes" :key="index">
                                <div class="row">
                                    <div class="col-2 d-none d-sm-block" style="padding-left: 0;">
                                        <div class="box-order-image">
                                            <img :src="sub_dish.dish_photo" alt="">
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-6 col-7">
                                        <h4><strong>{{ sub_dish.name }}</strong></h4>
                                        <p>{{ sub_dish.description }}</p>
                                    </div>
                                    <div v-if="sub_dish.is_available" class="col-sm-4 col-5" style="display: flex; gap: 10px; align-items: center;">                                        
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
                                    <div v-else class="col-sm-4 col-5 adding-food-cart txt-right">
                                        <div class="btn-over">Hết hàng</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <div class="col-sm-5 col-12">
        <div class="card">
            <div class="card-body">
                <div class="profile-statistics mb-2">
                    <h4 class="d-inline"><a :href="this.url_shopeefood" class="text-warning d-inline font-w600 m-0 p-3" target="_blank"><strong>{{ shop_infor.name }}</strong></a></h4>
                    <div v-if="this.is_admin == 1" class="d-inline">
                        <shop_update_status :shop_type_id="this.shop_type_id" :shop_is_close="this.shop_info_close"></shop_update_status>
                        <button type="button" class="btn btn-sm btn-danger light float-right" v-on:click="btn_delete_order_type()"><i class="fa fa-trash"></i></button>
                    </div>
                    <profile-shop-type :shop_type_id="this.shop_type_id"></profile-shop-type>
                </div>
                <div class="profile-blog">
                    <h4 class="card-header d-flex justify-content-between align-items-center font-w600 m-0 p-3">
                        <span class="text-warning">Món đang chọn</span>
                        <span class="badge badge-danger badge-pill">{{ this.total_selected }}</span>
                    </h4>
                    <table v-if="productItems.length > 0" class="table">
                        <tbody>
                            <tr v-for="(item, index) in productItems" :key="item.id">
                                <td>{{ item.name }}</td>
                                <td>x{{ item.number }}</td>
                                <td v-if="item.discount_price" width="120"><strong style="color: red;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(item.number) * parseFloat(item.discount_price)) }}</strong></td>
                                <td v-else width="120"><strong style="color: red;">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(parseFloat(item.number) * parseFloat(item.price)) }}</strong></td>
                                <td width="50">
                                    <div class="d-flex">
                                        <a href="#" v-on:click="remove_product_cart(index)" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-minus-circle"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="text-center mt-3"><strong>No Items Selected</strong></p>
                    <div class="card-footer border-0 mt-0 pt-0" v-if="productItems.length > 0">
                        <textarea id="comment" name="comment" placeholder="Ghi chú thêm.." v-model="comment" rows="2" maxlength="30" style="width: 100%; padding: 5px;"></textarea>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-primary btn-lg mt-3" v-on:click="add_order()"><i class="fa fa-cart-plus"></i> Đặt món</button>
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

        props: ['url_shopeefood','ship_fee','voucher','title','shop_type_id', 'alert', 'is_admin', 'shop_info_id', 'shop_info_close', 'order_column'],

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
                key_storage: this.shop_type_id,
                total_selected: 0,
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
                    this.total_selected+=element.number;
                    this.productItems.push(element);
                });
            }
        },

        methods: {
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
                    this.total_selected=0;
                    swal(response.data.message, "", "success");
                } else {
                    swal(response.data.message, "", "error");
                }
            },
            add_product_cart: function (id, name, price, discount_price, dish_type_name) {
                this.total_selected++;
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
                this.total_selected--;
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

            btn_delete_order_type() {
                Swal.fire({
                    title: 'Bạn muốn xóa đơn hàng này ?',
                    text: "",
                    type: "warning", 
                    showCancelButton: !0, 
                    confirmButtonColor: "#DD6B55", 
                    confirmButtonText: "Đồng Ý",
                    allowOutsideClick: false
                }).then(async (result) => {
                    if (result.value) {
                        let post_data = {
                            'shop_type_id': this.shop_type_id,
                        }

                        const response = await axios.post('/api/order/delete_order_type', post_data);

                        if (response.data.status) {
                            window.location.reload();
                        } else {
                            swal(response.data.message, "", "error");
                        }
                    }
                })
            }
        }
    }
</script>
