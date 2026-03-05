import type { UserConfig } from 'vite';

/**
 * Vite server configuration for DDEV.
 *
 * The custom environment variables are defined in `.ddev/config.yaml`
 * and can be customized `.ddev/config.local.yaml`.
 */

export default {
    server: {
        cors: true,
        host: '0.0.0.0',
        strictPort: true,
        port: parseInt(process.env.VITE_SERVER_HTTP_PORT),
        origin: `${process.env.PRIMARY_SITE_URL}:${process.env.VITE_SERVER_HTTPS_PORT}`,
    },
} as UserConfig;
