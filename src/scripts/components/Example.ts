import { ComponentElement } from '@stores/componentManager';

class Example extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {

    }

    disconnectedCallback() {

    }
}

customElements.define('c-example', ComponentElement(Example, 'Example'));
