

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('typewriter', () => ({
    texts: ['分享技术、记录生活、探索世界', '用代码书写故事', '每一篇都值得被记录'],
    display: '',
    textIndex: 0,
    charIndex: 0,
    isDeleting: false,
    speed: 150,

    start() {
        this.tick();
    },

    tick() {
        const current = this.texts[this.textIndex];

        if (!this.isDeleting) {
            this.display = current.substring(0, this.charIndex + 1);
            this.charIndex++;

            if (this.charIndex === current.length) {
                this.speed = 2000; // pause at end
                this.isDeleting = true;
            } else {
                this.speed = 150;
            }
        } else {
            this.display = current.substring(0, this.charIndex - 1);
            this.charIndex--;

            if (this.charIndex === 0) {
                this.isDeleting = false;
                this.textIndex = (this.textIndex + 1) % this.texts.length;
                this.speed = 500; // pause before next word
            } else {
                this.speed = 80;
            }
        }

        setTimeout(() => this.tick(), this.speed);
    },
}));

Alpine.start();
