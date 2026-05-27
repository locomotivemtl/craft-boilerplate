import type { UserConfig } from 'vite';

/**
 * Vite server configuration for DDEV.
 *
 * The custom environment variables are defined in `.ddev/config.yaml`
 * and can be customized `.ddev/config.local.yaml`.
 */

export default {
    server: {
        // Special address that respond to all network requests
        host: '0.0.0.0',
        // Use a strict port because we have to hard code this in vite.php
        strictPort: true,
        // This is the port running "inside" the Web container
        // It's the same as continer_port in .ddev/config.yaml
        port: parseInt(process.env.VITE_SERVER_PRIVATE_PORT),
        // Setting a specific origin ensures that your fonts & images load
        // correctly. Assumes you're accessing the front-end over https
        origin: `${process.env.PRIMARY_SITE_URL}:${process.env.VITE_SERVER_PUBLIC_PORT}`,

        allowedHosts: true,
        cors: true,
    },
} as UserConfig;
