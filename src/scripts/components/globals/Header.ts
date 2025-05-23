import { ComponentElement } from '@stores/componentManager';

class Header extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.classList.add('bg-gray-100');
    }

    disconnectedCallback() {}
}

customElements.define('c-header', ComponentElement(Header, 'Header'));
