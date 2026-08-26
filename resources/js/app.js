import Alpine from 'alpinejs';
import { iniciarConstrutor } from './construtor';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    iniciarConstrutor();
});