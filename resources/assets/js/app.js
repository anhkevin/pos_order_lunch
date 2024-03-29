
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('order-progress', require('./components/OrderProgress.vue').default);
// Vue.component('order-alert', require('./components/OrderAlert.vue').default);
// Vue.component('order-notifications', require('./components/OrderNotifications.vue').default);
Vue.component('transaction-history-component', require('./components/TransactionHistory.vue').default);
Vue.component('show-wallet-component', require('./components/ListWallet.vue').default);
Vue.component('form-collection', require('./components/FormCollection.vue').default);
Vue.component('order-create-component', require('./components/order/OrderCreate.vue').default);
Vue.component('avatar-component', require('./components/avatarAuto.vue').default);
Vue.component('pay-order-type', require('./components/order/PayOrderType.vue').default);
Vue.component('header-money', require('./components/layout/HeaderMoney.vue').default);
Vue.component('stepper-order', require('./components/order/StepperOrder.vue').default);
Vue.component('join-poll', require('./components/poll/JoinPoll.vue').default);
Vue.component('join-poll-login', require('./components/poll/JoinPollLogin.vue').default);
Vue.component('btn-edit-poll', require('./components/poll/EditPoll.vue').default);
Vue.component('admin-pay-order', require('./components/order/AdminPayOrder.vue').default);
Vue.component('btn-cancel-order', require('./components/order/ButtonCancelOrder.vue').default);
Vue.component('btn-copy-name', require('./components/layout/BtnCopyName.vue').default);
Vue.component('btn-cancel-join', require('./components/order/ButtonJoinOrder.vue').default);
Vue.component('btn-notification', require('./components/layout/ButtonNotification.vue').default);
Vue.component('shop_update_status', require('./components/shop/FormUpdateClose.vue').default);
Vue.component('add-order-type', require('./components/order/AddOrderType.vue').default);
Vue.component('profile-shop-type', require('./components/order/ProfileOrder.vue').default);

// const app = new Vue({
//     el: '#app',
//     mounted() {
//       window.Echo.channel('order_lunch_huyteam')
//       .listen('OrderStatusChanged', (e) => {
//         console.log('omgggg realtime bro')
//       });
//     }
// });
const app = new Vue({
    el: '#app'
});
