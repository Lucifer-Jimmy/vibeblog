

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('typewriter', () => ({
    text: '分享技术、记录生活、探索世界',
    display: '',
    charIndex: 0,

    start() {
        this.tick();
    },

    tick() {
        if (this.charIndex < this.text.length) {
            this.display = this.text.substring(0, this.charIndex + 1);
            this.charIndex++;
            setTimeout(() => this.tick(), 80);
        }
    },
}));

Alpine.start();
