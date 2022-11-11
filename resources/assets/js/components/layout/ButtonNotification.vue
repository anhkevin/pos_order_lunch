<template>
    <div>
        <a v-on:click="btn_push()" href="javascript:void(0)" class="btn-theme btn-notification d-none d-sm-flex" v-bind:class="!this.isPushEnabled ? 'btn-notification-off' : 'btn-notification-on'">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.6001 4.3008V1.4C12.6001 0.627199 13.2273 0 14.0001 0C14.7715 0 15.4001 0.627199 15.4001 1.4V4.3008C17.4805 4.6004 19.4251 5.56639 20.9287 7.06999C22.7669 8.90819 23.8001 11.4016 23.8001 14V19.2696L24.9327 21.5348C25.4745 22.6198 25.4171 23.9078 24.7787 24.9396C24.1417 25.9714 23.0147 26.6 21.8023 26.6H15.4001C15.4001 27.3728 14.7715 28 14.0001 28C13.2273 28 12.6001 27.3728 12.6001 26.6H6.19791C4.98411 26.6 3.85714 25.9714 3.22014 24.9396C2.58174 23.9078 2.52433 22.6198 3.06753 21.5348L4.20011 19.2696V14C4.20011 11.4016 5.23194 8.90819 7.07013 7.06999C8.57513 5.56639 10.5183 4.6004 12.6001 4.3008ZM14.0001 6.99998C12.1423 6.99998 10.3629 7.73779 9.04973 9.05099C7.73653 10.3628 7.00011 12.1436 7.00011 14V19.6C7.00011 19.817 6.94833 20.0312 6.85173 20.2258C6.85173 20.2258 6.22871 21.4718 5.57072 22.7864C5.46292 23.0034 5.47412 23.2624 5.60152 23.4682C5.72892 23.674 5.95431 23.8 6.19791 23.8H21.8023C22.0445 23.8 22.2699 23.674 22.3973 23.4682C22.5247 23.2624 22.5359 23.0034 22.4281 22.7864C21.7701 21.4718 21.1471 20.2258 21.1471 20.2258C21.0505 20.0312 21.0001 19.817 21.0001 19.6V14C21.0001 12.1436 20.2623 10.3628 18.9491 9.05099C17.6359 7.73779 15.8565 6.99998 14.0001 6.99998Z" fill="#3E4954"></path>
            </svg>
            <span>Thông Báo</span>
        </a>
    </div>
</template>

<script>
    import firebase from "firebase/app";
    import "firebase/messaging";  

    export default {

        props: ['user_token'],

        data() {
            return {
                isPushEnabled: false,
                vapid_key: process.env.MIX_FIREBASE_VAPID_KEY,
                fcmToken: '',
                messaging: {},
            }
        },

        mounted () {
            this.initFCM()
            this.registerServiceWorker()
        },

        methods: {
            initFCM () {
                firebase.initializeApp({
                    apiKey: "AIzaSyA4CxNW1ylhDm4jsrSKJfRF2z3gAhGA0Mg",
                    authDomain: "pos-food.firebaseapp.com",
                    projectId: "pos-food",
                    storageBucket: "pos-food.appspot.com",
                    messagingSenderId: "580641154682",
                    appId: "1:580641154682:web:5ff1a859798de84de53dc3",
                    measurementId: "G-BL5XS3K0TG"
                });

                this.messaging = firebase.messaging();
            },
            refreshedToken(is_refresh = false) {
                this.messaging.getToken({vapidKey: this.vapid_key}).then((currentToken) => {
                    if (currentToken) {
                        if (is_refresh || this.user_token != currentToken) {
                            this.setTokenSentToServer(currentToken)
                        }
                    } else {
                        this.setTokenSentToServer(false)
                    }
                }).catch((err) => {
                    this.setTokenSentToServer(false)
                });
            },
            setTokenSentToServer (token) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/store-token',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        console.log('Token stored.');
                    },
                    error: function (error) {
                        console.log(error);
                    },
                });
            },

            registerServiceWorker () {
                if (!('serviceWorker' in navigator)) {
                    console.log('Service workers aren\'t supported in this browser.')
                    return
                }
                navigator.serviceWorker.register('/firebase-messaging-sw.js')
                    .then(() => this.initialiseServiceWorker())
            },

            initialiseServiceWorker () {

                // Are Notifications supported in the service worker?
                if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
                    console.log('Notifications aren\'t supported.')
                    return
                }

                if (Notification.permission === 'denied') {
                    console.log('The user has blocked notifications.')
                    return
                }

                if (!('PushManager' in window)) {
                    console.log('Push messaging isn\'t supported.')
                    return
                }

                navigator.serviceWorker.ready.then(registration => {
                    registration.pushManager.getSubscription()
                    .then(subscription => {

                        this.isPushEnabled = false

                        if (!subscription) {
                            return
                        }
                        this.refreshedToken()

                        this.isPushEnabled = true
                    })
                    .catch(e => {
                        console.log('Error during getSubscription()', e)
                    })
                })
            },

            btn_push() {
                // Are Notifications supported in the service worker?
                if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
                    swal('Notifications aren\'t supported.', "", "error");
                    return;
                }

                // Check the current Notification permission.
                // If its denied, it's a permanent block until the
                // user changes the permission
                if (Notification.permission === 'denied') {
                    swal('The user has blocked notifications.', "", "error");
                    return;
                }

                // Check if push messaging is supported
                if (!('PushManager' in window)) {
                    swal('Push messaging isn\'t supported.', "", "error");
                    return;
                }

                if (this.isPushEnabled) {
                    Swal.fire({
                        title: 'Bạn không muốn nhận thông báo ?',
                        text: "",
                        type: "warning", 
                        showCancelButton: !0, 
                        confirmButtonColor: "#DD6B55", 
                        confirmButtonText: "OK",
                        allowOutsideClick: false
                    }).then(async (result) => {
                        if (result.value) {
                            this.isPushEnabled = false
                            this.unsubscribe();
                        }
                    })
                } else {
                    Swal.fire({
                        title: 'Bạn muốn nhận thông báo ?',
                        text: "",
                        type: "warning", 
                        showCancelButton: !0, 
                        confirmButtonColor: "#DD6B55", 
                        confirmButtonText: "OK",
                        allowOutsideClick: false
                    }).then(async (result) => {
                        if (result.value) {
                            this.isPushEnabled = true
                            this.subscribe();
                        }
                    })
                }
            },
            unsubscribe() {
                navigator.serviceWorker.ready.then(registration => {
                    registration.pushManager.getSubscription().then(subscription => {
                    if (!subscription) {
                        this.isPushEnabled = false
                        return
                    }
                    subscription.unsubscribe().then(() => {
                        this.setTokenSentToServer(false)
                        this.isPushEnabled = false
                    }).catch(e => {
                        console.log('Unsubscription error: ', e)
                        this.isPushEnabled = false
                    })
                    }).catch(e => {
                        console.log('Error thrown while unsubscribing.', e)
                    })
                })
            },
            subscribe() {

                navigator.serviceWorker.ready.then(registration => {
                    const options = { userVisibleOnly: true }

                    if (this.vapid_key) {
                        options.applicationServerKey = this.urlBase64ToUint8Array(this.vapid_key)
                    }

                    registration.pushManager.subscribe(options)
                    .then(subscription => {
                        this.isPushEnabled = true
                        this.refreshedToken(true)
                    })
                    .catch(e => {
                        if (Notification.permission === 'denied') {
                            console.log('Permission for Notifications was denied')
                            this.isPushEnabled = false
                        } else {
                            console.log('Unable to subscribe to push.', e)
                            this.isPushEnabled = false
                        }
                    })
                })
            },

            urlBase64ToUint8Array (base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4)
                const base64 = (base64String + padding)
                    .replace(/-/g, '+')
                    .replace(/_/g, '/')
                const rawData = window.atob(base64)
                const outputArray = new Uint8Array(rawData.length)
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i)
                }
                return outputArray
            }
        }
    }
</script>