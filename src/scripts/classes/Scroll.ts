import { $scroll } from '@stores/scroll';
import { isAnimationsReduced } from '@scripts/stores/animations';

import LocomotiveScroll, {
    type ILenisScrollToOptions,
    type lenisTargetScrollTo,
} from 'locomotive-scroll';

export class Scroll {
    static locomotiveScroll: LocomotiveScroll | null = null;
    static isInitialized: boolean = false;
    static isSmooth: boolean = !isAnimationsReduced.get();

    // =============================================================================
    // Lifecycle
    // =============================================================================
    static init() {
        // Handle animation stop/active
        isAnimationsReduced.subscribe((isReduced) => {
            this.isSmooth = !isReduced;

            if (!this.isInitialized) {
                this.initScroll();
            } else {
                this.destroy();
                requestAnimationFrame(() => {
                    this.initScroll();
                });
            }
        });
    }

    static initScroll() {
        this.locomotiveScroll = new LocomotiveScroll({
            lenisOptions: {
                smoothWheel: this.isSmooth,
            },
            scrollCallback({ scroll, limit, velocity, direction, progress }) {
                $scroll.set({
                    scroll,
                    limit,
                    velocity,
                    direction,
                    progress,
                });
            },
        });

        this.isInitialized = true;
    }

    static destroy() {
        this.locomotiveScroll?.destroy();
    }

    // =============================================================================
    // Methods
    // =============================================================================
    static start() {
        this.locomotiveScroll?.start();
    }

    static stop() {
        this.locomotiveScroll?.stop();
    }

    static addScrollElements(container: HTMLElement) {
        this.locomotiveScroll?.addScrollElements(container);
    }

    static removeScrollElements(container: HTMLElement) {
        this.locomotiveScroll?.removeScrollElements(container);
    }

    static scrollTo(target: lenisTargetScrollTo, options?: ILenisScrollToOptions) {
        this.locomotiveScroll?.scrollTo(target, options);
    }
}
