// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

self.addEventListener('notificationclick', (event) => {
  clients.openWindow('https://lapos.online/add_order/food')
});

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyA4CxNW1ylhDm4jsrSKJfRF2z3gAhGA0Mg",
    authDomain: "pos-food.firebaseapp.com",
    projectId: "pos-food",
    storageBucket: "pos-food.appspot.com",
    messagingSenderId: "580641154682",
    appId: "1:580641154682:web:5ff1a859798de84de53dc3",
    measurementId: "G-BL5XS3K0TG"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

/*messaging.onBackgroundMessage((payload) => {
    // Customize notification here
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
      body: payload.notification.body,
      icon: '/firebase-logo.png'
    };
  
    self.registration.showNotification(notificationTitle, notificationOptions);
});*/