# Development Tips

## Locomotive Scroll specificities

Locomotive Scroll high-jacks the native browser scroll which often creates issues with third-party elements.
Here are some examples on how to fix some of these issues:

### The user cannot scroll in the cookie-consent's preferences modal

To fix this issue, you need to add a custom rule to prevent Locomotive Scroll from altering the scroll behavior of a given element.

```ts
// src/scripts/classes/Scroll.ts

export class Scroll {
    static initScroll() {
        this.locomotiveScroll = new LocomotiveScroll({
            lenisOptions: {
                // For Vanilla Cookie Consent
                prevent: (node) => node.getAttribute('id') === 'cc-main',

                // For Cookie Yes
                prevent: (node) =>
                    node.getAttribute('class').includes('cky-modal') ||
                    node.getAttribute('class').includes('cky-consent-container'),
            },
        });
    }
}
```
