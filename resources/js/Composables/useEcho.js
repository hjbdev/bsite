import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    // wsHost: 'soketi', // testing
    wsHost:
        import.meta.env.VITE_PUSHER_HOST === "soketi"
            ? "localhost"
            : import.meta.env.VITE_PUSHER_HOST, // everything else
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    cluster: "eu",
    forceTLS: false,
    encrypted: true,
    disableStats: true,
    // enabledTransports: ["ws", "wss"],
});

export default function useEcho() {
    return echo;
}
