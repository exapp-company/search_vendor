// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
window.Pusher = Pusher;


window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'dmt1tcpfpmsv8jb2tkdf',
    wsHost: '127.0.0.1',
    wsPort: '8787' ?? 80,
    wssPort: '8787' ?? 443,
    forceTLS: 'https',
    enabledTransports: ['ws', 'wss'],
});
