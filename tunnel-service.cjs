const localtunnel = require('localtunnel');

const PORT = 8000;
const SUBDOMAIN = process.env.LT_SUBDOMAIN || 'silly-phones-think';

// Keep event loop alive indefinitely
setInterval(() => {}, 30000);

process.on('uncaughtException', (err) => {
    console.error('[Localtunnel] Uncaught exception:', err.message);
    setTimeout(startTunnel, 3000);
});

process.on('unhandledRejection', (reason) => {
    console.error('[Localtunnel] Unhandled rejection:', reason);
    setTimeout(startTunnel, 3000);
});

let currentTunnel = null;

async function startTunnel() {
    if (currentTunnel) {
        try { currentTunnel.close(); } catch(e) {}
        currentTunnel = null;
    }

    console.log(`[Localtunnel] Connecting to port ${PORT} with subdomain '${SUBDOMAIN}'...`);
    try {
        currentTunnel = await localtunnel({ port: PORT, subdomain: SUBDOMAIN });
        console.log(`[Localtunnel] Tunnel established: ${currentTunnel.url}`);

        currentTunnel.on('close', () => {
            console.warn('[Localtunnel] Tunnel closed. Reconnecting in 3 seconds...');
            setTimeout(startTunnel, 3000);
        });

        currentTunnel.on('error', (err) => {
            console.error('[Localtunnel] Tunnel error:', err.message);
            setTimeout(startTunnel, 3000);
        });
    } catch (err) {
        console.error('[Localtunnel] Connection failed:', err.message);
        setTimeout(startTunnel, 5000);
    }
}

startTunnel();
