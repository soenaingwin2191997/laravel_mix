<script src="{{asset('assets/global/js/firebase/firebase-8.3.2.js')}}"></script>

<script>

    "use strict";

    var permission = null;
    var authenticated = '{{ auth()->user() ? true : false }}';
    var pushNotify = @json($general->pn);
    var firebaseConfig = @json($general->push_config);

    function pushNotifyAction(){
        permission = Notification.permission;

        if(!('Notification' in window)){
            notify('info', 'Push notifications not available in your browser. Try Chromium.')
        }
    }

    //If enable push notification from admin panel
    if(pushNotify == 1){
        pushNotifyAction();
    }

    // var firebaseConfig = {"apiKey":"AIzaSyDBFcpfEJW5ngWqJRQQQvzO_K7otHQxJTI","authDomain":"signal-lab-15d48.firebaseapp.com","projectId":"signal-lab-15d48","storageBucket":"signal-lab-15d48.appspot.com","messagingSenderId":"1055530026537","appId":"1:1055530026537:web:d2120f6794f20b69aebe38","measurementId":"G-HHN4CXM949","serverKey":"AAAA9cJ-Bik:APA91bGd21EtVi-gL71u7xPOYlo-YI3ADjrL6tO2KdFcDAuCj_CTXwERieMz7iPy-3b823HBt4bhrG2pWpxtIbyc40W1zkMd3jUiP9J7D6_VMzyuqrB19QyFJ4zgMPehuOV6tfrZC0m4"};

    //When users allow browser notification
    if(permission != 'denied' && firebaseConfig){

        //Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        navigator.serviceWorker.register("{{ asset('assets/global/js/firebase/firebase-messaging-sw.js') }}")

        .then((registration) => {
            messaging.useServiceWorker(registration);

            function initFirebaseMessagingRegistration() {
                messaging
                .requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function (token){
                    $.ajax({
                        url: '{{ route("store.device.token") }}',
                        type: 'POST',
                        data: {
                            token: token,
                            '_token': "{{ csrf_token() }}"
                        },
                        success: function(response){
                            console.log(response);
                        },
                        error: function (err) {
                            console.log('User Chat Token Error'+ err);
                        },
                    });
                }).catch(function (error){
                    console.log(error);
                });
            }

            messaging.onMessage(function (payload){
                const title = payload.notification.title;
                const options = {
                    body: payload.notification.body,
                    icon: payload.notification.icon,
                    click_action:payload.notification.click_action,
                    vibrate: [200, 100, 200]
                };
                new Notification(title, options);
            });

            //For authenticated users
            if(authenticated){
                initFirebaseMessagingRegistration();
            }

        });

    }

</script>
